<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Contract;

/**
 * Defines the lifecycle of a service.
 */
enum Scope: string
{
    /**
     * Shared across the entire process lifetime (Global Singleton).
     */
    case PROCESS = 'process';

    /**
     * Shared within a logical execution cycle (e.g., a Tick or a Request).
     */
    case TICK = 'tick';

    /**
     * A new instance is created every time the service is requested.
     */
    case PROTOTYPE = 'prototype';
}
