<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Advanced;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class DeterministicTagOrderTest extends TestCase
{
    public function testTagOrderIsDeterministicBasedOnDeclaration(): void
    {
        $builder = new ContainerBuilder();
        $builder->tag('order', 'First');
        $builder->bind('First', \stdClass::class);
        $builder->tag('order', 'Second');
        $builder->bind('Second', \stdClass::class);
        
        $container = $builder->build();
        $names = [];
        foreach ($container->getTagged('order') as $service) {
            $names[] = get_class($service);
        }
        
        // Both are stdClass, so let's use different implementations to be sure
        $builder = new ContainerBuilder();
        $builder->tag('order', OrderPluginA::class);
        $builder->tag('order', OrderPluginB::class);
        $container = $builder->build();
        
        $instances = iterator_to_array($container->getTagged('order'));
        $this->assertInstanceOf(OrderPluginA::class, $instances[0]);
        $this->assertInstanceOf(OrderPluginB::class, $instances[1]);
    }
}

class OrderPluginA {}
class OrderPluginB {}
