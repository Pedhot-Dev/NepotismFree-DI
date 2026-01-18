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

    /** @var array<string, bool> True = Singleton, False = Prototype */
    private array $singletons = [];

    /** @var array<string, array<string, string|Closure>> consumer => [interface => implementation] */
    private array $contextualBindings = [];

    /** @var array<string, string[]> tag => [service_ids] */
    private array $tags = [];

    /** @var array<string, object> class => instance */
    private array $parameterObjects = [];

    public function bind(string $id, string|Closure $implementation): void
    {
        $this->bindings[$id] = $implementation;
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

    public function setSingleton(string $class, bool $isSingleton): void
    {
        $this->singletons[$class] = $isSingleton;
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

    public function isSingleton(string $id): bool
    {
        return $this->singletons[$id] ?? false;
    }

    /**
     * @return string[]
     */
    public function getServiceIds(): array
    {
        return array_keys($this->bindings);
    }
}
