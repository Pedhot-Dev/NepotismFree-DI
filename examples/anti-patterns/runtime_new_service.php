<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use PedhotDev\NepotismFree\Builder\ContainerBuilder;

/**
 * ANTI-PATTERN: Runtime 'new' for Managed Services
 *
 * Why this is forbidden:
 * 1. It bypasses the container's ability to manage singletons.
 * 2. It prevents mocking during unit tests (hard-coded dependency).
 * 3. It breaks the 'Inversion of Control' principle.
 */

class Logger {}

class BusinessService
{
    public function execute(): void
    {
        // INCORRECT: Creating a service manually inside business logic
        // This service should have been injected into the constructor.
        $logger = new Logger();
        // ...
    }
}

try {
    $builder = new ContainerBuilder();
    $container = $builder->build();

    $service = $container->get(BusinessService::class);
    $service->execute();

    echo "SUCCESS (Wait, this is an ANTI-PATTERN!): Runtime 'new' bypassed the container." . PHP_EOL;

} catch (Throwable $e) {
    echo "INTENTIONAL FAILURE: " . $e->getMessage() . PHP_EOL;
}
