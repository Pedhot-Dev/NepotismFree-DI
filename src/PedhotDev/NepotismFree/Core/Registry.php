<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Core;

use Closure;
use PedhotDev\NepotismFree\Exception\DefinitionException;

/**
 * Registry definition storage.
 * Holds all bindings and configurations before container compilation/locking.
 */
class Registry
{
    /** @var array<string, string|Closure> */
    private array $bindings = [];

    /** @var array<string, array<string, mixed>> */
    private array $arguments = [];

    /** @var array<string, \PedhotDev\NepotismFree\Contract\Scope> */
    private array $scopes = [];

    /** @var array<string, array<string, string|Closure>> consumer => [interface => implementation] */
    private array $contextualBindings = [];

    /** @var array<string, string[]> tag => [service_ids] */
    private array $tags = [];

    /** @var array<string, object> class => instance */
    private array $parameterObjects = [];

    /** @var array<string, string> serviceId => moduleName */
    private array $bindingModules = [];

    public function bind(string $id, string|Closure $implementation, ?string $module = null): void
    {
        $this->bindings[$id] = $implementation;
        if ($module) {
            $this->bindingModules[$id] = $module;
        }
    }

    public function bindContext(string $interface, string $implementation, string $consumer): void
    {
        $this->contextualBindings[$consumer][$interface] = $implementation;
    }

    public function tag(string $tag, string $serviceId): void
    {
        $this->tags[$tag][] = $serviceId;
    }

    public function bindParameterObject(string $class, object $instance): void
    {
        $this->parameterObjects[$class] = $instance;
    }

    public function bindArgument(string $class, string $paramName, mixed $value): void
    {
        $this->arguments[$class][$paramName] = $value;
    }

    public function setScope(string $class, \PedhotDev\NepotismFree\Contract\Scope $scope): void
    {
        $this->scopes[$class] = $scope;
    }

    public function setSingleton(string $class, bool $isSingleton): void
    {
        $this->setScope($class, $isSingleton ? \PedhotDev\NepotismFree\Contract\Scope::PROCESS : \PedhotDev\NepotismFree\Contract\Scope::PROTOTYPE);
    }

    public function getBinding(string $id): string|Closure|null
    {
        return $this->bindings[$id] ?? null;
    }

    public function getContextualBinding(string $interface, string $consumer): string|Closure|null
    {
        return $this->contextualBindings[$consumer][$interface] ?? null;
    }

    /**
     * @return string[]
     */
    public function getTagged(string $tag): array
    {
        return $this->tags[$tag] ?? [];
    }

    /**
     * @return array<string, string[]>
     */
    public function getTagsMap(): array
    {
        return $this->tags;
    }

    public function getParameterObject(string $class): ?object
    {
        return $this->parameterObjects[$class] ?? null;
    }

    public function getArgument(string $class, string $paramName): mixed
    {
        if (isset($this->arguments[$class]) && array_key_exists($paramName, $this->arguments[$class])) {
            return $this->arguments[$class][$paramName];
        }
        return null;
    }

    public function hasArgument(string $class, string $paramName): bool
    {
        return isset($this->arguments[$class]) && array_key_exists($paramName, $this->arguments[$class]);
    }

    public function getScope(string $id): \PedhotDev\NepotismFree\Contract\Scope
    {
        return $this->scopes[$id] ?? \PedhotDev\NepotismFree\Contract\Scope::PROTOTYPE;
    }

    public function isSingleton(string $id): bool
    {
        $scope = $this->getScope($id);
        return $scope === \PedhotDev\NepotismFree\Contract\Scope::PROCESS || $scope === \PedhotDev\NepotismFree\Contract\Scope::TICK;
    }

    /**
     * @return string[]
     */
    public function getServiceIds(): array
    {
        return array_keys($this->bindings);
    }

    public function getModule(string $id): ?string
    {
        return $this->bindingModules[$id] ?? null;
    }

    /**
     * @return array<string, string|Closure>
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * @return array<string, \PedhotDev\NepotismFree\Contract\Scope>
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }
}
