<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Core;

/**
 * Shared state for a resolution process.
 * Used to track the resolution stack and currently building services across scopes.
 */
class ResolutionContext
{
    /** @var array<string, bool> */
    public array $building = [];
    
    /** @var string[] */
    public array $stack = [];
}
