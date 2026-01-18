<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Lifecycle;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class SingletonLifecycleTest extends TestCase
{
    public function testSingletonReturnsSameInstance(): void
    {
        $builder = new ContainerBuilder();
        $builder->singleton(SingletonService::class);
        $container = $builder->build();

        $instance1 = $container->get(SingletonService::class);
        $instance2 = $container->get(SingletonService::class);

        $this->assertSame($instance1, $instance2);
    }
}

class SingletonService {}
