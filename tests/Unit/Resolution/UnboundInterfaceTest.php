<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Resolution;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\NotFoundException;

class UnboundInterfaceTest extends TestCase
{
    public function testUnboundInterfaceThrowsNotFoundException(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $this->expectException(NotFoundException::class);
        $container->get(SomeInterface::class);
    }
}

interface SomeInterface {}
