<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Lifecycle;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class LifecycleIsolationTest extends TestCase
{
    public function testSingletonIsIsolatedBetweenContainers(): void
    {
        $builder = new ContainerBuilder();
        $builder->singleton(SharedService::class);
        
        $container1 = $builder->build();
        // Since builder is locked after build, we need a NEW builder for second container if we want to test isolation
        // OR we test if successive builds (if allowed) return isolated containers.
        // Actually, build() locks the builder. Let's use two builders.
        
        $builder1 = new ContainerBuilder();
        $builder1->singleton(IsolatedSharedService::class);
        $c1 = $builder1->build();

        $builder2 = new ContainerBuilder();
        $builder2->singleton(IsolatedSharedService::class);
        $c2 = $builder2->build();

        $this->assertNotSame($c1->get(IsolatedSharedService::class), $c2->get(IsolatedSharedService::class));
    }
}

class IsolatedSharedService {}
