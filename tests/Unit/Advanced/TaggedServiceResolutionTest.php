<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Advanced;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class TaggedServiceResolutionTest extends TestCase
{
    public function testTaggedServicesCanBeResolvedAsGenerator(): void
    {
        $builder = new ContainerBuilder();
        $builder->tag('plugin', PluginA::class);
        $builder->tag('plugin', PluginB::class);
        
        $container = $builder->build();
        $tagged = $container->getTagged('plugin');
        
        $this->assertInstanceOf(\Generator::class, $tagged);
        $instances = iterator_to_array($tagged);
        
        $this->assertCount(2, $instances);
        $this->assertInstanceOf(PluginA::class, $instances[0]);
        $this->assertInstanceOf(PluginB::class, $instances[1]);
    }
}

class PluginA {}
class PluginB {}
