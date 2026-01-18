<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Component\Validation;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use PedhotDev\NepotismFree\Core\Registry;
use PedhotDev\NepotismFree\Exception\DefinitionException;
use PedhotDev\NepotismFree\Exception\NotFoundException;

/**
 * Performs static analysis of the container's dependency graph.
 */
class Validator
{
    private array $checked = [];
    private array $path = [];

    public function __construct(private Registry $registry) {}

    /**
     * Validate all explicitly bound services and their dependencies.
     */
    public function validate(array $serviceIds): void
    {
        foreach ($serviceIds as $id) {
            $this->validateService($id);
        }
    }

    private function validateService(string $id): void
    {
        if (isset($this->checked[$id])) return;

        $binding = $this->registry->getBinding($id);
        $concrete = ($binding && is_string($binding)) ? $binding : $id;

        if (isset($this->path[$concrete])) {
            // Circular dependency detection (static)
            // Resolver also handles this at runtime, but it's good to catch early.
            return; 
        }

        $this->path[$concrete] = true;

        try {
            $this->inspectConcrete($concrete);
        } finally {
            unset($this->path[$concrete]);
            $this->checked[$id] = true;
        }
    }

    private function inspectConcrete(string $class): void
    {
        if (!class_exists($class)) {
            // If it's an interface and no binding exists, it's an error
            throw new NotFoundException(sprintf("Binding or class '%s' not found.", $class));
        }

        try {
            $reflector = new ReflectionClass($class);
        } catch (ReflectionException $e) {
             throw new NotFoundException(sprintf("Class '%s' not found.", $class), 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            return; // Abstract/Interface without binding is allowed in registry? No, it should be bound.
        }

        $constructor = $reflector->getConstructor();
        if ($constructor === null) return;

        foreach ($constructor->getParameters() as $parameter) {
            $paramName = $parameter->getName();
            
            // Check explicit argument binding
            if ($this->registry->hasArgument($class, $paramName)) {
                continue;
            }

            $type = $parameter->getType();
            if (!$type) {
                throw DefinitionException::untypedParameter($class, $paramName);
            }

            if (!$type instanceof ReflectionNamedType) {
                 throw new DefinitionException(sprintf(
                     "Ambiguous type (union/intersection) in class '%s' for '$%s'.",
                     $class, $paramName
                 ));
            }

            if ($type->isBuiltin()) {
                if (!$parameter->isDefaultValueAvailable()) {
                    throw DefinitionException::unresolvableBuiltin($class, $paramName, $type->getName());
                }
                continue;
            }

            // Recursive check for dependency
            $dependencyId = $type->getName();
            
            // Contextual binding check
            if ($this->registry->getContextualBinding($dependencyId, $class)) {
                continue; // Assume contextual binding is valid or check it later
            }

            $this->validateService($dependencyId);
        }
    }
}
