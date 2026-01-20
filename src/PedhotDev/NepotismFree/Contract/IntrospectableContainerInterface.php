<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Contract;

use PedhotDev\NepotismFree\Introspection\DependencyGraph;

/**
 * Contract for containers that expose their internal state for tooling.
 * 
 * DESIGN GOAL: 
 * This interface exposes RAW structural data. It is intended for:
 * - Static analysis tools
 * - IDE plugins
 * - Debuggers / Profilers
 * 
 * It is NOT intended for runtime logic or application code.
 * Accessing these methods is expected to be slower than standard Container::get()
 * and may involve object allocation.
 */
interface IntrospectableContainerInterface
{
    /**
     * Returns the raw list of all registered service IDs and their definitions.
     * 
     * @return array<string, mixed> Map of ServiceID => Definition (Class string, Closure, or Instance)
     */
    public function getDefinitions(): array;

    /**
     * Returns the list of service IDs that have been instantiated and are currently cached.
     * 
     * @return string[]
     */
    public function getResolvedIds(): array;

    /**
     * Generates a dependency graph for the current state of the container.
     * 
     * This operation is lazy and potentially expensive.
     * 
     * @return DependencyGraph
     */
    public function getDependencyGraph(): DependencyGraph;
}
