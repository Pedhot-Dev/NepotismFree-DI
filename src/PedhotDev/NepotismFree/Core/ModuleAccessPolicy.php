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

    public function allowAccess(string $id): void
    {
        $this->allowedServices[$id] = true;
    }

    public function canAccess(string $id): bool
    {
        if (!$this->enforcementEnabled) {
            return true;
        }
        return $this->allowedServices[$id] ?? false;
    }

    public function setEnforcement(bool $enabled): void
    {
        $this->enforcementEnabled = $enabled;
    }
}
