<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\CircularDependencyException;

class CircularDependencyDetectionTest extends TestCase
{
    public function testCircularDependencyThrowsExceptionWithPath(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $this->expectException(CircularDependencyException::class);
        $this->expectExceptionMessage("Circular dependency detected");
        
        $container->get(CircularA::class);
    }
}

class CircularA { public function __construct(CircularB $b) {} }
class CircularB { public function __construct(CircularA $a) {} }
