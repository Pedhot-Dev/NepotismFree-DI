<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Resolution;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class NoSetterInjectionTest extends TestCase
{
    public function testSetterInjectionIsForbiddenByOmission(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $instance = $container->get(SetterService::class);
        $this->assertNull($instance->dep, "Setter should not be called automatically.");
    }
}

class SetterDependency {}
class SetterService 
{ 
    public ?SetterDependency $dep = null;
    public function setDep(SetterDependency $dep): void { $this->dep = $dep; }
}
