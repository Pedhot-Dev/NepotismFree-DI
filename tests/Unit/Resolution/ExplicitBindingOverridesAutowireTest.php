<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Resolution;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class ExplicitBindingOverridesAutowireTest extends TestCase
{
    public function testExplicitBindingTakesPrecedenceOverAutowiring(): void
    {
        $builder = new ContainerBuilder();
        $builder->bind(OverrideDependency::class, OverrideSubDependency::class);
        $container = $builder->build();

        $instance = $container->get(OverrideDependency::class);
        $this->assertInstanceOf(OverrideSubDependency::class, $instance);
    }
}

class OverrideDependency {}
class OverrideSubDependency extends OverrideDependency {}
