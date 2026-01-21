<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Builder;

use Closure;
use PedhotDev\NepotismFree\Core\Container;
use PedhotDev\NepotismFree\Core\Registry;
use PedhotDev\NepotismFree\Core\ModuleAccessPolicy;
use PedhotDev\NepotismFree\Contract\ContainerInterface;
use PedhotDev\NepotismFree\Contract\ModuleInterface;
use PedhotDev\NepotismFree\Component\Validation\Validator;
use PedhotDev\NepotismFree\Component\Compiler\Compiler;
use PedhotDev\NepotismFree\Contract\Scope;
use PedhotDev\NepotismFree\Exception\DefinitionException;

use PedhotDev\NepotismFree\Contract\ModuleConfiguratorInterface;

/**
 * Builds a NepotismFree Container.
 */
class ContainerBuilder implements ModuleConfiguratorInterface
{
    private Registry $registry;
    private ModuleAccessPolicy $accessPolicy;
    private bool $locked = false;
    private ?string $currentModule = null;

    public function __construct()
    {
        $this->registry = new Registry();
        $this->accessPolicy = new ModuleAccessPolicy($this->registry);
    }

    /**
     * Bind an interface to a implementation (class name) or a factory Closure.
     * 
     * @param string $id Interface or Class name
     * @param string|Closure(ContainerInterface):mixed $implementation
     */
    public function bind(string $id, string|Closure $implementation): self
    {
        $this->assertNotLocked();
        $this->registry->bind($id, $implementation, $this->currentModule);
        return $this;
    }

    /**
     * Mark a class or interface as a Singleton.
     */
    public function singleton(string $id): self
    {
        $this->assertNotLocked();
        $this->registry->setScope($id, Scope::PROCESS);
        return $this;
    }

    /**
     * Mark a class or interface as a Prototype (new instance every time).
     * This is the default, but explicit configuration is allowed.
     */
    public function prototype(string $id): self
    {
        $this->assertNotLocked();
        $this->registry->setScope($id, Scope::PROTOTYPE);
        return $this;
    }

    /**
     * Mark a class or interface as Scoped (shared within a Tick/Request).
     */
    public function scoped(string $id): self
    {
        $this->assertNotLocked();
        $this->registry->setScope($id, Scope::TICK);
        return $this;
    }

    /**
     * Bind a scalar argument for a specific service.
     * 
     * @param string $service Class name to inject into
     * @param string $paramName Constructor parameter name
     * @param mixed $value The value to inject
     */
    public function bindArgument(string $service, string $paramName, mixed $value): self
    {
        $this->assertNotLocked();
        $this->registry->bindArgument($service, $paramName, $value);
        return $this;
    }

    /**
     * Bind a contextual implementation for a specific consumer.
     */
    public function bindContext(string $interface, string $implementation, string $consumer): self
    {
        $this->assertNotLocked();
        $this->registry->bindContext($interface, $implementation, $consumer);
        return $this;
    }

    /**
     * Tag a service for group resolution.
     */
    public function tag(string $tag, string $serviceId): self
    {
        $this->assertNotLocked();
        $this->registry->tag($tag, $serviceId);
        return $this;
    }

    /**
     * Bind an immutable parameter object.
     */
    public function bindParameterObject(string $class, object $instance): self
    {
        $this->assertNotLocked();
        $this->registry->bindParameterObject($class, $instance);
        return $this;
    }


    /**
     * Add a module and configure its services.
     */
    public function addModule(ModuleInterface $module): self
    {
        $this->assertNotLocked();
        $this->accessPolicy->setEnforcement(true);
        
        $prevModule = $this->currentModule;
        $this->currentModule = (new \ReflectionClass($module))->getShortName();

        try {
            $module->configure($this);
            foreach ($module->getExposedServices() as $id) {
                $this->accessPolicy->allowAccess($id);
            }
        } finally {
            $this->currentModule = $prevModule;
        }
        
        return $this;
    }

    /**
     * Perform full graph validation.
     * @throws \PedhotDev\NepotismFree\Exception\DefinitionException
     */
    public function validate(): self
    {
        $validator = new Validator($this->registry);
        $validator->validate($this->registry->getServiceIds());
        return $this;
    }

    /**
     * Compile the container to a static PHP file.
     */
    public function compile(string $path, string $className = 'CompiledContainer'): self
    {
        $compiler = new Compiler($this->registry, $this->accessPolicy);
        $compiler->compile($path, $className);
        return $this;
    }

    /**
     * Compile and lock the container.
     */
    public function build(): Container
    {
        $this->locked = true;
        return new Container($this->registry, $this->accessPolicy);
    }

    private function assertNotLocked(): void
    {
        if ($this->locked) {
            throw DefinitionException::builderIsImmutable();
        }
    }
}
