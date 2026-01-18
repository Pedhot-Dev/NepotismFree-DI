<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Resolution;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\DefinitionException;

class UnionTypeResolutionTest extends TestCase
{
    public function testUnionTypeResolutionThrowsDefinitionException(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $this->expectException(DefinitionException::class);
        $this->expectExceptionMessage("uses a complex type (union/intersection)");
        
        $container->get(UnionService::class);
    }
}

class DepA {}
class DepB {}
class UnionService { public function __construct(DepA|DepB $dep) {} }
