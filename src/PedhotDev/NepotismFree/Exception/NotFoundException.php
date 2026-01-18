<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Exception;

use PedhotDev\NepotismFree\Contract\Exception\NotFoundExceptionInterface;

/**
 * Thrown when a requested service or entry is not found in the container.
 */
class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
    public static function entryNotFound(string $id): self
    {
        return new self(sprintf(
            "Service or entry '%s' not found. Explicit binding required in NepotismFree DI.",
            $id
        ));
    }
}
