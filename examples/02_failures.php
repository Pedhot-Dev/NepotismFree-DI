<?php

require __DIR__ . '/../vendor/autoload.php';

use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Exception\ContainerException;

// --- Definitions ---

class Config
{
    public function __construct(public string $dbHost) {}
}

class CircularA
{
    public function __construct(CircularB $b) {}
}

class CircularB
{
    public function __construct(CircularA $a) {}
}

// --- Scenarios ---

$builder = new ContainerBuilder();
$container = $builder->build();

echo "--- Scenario 1: Missing Scalar Binding ---" . PHP_EOL;
try {
    $container->get(Config::class);
} catch (ContainerException $e) {
    echo "Caught Strict Exception: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "--- Scenario 2: Circular Dependency ---" . PHP_EOL;
try {
    $container->get(CircularA::class);
} catch (ContainerException $e) {
    echo "Caught Strict Exception: " . $e->getMessage() . PHP_EOL;
}
