<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Core;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ReflectionNamedType;
use PedhotDev\NepotismFree\Contract\ContainerInterface;
use PedhotDev\NepotismFree\Exception\CircularDependencyException;
use PedhotDev\NepotismFree\Exception\DefinitionException;
use PedhotDev\NepotismFree\Exception\NotFoundException;

/**
 * Resolves classes and dependencies using Reflection.
 */
class Resolver
{
    private array $building = [];
    private array $reflectionCache = [];

    public function __construct(
        private Registry $registry,
        private ContainerInterface $container
    ) {}

    public function resolve(string $id): mixed
    {
        // 0. Forbidden: Injection of the Container itself (Service Locator risk)
        if ($id === Container::class || $id === ContainerInterface::class) {
             throw DefinitionException::containerInjectionForbidden();
        }

        // 0. Check for Parameter Object
        if ($instance = $this->registry->getParameterObject($id)) {
            return $instance;
        }

        // 1. Check for Factory Binding
        $binding = $this->registry->getBinding($id);

        if ($binding instanceof Closure) {
            return $binding($this->container);
        }

        $concrete = $binding && is_string($binding) ? $binding : $id;

        // Circular dependency check
        if (isset($this->building[$concrete])) {
            throw CircularDependencyException::create($concrete, array_keys($this->building));
        }

        $this->building[$concrete] = true;

        try {
            return $this->build($concrete);
        } finally {
            unset($this->building[$concrete]);
        }
    }

    private function build(string $class): object
    {
        try {
            $reflector = $this->getReflector($class);
        } catch (ReflectionException $e) {
            throw new NotFoundException(sprintf("Class '%s' not found.", $class), 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new NotFoundException(sprintf("Class '%s' is not instantiable.", $class));
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $dependencies[] = $this->resolveParameter($class, $parameter);
        }

        return $reflector->newInstanceArgs($dependencies);
    }

    private function resolveParameter(string $class, ReflectionParameter $parameter): mixed
    {
        $paramName = $parameter->getName();

        // 1. Check for explicit scalar/argument binding
        if ($this->registry->hasArgument($class, $paramName)) {
            return $this->registry->getArgument($class, $paramName);
        }

        $type = $parameter->getType();

        // 2. Fail if no type is documented
        if (!$type) {
             throw DefinitionException::untypedParameter($class, $paramName);
        }

        // 3. Handle Union/Intersection types
        if (!$type instanceof ReflectionNamedType) {
             throw new DefinitionException(sprintf(
                 "Parameter '$%s' in class '%s' uses a complex type (union/intersection). Ambiguous injection forbidden. Explicitly bind this argument.",
                 $paramName,
                 $class
             ));
        }

        if ($type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            throw DefinitionException::unresolvableBuiltin($class, $paramName, $type->getName());
        }

        // 4. Resolve Class Dependency
        $dependencyId = $type->getName();

        // 5. Check for Contextual Binding (V2 Feature)
        if ($contextual = $this->registry->getContextualBinding($dependencyId, $class)) {
            // Contextual binding can be a concrete class name or a closure
            if ($contextual instanceof Closure) {
                return $contextual($this->container);
            }
            return $this->container->get($contextual, true);
        }
        
        try {
            return $this->container->get($dependencyId, true);
        } catch (NotFoundException $e) {
            if ($type->allowsNull()) {
                return null;
            }
            throw $e;
        }
    }

    private function getReflector(string $class): ReflectionClass
    {
        if (!isset($this->reflectionCache[$class])) {
            $this->reflectionCache[$class] = new ReflectionClass($class);
        }
        return $this->reflectionCache[$class];
    }
}
