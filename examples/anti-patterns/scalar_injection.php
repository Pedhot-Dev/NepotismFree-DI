<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use PedhotDev\NepotismFree\Builder\ContainerBuilder;

/**
 * ANTI-PATTERN: Undefined Scalar Injection
 *
 * Why this is forbidden:
 * 1. The container is designed for object graph construction.
 * 2. Required scalar parameters without defaults or explicit bindings break determinism.
 * 3. "Magic" resolution of strings or integers is error-prone.
 */

class DatabaseConfig
{
    // INCORRECT: Depending on a scalar value that the container doesn't know how to fill
    public function __construct(
        private string $dsn,
        private int $timeout
    ) {}
}

try {
    $builder = new ContainerBuilder();

    // This will fail with a DefinitionException during build or resolution
    // because the container cannot "guess" the $dsn or $timeout values.
    $container = $builder->build();
    $container->get(DatabaseConfig::class);

} catch (Throwable $e) {
    echo "INTENTIONAL FAILURE: " . $e->getMessage() . PHP_EOL;
}
