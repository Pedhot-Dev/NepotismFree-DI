<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\DefinitionException;

class SingleCauseExceptionTest extends TestCase
{
    public function testExceptionHasClearReasonWithoutNestingUnlessExternal(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        try {
            $container->get(SingleCauseScalarService::class);
        } catch (DefinitionException $e) {
            $this->assertNull($e->getPrevious(), "Internal configuration errors should be the root cause.");
        }
    }
}

class SingleCauseScalarService { public function __construct(string $name) {} }
