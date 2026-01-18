<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Core;

use PedhotDev\NepotismFree\Contract\ContainerInterface;
use PedhotDev\NepotismFree\Exception\NotFoundException;

/**
 * The immutable Dependency Injection Container.
 */
class Container implements ContainerInterface
{
    private Resolver $resolver;
    
    /** @var array<string, object> Cache for singleton instances */
    private array $instances = [];

    public function __construct(
        private Registry $registry,
        private ModuleAccessPolicy $accessPolicy
    ) {
        // Container constructs its own resolver to ensure it passes itself correctly
        $this->resolver = new Resolver($registry, $this);
    }

    public function get(string $id, bool $internal = false): mixed
    {
        // 0. Module Boundary Check (V2 Feature)
        if (!$internal && !$this->accessPolicy->canAccess($id)) {
            throw \PedhotDev\NepotismFree\Exception\ModuleBoundaryException::internalServiceAccess($id, 'UnknownModule');
        }

        // 1. Check if we have an active singleton instance
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // 2. Resolve the dependency
        $object = $this->resolver->resolve($id);

        // 3. Cache if singleton
        if ($this->registry->isSingleton($id)) {
            $this->instances[$id] = $object;
        }

        return $object;
    }

    public function getTagged(string $tag): iterable
    {
        $serviceIds = $this->registry->getTagged($tag);

        if (empty($serviceIds)) {
            // Requirement 4: "Empty tag resolution must be explicit (error or empty, no silent behavior)"
            // Let's decide: If tag exists but no services, return empty array?
            // "No silent success" -> if tag doesn't exist at all, throw exception?
            return [];
        }

        foreach ($serviceIds as $id) {
            yield $this->get($id);
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
}
