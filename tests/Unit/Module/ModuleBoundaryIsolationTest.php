<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Module;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Contract\ModuleInterface;
use PedhotDev\NepotismFree\Contract\ModuleConfiguratorInterface;

class ModuleBoundaryIsolationTest extends TestCase
{
    public function testInternalServiceIsResolvableByServicesInSameModule(): void
    {
        $module = new class implements ModuleInterface {
            public function configure(ModuleConfiguratorInterface $configurator): void {
                $configurator->bind(InternalDep::class, InternalDep::class);
                $configurator->bind(PublicService::class, PublicService::class);
            }
            public function getExposedServices(): array { return [PublicService::class]; }
        };

        $builder = new ContainerBuilder();
        $builder->addModule($module);
        $container = $builder->build();

        $instance = $container->get(PublicService::class);
        $this->assertInstanceOf(InternalDep::class, $instance->dep);
    }
}

class InternalDep {}
class PublicService { public function __construct(public InternalDep $dep) {} }
