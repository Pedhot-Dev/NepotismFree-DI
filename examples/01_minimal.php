<?php

require __DIR__ . '/../vendor/autoload.php';

use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Contract\ContainerInterface;

// --- Definitions ---

interface GreeterInterface
{
    public function greet(string $name): string;
}

class EnglishGreeter implements GreeterInterface
{
    public function greet(string $name): string
    {
        return "Hello, $name!";
    }
}

class App
{
    public function __construct(private GreeterInterface $greeter) {}

    public function run(): void
    {
        echo $this->greeter->greet("World") . PHP_EOL;
    }
}

// --- Bootstrap ---

try {
    $builder = new ContainerBuilder();

    // Explicit binding
    $builder->bind(GreeterInterface::class, EnglishGreeter::class);

    // Build container
    $container = $builder->build();

    // Resolve App (Auto-wired concrete class)
    $app = $container->get(App::class);
    $app->run();

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
