<?php

declare(strict_types=1);

namespace Tests\Unit\Introspection;

use PedhotDev\NepotismFree\Core\Container;
use PedhotDev\NepotismFree\Core\ModuleAccessPolicy;
use PedhotDev\NepotismFree\Core\Registry;
use PedhotDev\NepotismFree\Contract\IntrospectableContainerInterface;
use PedhotDev\NepotismFree\Introspection\ServiceNode;
use PHPUnit\Framework\TestCase;

class IntrospectionTest extends TestCase
{
    private Container $container;
    private Registry $registry;

    protected function setUp(): void
    {
        $this->registry = new Registry();
        $this->container = new Container($this->registry, new ModuleAccessPolicy($this->registry));
    }

    public function test_it_implements_introspectable_interface(): void
    {
        $this->assertInstanceOf(IntrospectableContainerInterface::class, $this->container);
    }

    public function test_it_exposes_definitions(): void
    {
        $this->registry->bind(ServiceA::class, ServiceA::class);
        $this->registry->bind('my_factory', fn() => new \stdClass());

        $defs = $this->container->getDefinitions();

        $this->assertArrayHasKey(ServiceA::class, $defs);
        $this->assertArrayHasKey('my_factory', $defs);
        $this->assertInstanceOf(\Closure::class, $defs['my_factory']);
    }

    public function test_it_tracks_resolved_services(): void
    {
        $this->registry->bind(ServiceA::class, ServiceA::class);
        $this->registry->setSingleton(ServiceA::class, true);

        $this->assertEmpty($this->container->getResolvedIds());

        $this->container->get(ServiceA::class);

        $resolved = $this->container->getResolvedIds();
        $this->assertCount(1, $resolved);
        $this->assertContains(ServiceA::class, $resolved);
    }

    public function test_it_generates_dependency_graph(): void
    {
        // Setup: A depends on B
        $this->registry->bind(ServiceA::class, ServiceA::class);
        $this->registry->bind(ServiceB::class, ServiceB::class);

        $graph = $this->container->getDependencyGraph();
        
        $nodeA = $graph->getNode(ServiceA::class);
        $this->assertInstanceOf(ServiceNode::class, $nodeA);
        $this->assertTrue($nodeA->isResolved === false);
        $this->assertContains(ServiceB::class, $nodeA->dependencies);

        $nodeB = $graph->getNode(ServiceB::class);
        $this->assertEmpty($nodeB->dependencies);
    }

    public function test_it_detects_cycles_statically(): void
    {
        // Setup: CycleA -> CycleB -> CycleA
        $this->registry->bind(CycleA::class, CycleA::class);
        $this->registry->bind(CycleB::class, CycleB::class);

        $graph = $this->container->getDependencyGraph();

        $this->assertTrue($graph->hasCycle(CycleA::class));
        $this->assertTrue($graph->hasCycle(CycleB::class));
    }
}

// Fixtures
class ServiceB {}
class ServiceA { public function __construct(ServiceB $b) {} }

class CycleB { public function __construct(CycleA $a) {} }
class CycleA { public function __construct(CycleB $b) {} }
