<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use PedhotDev\NepotismFree\Builder\ContainerBuilder;

/**
 * ANTI-PATTERN: Ambiguous Binding Decision
 *
 * Why this is forbidden:
 * 1. Determinism requires an explicit implementation for every interface.
 * 2. If multiple implementation classes exist, the container refuses to pick one randomly.
 */

interface CacheInterface {}
class RedisCache implements CacheInterface {}
class ApcuCache implements CacheInterface {}

class Application
{
    // The container doesn't know which CacheInterface implementation to provide.
    public function __construct(
        private CacheInterface $cache
    ) {}
}

try {
    $builder = new ContainerBuilder();

    // INCORRECT: Requesting an interface without a supporting 'bind' call.
    // Even if RedisCache exists, the container will NOT automatically pick it
    // as the implementation for CacheInterface.
    $container = $builder->build();
    $container->get(Application::class);

} catch (Throwable $e) {
    echo "INTENTIONAL FAILURE: " . $e->getMessage() . PHP_EOL;
}
