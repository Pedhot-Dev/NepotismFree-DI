# How To Use NepotismFree DI

## 0. Strict Architectural Rules
This container is designed for production-grade, long-running systems. It enforces strict invariants to guarantee safety and determinism.

- **No Container Injection**: You MUST NOT inject `Container`, `ContainerInterface`, `ScopedContainer`, or `ScopeManager` into your services. This prevents the Service Locator anti-pattern.
- **Scope Safety**: A service with a wider scope (e.g., `PROCESS`) CANNOT depend on a service with a narrower scope (e.g., `TICK`). This prevents memory leaks and stale data.
- **Module Privacy**: Services bound within a Module are PRIVATE by default. They can only be accessed by other services in the same module unless explicitly added to `getExposedServices()`.
- **Immutability**: Once `build()` is called, the container and its configuration are locked.

---

## 1. Installation
```bash
composer require pedhot-dev/nepotismfree-di
```

## 2. Bootstrapping
You must configure the container *before* using it.

```php
use PedhotDev\NepotismFree\NepotismFree;

$builder = NepotismFree::createBuilder();

// BINDING INTERFACES
$builder->bind(MyInterface::class, MyImplementation::class);

// BINDING SCALARS (Mandatory for primitives)
$builder->bindArgument(Database::class, 'host', 'localhost');
$builder->bindArgument(Database::class, 'port', 3306);

// CHANGING LIFECYCLE (SCOPES)
$builder->singleton(Database::class); // PROCESS scope: Shared for entire process
$builder->scoped(Session::class);    // TICK scope: Shared within one logical execution cycle
$builder->prototype(Report::class);   // PROTOTYPE scope: New instance every time

// FINALIZING
$container = $builder->build();
```

## 3. Advanced V2 Features

### Contextual Bindings (Propagated)
Inject different implementations based on the consuming class. Deep propagation is supported: if class A depends on B, and B depends on C, a contextual binding for C defined for A will apply even when B is resolving C.
```php
$builder->bindContext(LoggerInterface::class, SysLogger::class, Database::class);
```

### Tagging & Group Resolution
Resolve multiple services under a single tag.
```php
$builder->tag('plugin', MyPlugin::class);
$builder->tag('plugin', OtherPlugin::class);

$plugins = $container->getTagged('plugin'); // Generator
```

### Strict Modules
Isolate groups of services.
```php
class AuthModule implements ModuleInterface {
    public function configure(ContainerBuilder $builder): void {
        $builder->bind(InternalHelper::class, InternalHelper::class);
        $builder->bind(AuthService::class, AuthService::class);
    }
    public function getExposedServices(): array {
        return [AuthService::class]; // InternalHelper is HIDDEN
    }
}
$builder->addModule(new AuthModule());
```

## 4. Scoped Execution (Long-running processes)
Use `ScopeManager` to handle logical execution cycles (ticks/requests) safely.

```php
use PedhotDev\NepotismFree\Core\ScopeManager;
use PedhotDev\NepotismFree\Contract\Scope;

$scopeManager = new ScopeManager($container);

// Every 'run' creates a fresh child container for the TICK scope
$scopeManager->run(Scope::TICK, function($scopedContainer) {
    $session = $scopedContainer->get(Session::class);
    // ... do work ...
}); // $session and all TICK scoped services are disposed here
```

## 5. Compiling for Production
Eliminate reflection and speed up resolution.

```php
$builder->compile('/path/to/CompiledContainer.php', 'MyContainer');

// Usage
require '/path/to/CompiledContainer.php';
$container = new \PedhotDev\NepotismFree\Compiled\MyContainer([
    // Pass factory closures for services that use them
    'factory_service_id' => fn($c) => new Service(),
]);
```

## 5. Common Mysteries & Failures
"Fail Fast" means you will see errors. Here is how to fix them:

- **"Parameter '$limit' in class 'Search' has no type."**
  - **Fix**: Add a type hint to your constructor. `__construct($limit)` -> `__construct(int $limit)`.

- **"Cannot resolve built-in type 'int' for parameter '$port'..."**
  - **Fix**: You must bind this argument explicitly.
  - `$builder->bindArgument(Service::class, 'port', 8080);`

- **"Circular dependency detected: A -> B -> A"**
  - **Fix**: You have a logic loop. Refactor one class to not need the other in the constructor. Use a setter or a proxy if absolutely necessary (but we don't support automatic proxies, so fix your design!).

- **"Service 'FooInterface' not found."**
  - **Fix**: We do not auto-guess implementations. Bind it: `$builder->bind(FooInterface::class, FooConcrete::class);`.

- **"Unsafe scope injection: Service 'A' (PROCESS) cannot depend on 'B' (TICK)"**
  - **Fix**: You are trying to inject a short-lived service into a long-lived singleton. This is a design flaw that leads to memory leaks. Pass the data explicitly or use a `PROTOTYPE` for 'B' if appropriate.

- **"Service 'X' is internal to module 'M' and cannot be accessed from the outside."**
  - **Fix**: You are violating a module boundary. Either expose 'X' in Module 'M''s `getExposedServices()` or refactor your code to depend on an exposed interface.
