<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Resolution;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class DefaultParameterValueTest extends TestCase
{
    public function testBuiltinTypeWithDefaultValueDoesNotFail(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $instance = $container->get(DefaultValueService::class);
        $this->assertEquals(8080, $instance->port);
    }
}

class DefaultValueService { public function __construct(public int $port = 8080) {} }
