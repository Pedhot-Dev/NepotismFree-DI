<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Contract\ContainerInterface;

/**
 * ANTI-PATTERN: Service Locating via Container Injection
 *
 * Why this is forbidden:
 * 1. It hides the actual dependencies of the class.
 * 2. It couples the business logic to the DI container.
 * 3. It makes unit testing harder as you must mock the entire container.
 */

class ServiceLocatorUser
{
    // INCORRECT: Injecting the container implementation or interface
    public function __construct(
        private ContainerInterface $container
    ) {}

    public function doWork(): void
    {
        // INCORRECT: Fetching dependencies manually at runtime
        $mailer = $this->container->get('Mailer');
        $mailer->send('Hello');
    }
}

try {
    $builder = new ContainerBuilder();
    $builder->bind('Mailer', \stdClass::class);

    // This will fail because the Container cannot be injected into itself
    // and the architecture explicitly discourages this pattern.
    $container = $builder->build();
    $container->get(ServiceLocatorUser::class);

} catch (Throwable $e) {
    echo "INTENTIONAL FAILURE: " . $e->getMessage() . PHP_EOL;
}
