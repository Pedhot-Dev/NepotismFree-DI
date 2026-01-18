<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\DefinitionException;

class ErrorMessageSpecificityTest extends TestCase
{
    public function testErrorMessageIncludesClassNameAndParameterName(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        try {
            $container->get(SpecificErrorService::class);
            $this->fail("Expected DefinitionException was not thrown.");
        } catch (DefinitionException $e) {
            $this->assertStringContainsString('SpecificErrorService', $e->getMessage());
            $this->assertStringContainsString('$name', $e->getMessage());
        }
    }
}

class SpecificErrorService { public function __construct(string $name) {} }
