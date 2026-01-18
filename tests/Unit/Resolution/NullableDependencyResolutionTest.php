<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Resolution;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class NullableDependencyResolutionTest extends TestCase
{
    public function testNullableDependencyResolvesToNullIfUnbound(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $instance = $container->get(NullableService::class);
        $this->assertNull($instance->dep);
    }
}

interface SomeDepInterface {}
class NullableService { public function __construct(public ?SomeDepInterface $dep) {} }
