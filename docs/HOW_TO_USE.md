# How To Use NepotismFree DI

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

// CHANGING LIFECYCLE
$builder->singleton(Database::class); // Shared instance
$builder->prototype(ReportGenerator::class); // New instance every time

// FINALIZING
$container = $builder->build();
```

## 3. Advanced V2 Features

### Contextual Bindings
Inject different implementations based on the consuming class.
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

## 4. Resolving Services
```php
$db = $container->get(Database::class);
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
