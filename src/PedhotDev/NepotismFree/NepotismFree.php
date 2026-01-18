<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree;

use PedhotDev\NepotismFree\Builder\ContainerBuilder;

/**
 * Entry point for the NepotismFree DI library.
 */
final class NepotismFree
{
    /**
     * Creates a new ContainerBuilder instance.
     * This is the recommended way to start building your container.
     */
    public static function createBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }
}
