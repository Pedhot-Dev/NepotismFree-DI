<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\DefinitionException;

class FailFastOnFirstErrorTest extends TestCase
{
    public function testResolutionFailsAtTheFirstEncounteredError(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        $this->expectException(DefinitionException::class);
        // It should fail on the first unresolvable parameter, not try to resolve others.
        $container->get(MultiErrorService::class);
    }
}

class MultiErrorService { public function __construct(string $a, int $b) {} }
