<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Advanced;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class ContextualBindingConflictTest extends TestCase
{
    public function testLastContextualBindingWinsIfConflictOccurs(): void
    {
        $builder = new ContainerBuilder();
        $builder->bindContext(LoggerInterface::class, LoggerA::class, Consumer::class);
        $builder->bindContext(LoggerInterface::class, LoggerB::class, Consumer::class);
        
        $container = $builder->build();
        $instance = $container->get(Consumer::class);
        
        $this->assertInstanceOf(LoggerB::class, $instance->logger);
    }
}

interface LoggerInterface {}
class LoggerA implements LoggerInterface {}
class LoggerB implements LoggerInterface {}
class Consumer { public function __construct(public LoggerInterface $logger) {} }
