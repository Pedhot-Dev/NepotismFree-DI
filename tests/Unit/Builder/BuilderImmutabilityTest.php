<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Builder;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\DefinitionException;

class BuilderImmutabilityTest extends TestCase
{
    public function test_builder_cannot_be_modified_after_build(): void
    {
        $builder = new ContainerBuilder();
        $builder->build();

        $this->expectException(DefinitionException::class);
        $this->expectExceptionMessage("The builder is immutable");

        $builder->bind('SomeService', \stdClass::class);
    }

    public function test_tagging_fails_after_build(): void
    {
        $builder = new ContainerBuilder();
        $builder->build();

        $this->expectException(DefinitionException::class);
        $builder->tag('group', 'service');
    }

    public function test_singleton_fails_after_build(): void
    {
        $builder = new ContainerBuilder();
        $builder->build();

        $this->expectException(DefinitionException::class);
        $builder->singleton('service');
    }
}
