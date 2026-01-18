<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Module;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Contract\ModuleInterface;
use PedhotDev\NepotismFree\Contract\ModuleConfiguratorInterface;

class ModuleCannotAccessContainerTest extends TestCase
{
    public function testModuleCannotAccessContainerDuringConfiguration(): void
    {
        $module = new class implements ModuleInterface {
            public function configure(ModuleConfiguratorInterface $configurator): void {
                // There is no way to get the container from the configurator
                $reflection = new \ReflectionObject($configurator);
                \PHPUnit\Framework\Assert::assertFalse($reflection->hasMethod('getContainer'));
            }
            public function getExposedServices(): array { return []; }
        };

        $builder = new ContainerBuilder();
        $builder->addModule($module);
        $builder->build();
    }
}
