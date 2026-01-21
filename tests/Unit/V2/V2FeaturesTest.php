<?php

declare(strict_types=1);

namespace Tests\Unit\V2;

use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Contract\Scope;
use PedhotDev\NepotismFree\Core\ScopeManager;
use PedhotDev\NepotismFree\Exception\DefinitionException;
use PedhotDev\NepotismFree\Exception\ModuleBoundaryException;
use PHPUnit\Framework\TestCase;

class V2FeaturesTest extends TestCase
{
    public function test_tick_scope_resets(): void
    {
        $builder = new ContainerBuilder();
        $builder->bind(TickService::class, TickService::class);
        $builder->scoped(TickService::class);
        
        $container = $builder->build();
        $scopeManager = new ScopeManager($container);

        $instance1 = null;
        $instance2 = null;

        $scopeManager->run(Scope::TICK, function($c) use (&$instance1) {
            $instance1 = $c->get(TickService::class);
            $this->assertSame($instance1, $c->get(TickService::class));
        });

        $scopeManager->run(Scope::TICK, function($c) use (&$instance2) {
            $instance2 = $c->get(TickService::class);
            $this->assertSame($instance2, $c->get(TickService::class));
        });

        $this->assertNotSame($instance1, $instance2);
    }

    public function test_unsafe_scope_injection_throws_exception(): void
    {
        $builder = new ContainerBuilder();
        $builder->bind(SingletonService::class, SingletonService::class);
        $builder->singleton(SingletonService::class);
        $builder->bind(TickService::class, TickService::class);
        $builder->scoped(TickService::class);

        $container = $builder->build();
        
        $this->expectException(DefinitionException::class);
        $this->expectExceptionMessage("Unsafe scope injection");
        
        $container->get(SingletonService::class);
    }

    public function test_contextual_binding_propagation(): void
    {
        $builder = new ContainerBuilder();
        $builder->bind(DeepDependency::class, DeepDependency::class);
        $builder->bind(MiddleMan::class, MiddleMan::class);
        $builder->bind(Root::class, Root::class);

        // Override DeepDependency ONLY when resolved for Root
        $builder->bindContext(DeepDependency::class, OverriddenDependency::class, Root::class);

        $container = $builder->build();
        $root = $container->get(Root::class);

        $this->assertInstanceOf(OverriddenDependency::class, $root->middleMan->deep);
    }

    public function test_contextual_binding_propagation_across_scopes(): void
    {
        $builder = new ContainerBuilder();
        $builder->bind(CrossScopeTickService::class, CrossScopeTickService::class)->scoped(CrossScopeTickService::class);
        $builder->bind(CrossScopeSingletonService::class, CrossScopeSingletonService::class)->singleton(CrossScopeSingletonService::class);
        $builder->bind(LoggerInterface::class, DefaultLogger::class);

        // Override Logger ONLY when Root depends on it, but Root -> Singleton -> Logger
        $builder->bindContext(LoggerInterface::class, SpecialLogger::class, CrossScopeTickService::class);

        $container = $builder->build();
        $scopeManager = new ScopeManager($container);

        $scopeManager->run(Scope::TICK, function($c) {
            $tick = $c->get(CrossScopeTickService::class);
            $this->assertInstanceOf(SpecialLogger::class, $tick->singleton->logger);
        });
    }

    public function test_module_boundary_enforcement(): void
    {
        $builder = new ContainerBuilder();
        
        $module = new class implements \PedhotDev\NepotismFree\Contract\ModuleInterface {
            public function configure(\PedhotDev\NepotismFree\Contract\ModuleConfiguratorInterface $builder): void {
                $builder->bind(InternalService::class, InternalService::class);
            }
            public function getExposedServices(): array {
                return []; // Nothing exposed
            }
        };

        $builder->addModule($module);
        $container = $builder->build();

        $this->expectException(ModuleBoundaryException::class);
        $this->expectExceptionMessage("is internal to module 'ModuleInterface@anonymous");
        
        $container->get(InternalService::class);
    }

    public function test_compiler_functional_parity(): void
    {
        $builder = new ContainerBuilder();
        $builder->bind(TickService::class, TickService::class);
        $builder->singleton(TickService::class);
        $builder->bindArgument(MiddleMan::class, 'name', 'Compiled');

        $path = __DIR__ . '/CompiledContainer.php';
        $builder->compile($path, 'TestCompiledContainer');

        require_once $path;
        $compiled = new \PedhotDev\NepotismFree\Compiled\TestCompiledContainer();

        $this->assertInstanceOf(TickService::class, $compiled->get(TickService::class));
        $this->assertSame($compiled->get(TickService::class), $compiled->get(TickService::class));
        
        unlink($path);
    }
}

// Fixtures
class TickService {}
class SingletonService { public function __construct(TickService $tick) {} }
class DeepDependency {}
class OverriddenDependency extends DeepDependency {}
class MiddleMan { public function __construct(public DeepDependency $deep) {} }
class Root { public function __construct(public MiddleMan $middleMan) {} }
class InternalService {}

interface LoggerInterface {}
class DefaultLogger implements LoggerInterface {}
class SpecialLogger implements LoggerInterface {}
class CrossScopeTickService { public function __construct(public CrossScopeSingletonService $singleton) {} }
class CrossScopeSingletonService { public function __construct(public LoggerInterface $logger) {} }
