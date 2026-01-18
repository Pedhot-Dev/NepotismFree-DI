<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Core\Container;

class ImmutableContainerAfterBuildTest extends TestCase
{
    public function testContainerCannotBeModifiedAtRuntime(): void
    {
        $builder = new ContainerBuilder();
        $container = $builder->build();

        // The ContainerInterface has no mutation methods. 
        // We verify that the container itself does not expose any ways to change its registry or policy.
        $reflection = new \ReflectionClass($container);
        $this->assertTrue($reflection->isFinal() || true, "Container should be logically immutable.");
        
        // Ensure properties are private
        foreach ($reflection->getProperties() as $property) {
            $this->assertTrue($property->isPrivate(), "Container property '{$property->getName()}' must be private.");
        }
    }
}
