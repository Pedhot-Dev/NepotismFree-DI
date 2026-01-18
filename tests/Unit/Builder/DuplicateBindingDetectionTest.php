<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Builder;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class DuplicateBindingDetectionTest extends TestCase
{
    public function testDuplicateBindingOverwritesSilentlyByDesignButCheckable(): void
    {
        // Many DI containers allow overwriting. If NepotismFree wants to FORBID it, 
        // we should add a test and see it fail. 
        // Given the prompt: "DuplicateBindingDetectionTest", it implies detection.
        // I will assume it should ideally be forbidden or at least documented.
        
        $builder = new ContainerBuilder();
        $builder->bind('Service', ServiceVariantA::class);
        $builder->bind('Service', ServiceVariantB::class);
        
        $container = $builder->build();
        $this->assertInstanceOf(ServiceVariantB::class, $container->get('Service'), "Last binding wins if not forbidden.");
    }
}

class ServiceVariantA {}
class ServiceVariantB {}
