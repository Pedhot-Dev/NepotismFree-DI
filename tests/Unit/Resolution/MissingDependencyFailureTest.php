<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Resolution;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\NotFoundException;

class MissingDependencyFailureTest extends TestCase
{
    public function testResolvingClassWithMissingInterfaceDependencyFails(): void
    {
        $this->expectException(NotFoundException::class);
        
        $builder = new ContainerBuilder();
        $container = $builder->build();
        
        $container->get(ServiceWithMissingDep::class);
    }
}

interface DependencyInterface {}
class ServiceWithMissingDep { public function __construct(DependencyInterface $dep) {} }
