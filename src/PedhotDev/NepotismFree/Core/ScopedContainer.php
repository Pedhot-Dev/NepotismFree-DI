<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Core;

use PedhotDev\NepotismFree\Contract\ContainerInterface;
use PedhotDev\NepotismFree\Contract\Scope;
use PedhotDev\NepotismFree\Core\ResolutionContext;
use PedhotDev\NepotismFree\Exception\ModuleBoundaryException;

/**
 * A container that handles instances for a specific scope (e.g., TICK or REQUEST),
 * delegating to a parent container for other scopes.
 */
class ScopedContainer implements ContainerInterface
{
    /** @var array<string, object> */
    private array $instances = [];
    private Resolver $resolver;

    public function __construct(
        private Container $parent,
        private Registry $registry,
        private ModuleAccessPolicy $accessPolicy,
        private Scope $scope,
        private ResolutionContext $context
    ) {
        $this->resolver = new Resolver($registry, $this, $context);
    }

    public function get(string $id, bool $internal = false): mixed
    {
        // 0. Module Boundary Check
        $consumerId = $this->resolver->getCurrentConsumer();
        $consumerModule = $consumerId ? $this->registry->getModule($consumerId) : null;

        if (!$internal && !$this->accessPolicy->canAccess($id, $consumerModule)) {
            $serviceModule = $this->registry->getModule($id) ?? 'UnknownModule';
            throw ModuleBoundaryException::internalServiceAccess($id, $serviceModule, $consumerModule);
        }

        $serviceScope = $this->registry->getScope($id);

        // 1. If it belongs to THIS scope, manage it here
        if ($serviceScope === $this->scope) {
            if (isset($this->instances[$id])) {
                return $this->instances[$id];
            }

            return $this->instances[$id] = $this->resolver->resolve($id);
        }

        // 2. Delegate to parent for other scopes (e.g., PROCESS or PROTOTYPE)
        return $this->parent->get($id, $internal);
    }

    public function has(string $id): bool
    {
        return $this->parent->has($id);
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
}
