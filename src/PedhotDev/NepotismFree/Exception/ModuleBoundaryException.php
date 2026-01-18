<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Exception;

/**
 * Thrown when an internal module service is accessed from the outside.
 */
class ModuleBoundaryException extends ContainerException
{
    public static function internalServiceAccess(string $id, string $moduleClass): self
    {
        return new self(sprintf(
            "Service '%s' is internal to module '%s' and cannot be accessed from the outside.",
            $id,
            $moduleClass
        ));
    }
}
