<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Introspection;

/**
 * Represents the static dependency structure of the container.
 * This object is a snapshot in time.
 */
class DependencyGraph
{
    /**
     * @param array<string, ServiceNode> $nodes
     */
    public function __construct(
        private array $nodes
    ) {}

    /**
     * @return array<string, ServiceNode>
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    public function getNode(string $id): ?ServiceNode
    {
        return $this->nodes[$id] ?? null;
    }

    public function hasCycle(string $id): bool
    {
        // Simple DFS cycle detection
        return $this->detectCycle($id, []);
    }

    private function detectCycle(string $current, array $path): bool
    {
        if (isset($path[$current])) {
            return true;
        }

        $node = $this->getNode($current);
        if (!$node) {
            return false;
        }

        $path[$current] = true;
        foreach ($node->dependencies as $dep) {
            if ($this->detectCycle($dep, $path)) {
                return true;
            }
        }
        
        return false;
    }
}
