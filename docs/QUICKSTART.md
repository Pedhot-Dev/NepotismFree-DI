# Quickstart

This guide provides the shortest correct path to using the NepotismFree DI container. Success requires adopting a strict, deterministic mental model.

## 1. Minimal Correct Usage

Configure your service graph and build the immutable container.

```php
use PedhotDev\NepotismFree\NepotismFree;

// 1. Initialize the builder
$builder = NepotismFree::createBuilder();

// 2. Define explicit bindings (Required for Interfaces)
$builder->bind(PaymentInterface::class, StripePayment::class);

// 3. Configure singletons
$builder->singleton(Logger::class);

// 4. Build the immutable container
$container = $builder->build();

// 5. Resolve your entry point (Concrete classes are auto-wired)
$app = $container->get(Application::class);
```

## 2. Intentional Failure Example

The container focuses on architectural correctness over convenience. If a dependency is missing or ambiguous, it fails immediately rather than guessing.

```php
// Interface without binding
interface Storage {}
class DiskStorage implements Storage {}

$builder = new ContainerBuilder();
$container = $builder->build();

// This will fail because the container refuses to guess which 
// implementation to use for the 'Storage' interface.
$container->get(Storage::class); // Throws DefinitionException
```

## Core Mental Model

1. **Build Once**: Configure everything in the `ContainerBuilder`, then `build()`.
2. **Inject Everything**: Dependencies must be declared in constructors.
3. **No Locators**: Never inject the container into your services.
4. **Be Explicit**: If you use an interface, you MUST provide a binding.
