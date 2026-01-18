<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Core\Container;
use PedhotDev\NepotismFree\Contract\ContainerInterface;
use PedhotDev\NepotismFree\Exception\DefinitionException;

class ContainerInjectionForbiddenTest extends TestCase
{
    public function testDirectContainerResolutionIsForbidden(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $this->expectException(DefinitionException::class);
        $this->expectExceptionMessage("Injection of the Container is forbidden");
        
        $container->get(Container::class);
    }

    public function testContainerInterfaceResolutionIsForbidden(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $this->expectException(DefinitionException::class);
        $this->expectExceptionMessage("Injection of the Container is forbidden");
        
        $container->get(ContainerInterface::class);
    }

    public function testContainerConstructorInjectionIsForbidden(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $this->expectException(DefinitionException::class);
        $this->expectExceptionMessage("Injection of the Container is forbidden");
        
        $container->get(ServiceDependingOnContainer::class);
    }
}

class ServiceDependingOnContainer
{
    public function __construct(Container $container) {}
}
