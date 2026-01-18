<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Lifecycle;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

class ContextualBindingIsolationTest extends TestCase
{
    public function testContextualBindingDoesNotLeakToOtherConsumers(): void
    {
        $builder = new ContainerBuilder();
        $builder->bind(LoggerInterface::class, DefaultLogger::class);
        $builder->bindContext(LoggerInterface::class, SpecialLogger::class, SpecialService::class);
        
        $container = $builder->build();
        
        $default = $container->get(DefaultService::class);
        $special = $container->get(SpecialService::class);
        
        $this->assertInstanceOf(DefaultLogger::class, $default->logger);
        $this->assertInstanceOf(SpecialLogger::class, $special->logger);
    }
}

interface LoggerInterface {}
class DefaultLogger implements LoggerInterface {}
class SpecialLogger implements LoggerInterface {}
class DefaultService { public function __construct(public LoggerInterface $logger) {} }
class SpecialService { public function __construct(public LoggerInterface $logger) {} }
