<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Advanced;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class EmptyTagResolutionBehaviorTest extends TestCase
{
    public function testEmptyTagReturnsEmptyGenerator(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();
        
        $tagged = $container->getTagged('non-existent');
        $this->assertCount(0, iterator_to_array($tagged));
    }
}
