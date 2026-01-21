<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Core;

/**
 * Enforces module boundary access (exposed services + strict status).
 */
class ModuleAccessPolicy
{
    private array $allowedServices = [];
    private bool $enforcementEnabled = false;

    public function __construct(private Registry $registry) {}

    public function allowAccess(string $id): void
    {
        $this->allowedServices[$id] = true;
    }

    public function canAccess(string $id, ?string $consumerModule = null): bool
    {
        if (!$this->enforcementEnabled) {
            return true;
        }

        // 1. If explicitly allowed (exposed), anyone can access
        if (isset($this->allowedServices[$id])) {
            return true;
        }

        // 2. If it's internal to the SAME module, it's fine
        if ($consumerModule !== null) {
            $serviceModule = $this->registry->getModule($id);
            if ($serviceModule === $consumerModule) {
                return true;
            }
        }

        return false;
    }

    public function setEnforcement(bool $enabled): void
    {
        $this->enforcementEnabled = $enabled;
    }
}
