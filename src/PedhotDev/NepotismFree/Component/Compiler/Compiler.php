<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Component\Compiler;

use ReflectionClass;
use ReflectionNamedType;
use PedhotDev\NepotismFree\Contract\ContainerInterface;
use PedhotDev\NepotismFree\Contract\Scope;
use PedhotDev\NepotismFree\Core\Container;
use PedhotDev\NepotismFree\Core\ScopedContainer;
use PedhotDev\NepotismFree\Core\ScopeManager;
use PedhotDev\NepotismFree\Core\ModuleAccessPolicy;
use PedhotDev\NepotismFree\Core\Registry;
use PedhotDev\NepotismFree\Exception\DefinitionException;
use PedhotDev\NepotismFree\Exception\NotFoundException;

/**
 * Generates a static PHP class for the container.
 */
class Compiler
{
    public function __construct(
        private Registry $registry,
        private ?ModuleAccessPolicy $accessPolicy = null
    ) {}

    public function compile(string $path, string $className): void
    {
        $code = "<?php\n\ndeclare(strict_types=1);\n\n";
        $code .= "namespace PedhotDev\NepotismFree\Compiled;\n\n";
        $code .= "use PedhotDev\NepotismFree\Contract\ContainerInterface;\n";
        $code .= "use PedhotDev\NepotismFree\Exception\NotFoundException;\n";
        $code .= "use PedhotDev\NepotismFree\Exception\DefinitionException;\n";
        $code .= "use PedhotDev\NepotismFree\Contract\Scope;\n\n"; // Added use statement for Scope
        $code .= "class {$className} implements ContainerInterface\n{\n";
        $code .= "    private \\stdClass \$instances;\n";
        $code .= "    private array \$scopedInstances = [];\n\n";
        $code .= "    public function __construct(private array \$factories = []) {\n";
        $code .= "        \$this->instances = new \\stdClass();\n";
        $code .= "        \$this->instances->storage = [];\n";
        $code .= "    }\n\n";
        
        $code .= "    public function createScope(Scope \$scope): self\n    {\n";
        $code .= "        \$child = clone \$this;\n";
        $code .= "        \$child->scopedInstances = [];\n";
        $code .= "        return \$child;\n";
        $code .= "    }\n\n";

        $code .= "    public function get(string \$id): mixed\n    {\n";
        $code .= "        return match (\$id) {\n";
        
        foreach ($this->registry->getServiceIds() as $id) {
            // ONLY expose services that are allowed by access policy
            if ($this->accessPolicy && !$this->accessPolicy->canAccess($id)) {
                continue;
            }

            $methodName = $this->getMethodName($id);
            $code .= "            '{$id}' => \$this->{$methodName}(),\n";
        }
        
        $code .= "            default => throw new NotFoundException(\"Service '\$id' not found in compiled container.\")\n";
        $code .= "        };\n";
        $code .= "    }\n\n";

        foreach ($this->registry->getServiceIds() as $id) {
            $code .= $this->generateResolveMethod($id);
        }
        
        $code .= "    public function has(string \$id): bool\n    {\n";
        $code .= "        return in_array(\$id, " . var_export($this->registry->getServiceIds(), true) . ", true);\n";
        $code .= "    }\n\n";

        $code .= "    public function getTagged(string \$tag): iterable\n    {\n";
        $code .= "        \$tags = " . $this->getTags() . ";\n";
        $code .= "        foreach (\$tags[\$tag] ?? [] as \$id) {\n";
        $code .= "            yield \$this->get(\$id);\n";
        $code .= "        }\n";
        $code .= "    }\n";
        $code .= "}\n";

        file_put_contents($path, $code);
    }

    private function generateResolveMethod(string $id): string
    {
        $methodName = $this->getMethodName($id);
        $scope = $this->registry->getScope($id);
        
        $code = "    private function {$methodName}(): mixed\n    {\n";
        
        if ($scope === Scope::PROCESS) {
            $code .= "        if (isset(\$this->instances->storage['{$id}'])) return \$this->instances->storage['{$id}'];\n";
            $instantiator = $this->getInstantiator($id);
            $code .= "        return \$this->instances->storage['{$id}'] = {$instantiator};\n";
        } elseif ($scope === Scope::TICK) {
            $code .= "        if (isset(\$this->scopedInstances['{$id}'])) return \$this->scopedInstances['{$id}'];\n";
            $instantiator = $this->getInstantiator($id);
            $code .= "        return \$this->scopedInstances['{$id}'] = {$instantiator};\n";
        } else {
            // PROTOTYPE
            $instantiator = $this->getInstantiator($id);
            $code .= "        return {$instantiator};\n";
        }
        
        $code .= "    }\n\n";
        return $code;
    }

    private function getInstantiator(string $id): string
    {
        $binding = $this->registry->getBinding($id);
        if ($binding instanceof \Closure) {
            return "\$this->factories['{$id}'](\$this)";
        }

        $concrete = is_string($binding) ? $binding : $id;
        
        if (!class_exists($concrete)) {
            return "throw new NotFoundException(\"Class '{$concrete}' not found.\")";
        }

        $reflector = new ReflectionClass($concrete);
        $constructor = $reflector->getConstructor();
        
        if (!$constructor) {
            return "new \\{$concrete}()";
        }

        $args = [];
        foreach ($constructor->getParameters() as $param) {
            $paramName = $param->getName();
            
            // 1. Argument binding
            if ($this->registry->hasArgument($concrete, $paramName)) {
                $args[] = var_export($this->registry->getArgument($concrete, $paramName), true);
                continue;
            }

            $type = $param->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                if ($param->isDefaultValueAvailable()) {
                    $args[] = var_export($param->getDefaultValue(), true);
                    continue;
                }
                // This should have been caught by validator, but for safety in compiler:
                return "throw new \RuntimeException(\"Cannot resolve parameter '{$paramName}' in '{$concrete}' at compile time.\")";
            }

            $depId = $type->getName();
            
            // Runtime Rule: Forbidden Injections
            if (
                $depId === Container::class || 
                $depId === ContainerInterface::class || 
                $depId === ScopedContainer::class || 
                $depId === ScopeManager::class
            ) {
                 throw DefinitionException::containerInjectionForbidden();
            }

            // Runtime Rule: Scope Safety (PROCESS -> TICK)
            if ($this->registry->getScope($concrete) === Scope::PROCESS && $this->registry->getScope($depId) === Scope::TICK) {
                throw DefinitionException::unsafeScopeInjection($concrete, $depId);
            }

            // 2. Contextual binding
            if ($contextual = $this->registry->getContextualBinding($depId, $concrete)) {
                if ($contextual instanceof \Closure) {
                    // We'd need to pass this factory too, let's simplify for now or handle it
                    $args[] = "\$this->factories['context:{$id}:{$paramName}'](\$this)";
                } else {
                    $args[] = "\$this->get('{$contextual}')";
                }
            } else {
                $args[] = "\$this->get('{$depId}')";
            }
        }

        return "new \\{$concrete}(" . implode(', ', $args) . ")";
    }

    private function getMethodName(string $id): string
    {
        return 'resolve_' . str_replace(['\\', '.'], '_', $id);
    }

    private function getTags(): string
    {
        $tagsMap = $this->registry->getTagsMap();
        $filteredMap = [];
        
        foreach ($tagsMap as $tag => $ids) {
            $filteredMap[$tag] = [];
            foreach ($ids as $id) {
                // ONLY include tags for services that are allowed by access policy
                if (!$this->accessPolicy || $this->accessPolicy->canAccess($id)) {
                    $filteredMap[$tag][] = $id;
                }
            }
        }
        
        return var_export($filteredMap, true);
    }
}
