<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Resolution;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class NoPropertyInjectionTest extends TestCase
{
    public function testPropertyInjectionIsForbiddenByOmission(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $instance = $container->get(PropertyService::class);
        $this->assertNull($instance->dep, "Property should not be injected automatically.");
    }
}

class PropertyDependency {}
class PropertyService 
{ 
    /** @Inject */
    public ?PropertyDependency $dep = null;
}
