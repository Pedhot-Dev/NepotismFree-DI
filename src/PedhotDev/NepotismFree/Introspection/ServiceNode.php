<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Introspection;

/**
 * Represents a single node in the dependency graph.
 * Mutable by design only during graph construction, then treated as read-only.
 */
class ServiceNode
{
    /** @var string[] */
    public array $dependencies = [];

    public function __construct(
        public readonly string $id,
        public readonly string $type, // 'singleton', 'prototype', 'factory'
        public readonly bool $isResolved,
        public readonly ?string $concrete = null,
    ) {}

    public function addDependency(string $serviceId): void
    {
        $this->dependencies[] = $serviceId;
    }
}
