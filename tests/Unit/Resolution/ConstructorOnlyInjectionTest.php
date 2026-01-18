<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Resolution;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class ConstructorOnlyInjectionTest extends TestCase
{
    public function testOnlyConstructorParametersAreInjected(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $instance = $container->get(ConstructorOnlyService::class);
        $this->assertInstanceOf(ConstructorOnlyDependency::class, $instance->dep);
    }
}

class ConstructorOnlyDependency {}
class ConstructorOnlyService { public function __construct(public ConstructorOnlyDependency $dep) {} }
