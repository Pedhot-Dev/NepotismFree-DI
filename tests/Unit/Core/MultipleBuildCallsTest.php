<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\DefinitionException;

class MultipleBuildCallsTest extends TestCase
{
    public function testCallingBuildMultipleTimesShouldNotResetOrBreakInvariants(): void
    {
        $builder = new ContainerBuilder();
        $container1 = $builder->build();
        
        // Based on the locked builder requirement, build() itself might be locked too?
        // Let's check: if we want strictness, building twice on the same builder might be forbidden or simply redundant.
        // Actually, the current implementation allows it but it's logically "locked" for mutation.
        
        $container2 = $builder->build();
        $this->assertNotSame($container1, $container2, "Successive builds return new container instances.");
    }
}
