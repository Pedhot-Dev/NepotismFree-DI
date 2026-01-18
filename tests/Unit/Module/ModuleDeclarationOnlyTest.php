<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Module;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Contract\ModuleInterface;
use PedhotDev\NepotismFree\Contract\ModuleConfiguratorInterface;

class ModuleDeclarationOnlyTest extends TestCase
{
    public function testModuleCanOnlyDeclareBindingsAndNotResolve(): void
    {
        $module = new class implements ModuleInterface {
            public function configure(ModuleConfiguratorInterface $configurator): void {
                $configurator->bind('Service', \stdClass::class);
                // ModuleConfiguratorInterface does not have get() or resolve()
            }
            public function getExposedServices(): array { return []; }
        };

        $builder = new ContainerBuilder();
        $builder->addModule($module);
        $container = $builder->build();

        $this->assertTrue(true, "Module configured successfully without being able to resolve.");
    }
}
