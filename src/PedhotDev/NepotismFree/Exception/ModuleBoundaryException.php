<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Exception;

/**
 * Thrown when an internal module service is accessed from the outside.
 */
class ModuleBoundaryException extends ContainerException
{
    public static function internalServiceAccess(string $id, string $serviceModule, ?string $consumerModule = null): self
    {
        $message = sprintf("Service '%s' is internal to module '%s' and cannot be accessed from the outside.", $id, $serviceModule);
        if ($consumerModule !== null) {
            $message = sprintf("Service '%s' (internal to module '%s') cannot be accessed by module '%s'.", $id, $serviceModule, $consumerModule);
        }
        return new self($message);
    }
}
