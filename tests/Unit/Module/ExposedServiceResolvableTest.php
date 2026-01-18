<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Module;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Contract\ModuleInterface;
use PedhotDev\NepotismFree\Contract\ModuleConfiguratorInterface;

class ExposedServiceResolvableTest extends TestCase
{
    public function testExposedServiceCanBeResolvedDirectly(): void
    {
        $module = new class implements ModuleInterface {
            public function configure(ModuleConfiguratorInterface $configurator): void {
                $configurator->bind('Exposed', \stdClass::class);
            }
            public function getExposedServices(): array { return ['Exposed']; }
        };

        $builder = new ContainerBuilder();
        $builder->addModule($module);
        $container = $builder->build();

        $instance = $container->get('Exposed');
        $this->assertInstanceOf(\stdClass::class, $instance);
    }
}
