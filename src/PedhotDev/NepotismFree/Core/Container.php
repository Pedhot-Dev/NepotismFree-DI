<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Core;

use PedhotDev\NepotismFree\Contract\ContainerInterface;
use PedhotDev\NepotismFree\Contract\IntrospectableContainerInterface;
use PedhotDev\NepotismFree\Exception\NotFoundException;
use PedhotDev\NepotismFree\Contract\Scope;
use PedhotDev\NepotismFree\Core\ResolutionContext;
use PedhotDev\NepotismFree\Introspection\DependencyGraph;
use PedhotDev\NepotismFree\Introspection\ServiceNode;

/**
 * The immutable Dependency Injection Container.
 */
class Container implements ContainerInterface, IntrospectableContainerInterface
{
    private Resolver $resolver;
    
    /** @var array<string, object> Cache for singleton instances */
    private array $instances = [];

    public function __construct(
        private Registry $registry,
        private ModuleAccessPolicy $accessPolicy,
        private Scope $scope = Scope::PROCESS,
        private ?ResolutionContext $context = null
    ) {
        $this->context ??= new ResolutionContext();
        $this->resolver = new Resolver($registry, $this, $this->context);
    }

    public function get(string $id, bool $internal = false): mixed
    {
        // 0. Module Boundary Check
        $consumerId = $this->resolver->getCurrentConsumer();
        $consumerModule = $consumerId ? $this->registry->getModule($consumerId) : null;

        if (!$internal && !$this->accessPolicy->canAccess($id, $consumerModule)) {
            $serviceModule = $this->registry->getModule($id) ?? 'UnknownModule';
            throw \PedhotDev\NepotismFree\Exception\ModuleBoundaryException::internalServiceAccess($id, $serviceModule, $consumerModule);
        }

        // 1. Check if we have an active instance in THIS scope
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // 2. Resolve the dependency
        $object = $this->resolver->resolve($id);

        // 3. Cache if it belongs to THIS scope
        if ($this->registry->getScope($id) === $this->scope) {
            $this->instances[$id] = $object;
        }

        return $object;
    }

    /**
     * Creates a child container for a specific scope.
     */
    public function createScope(Scope $scope): ScopedContainer
    {
        return new ScopedContainer($this, $this->registry, $this->accessPolicy, $scope, $this->context);
    }

    public function getTagged(string $tag): iterable
    {
        $serviceIds = $this->registry->getTagged($tag);

        if (empty($serviceIds)) {
            return [];
        }

        $consumerId = $this->resolver->getCurrentConsumer();
        $consumerModule = $consumerId ? $this->registry->getModule($consumerId) : null;

        foreach ($serviceIds as $id) {
            if ($this->accessPolicy->canAccess($id, $consumerModule)) {
                yield $this->get($id, true);
            }
        }
    }

    public function has(string $id): bool
    {
        // Definition check: Is it bound explicitly?
        if ($this->registry->getBinding($id) !== null) {
            return true;
        }

        // Is it a class that exists? (We auto-wire concrete classes if they are valid)
        if (class_exists($id)) {
            return true;
        }

        return false;
    }

    // --- Introspection API ---

    public function getDefinitions(): array
    {
        return $this->registry->getBindings();
    }

    public function getResolvedIds(): array
    {
        return array_keys($this->instances);
    }

    public function getDependencyGraph(): DependencyGraph
    {
        $nodes = [];
        $ids = $this->registry->getServiceIds();

        foreach ($ids as $id) {
            // Determine type
            $isSingleton = $this->registry->isSingleton($id);
            $type = $isSingleton ? 'singleton' : 'prototype';
            
            // Check implementation
            $binding = $this->registry->getBinding($id);
            if ($binding instanceof \Closure) {
                // Factories are opaque to static analysis mostly
                $node = new ServiceNode(
                    id: $id,
                    type: 'factory', 
                    isResolved: isset($this->instances[$id]),
                    concrete: 'closure'
                );
            } else {
                $concrete = is_string($binding) ? $binding : $id;
                
                $node = new ServiceNode(
                    id: $id,
                    type: $type,
                    isResolved: isset($this->instances[$id]),
                    concrete: $concrete
                );

                // Calculate dependencies if it's a class
                if (class_exists($concrete)) {
                    $deps = $this->resolver->getDependencies($concrete);
                    foreach ($deps as $dep) {
                        if (isset($dep['type'])) {
                             $node->addDependency($dep['type']);
                        }
                    }
                }
            }
            $nodes[$id] = $node;
        }

        return new DependencyGraph($nodes);
    }
}
