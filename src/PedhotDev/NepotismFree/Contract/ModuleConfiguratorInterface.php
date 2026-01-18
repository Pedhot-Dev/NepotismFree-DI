<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Contract;

use Closure;

/**
 * Restricted interface for module configuration.
 * Only allows DECLARATION of bindings.
 */
interface ModuleConfiguratorInterface
{
    public function bind(string $id, string|Closure $implementation): self;
    
    public function singleton(string $id): self;
    
    public function prototype(string $id): self;
    
    public function bindArgument(string $service, string $paramName, mixed $value): self;
    
    public function bindContext(string $interface, string $implementation, string $consumer): self;
    
    public function tag(string $tag, string $serviceId): self;
    
    public function bindParameterObject(string $class, object $instance): self;
}
