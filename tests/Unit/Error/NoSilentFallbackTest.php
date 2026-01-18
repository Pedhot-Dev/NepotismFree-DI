<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\NotFoundException;

class NoSilentFallbackTest extends TestCase
{
    public function testContainerDoesNotSilentlyReturnNullForMissingServices(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $this->expectException(NotFoundException::class);
        $container->get('NonExistent');
    }
}
