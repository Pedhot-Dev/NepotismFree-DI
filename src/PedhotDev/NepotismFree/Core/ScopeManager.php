<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Core;

use PedhotDev\NepotismFree\Contract\ContainerInterface;
use PedhotDev\NepotismFree\Contract\Scope;

/**
 * Manages the lifecycle of logical execution scopes (ticks).
 */
class ScopeManager
{
    public function __construct(private ContainerInterface $container) {}

    /**
     * Executes a callback within a specific scope.
     * The scoped container is disposed after execution.
     * 
     * @template T
     * @param Scope $scope
     * @param callable(ScopedContainer):T $callback
     * @return T
     */
    public function run(Scope $scope, callable $callback): mixed
    {
        $scopedContainer = $this->container->createScope($scope);
        return $callback($scopedContainer);
    }
}
