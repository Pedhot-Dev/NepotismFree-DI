<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Exception;

/**
 * Thrown when a circular dependency is detected during resolution.
 */
class CircularDependencyException extends ContainerException
{
    /**
     * @param string[] $path
     */
    public static function create(string $currentId, array $path): self
    {
        $pathString = implode(' -> ', $path) . ' -> ' . $currentId;
        return new self(sprintf(
            "Circular dependency detected: %s",
            $pathString
        ));
    }
}
