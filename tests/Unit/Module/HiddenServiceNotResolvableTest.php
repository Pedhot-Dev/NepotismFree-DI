<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Module;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Contract\ModuleInterface;
use PedhotDev\NepotismFree\Contract\ModuleConfiguratorInterface;
use PedhotDev\NepotismFree\Exception\ModuleBoundaryException;

class HiddenServiceNotResolvableTest extends TestCase
{
    public function testInternalServiceCannotBeResolvedDirectly(): void
    {
        $module = new class implements ModuleInterface {
            public function configure(ModuleConfiguratorInterface $configurator): void {
                $configurator->bind('Internal', \stdClass::class);
            }
            public function getExposedServices(): array { return []; }
        };

        $builder = new ContainerBuilder();
        $builder->addModule($module);
        $container = $builder->build();

        $this->expectException(ModuleBoundaryException::class);
        $container->get('Internal');
    }
}
