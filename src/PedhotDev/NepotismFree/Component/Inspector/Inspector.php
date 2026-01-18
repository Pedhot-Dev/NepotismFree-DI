<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Component\Inspector;

use PedhotDev\NepotismFree\Core\Registry;

/**
 * Provides tools to inspect the container's state and graph.
 */
class Inspector
{
    public function __construct(private Registry $registry) {}

    public function getDependencyTree(string $id, int $depth = 0): string
    {
        $indent = str_repeat('  ', $depth);
        $output = "{$indent}- {$id}\n";
        
        // This would require a more complex traversal logic similar to Validator.
        return $output;
    }
}
