<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Lifecycle;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class PrototypeLifecycleTest extends TestCase
{
    public function testPrototypeReturnsNewInstanceEachTime(): void
    {
        $builder = new ContainerBuilder();
        $builder->prototype(PrototypeService::class);
        $container = $builder->build();

        $instance1 = $container->get(PrototypeService::class);
        $instance2 = $container->get(PrototypeService::class);

        $this->assertNotSame($instance1, $instance2);
    }
}

class PrototypeService {}
