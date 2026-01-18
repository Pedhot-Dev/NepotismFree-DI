<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Tests\Unit\Module;

use PHPUnit\Framework\TestCase;
use PedhotDev\NepotismFree\Contract\ModuleInterface;
use PedhotDev\NepotismFree\Contract\ModuleConfiguratorInterface;

class ModuleCannotResolveServicesTest extends TestCase
{
    public function testModuleConfiguratorHasNoResolveMethod(): void
    {
        $reflection = new \ReflectionClass(ModuleConfiguratorInterface::class);
        $this->assertFalse($reflection->hasMethod('get'));
        $this->assertFalse($reflection->hasMethod('resolve'));
    }
}
