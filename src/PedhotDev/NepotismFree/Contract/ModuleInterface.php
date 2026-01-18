<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Contract;

use PedhotDev\NepotismFree\Builder\ContainerBuilder;

interface ModuleInterface
{
    /**
     * Define the module's services.
     */
    public function configure(ModuleConfiguratorInterface $configurator): void;

    /**
     * List of service IDs exposed to the outside.
     * All other services defined in this module are INTERNAL.
     * 
     * @return string[]
     */
    public function getExposedServices(): array;
}
