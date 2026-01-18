<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\DefinitionException;

class ScalarInjectionRejectedTest extends TestCase
{
    public function testUntypedScalarInjectionIsRejected(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $this->expectException(DefinitionException::class);
        $this->expectExceptionMessage("has no type. Loose typing is forbidden.");
        
        $container->get(RejectUntypedService::class);
    }

    public function testUnboundBuiltinScalarIsRejected(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $this->expectException(DefinitionException::class);
        $this->expectExceptionMessage("Cannot resolve built-in type 'string'");
        
        $container->get(RejectScalarService::class);
    }
}

class RejectUntypedService { public function __construct($name) {} }
class RejectScalarService { public function __construct(string $name) {} }
