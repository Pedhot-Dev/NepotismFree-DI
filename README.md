# NepotismFree DI

**A High-Performance, Opinionated Dependency Injection Container for PHP 8.2+**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://www.php.net/releases/8.2/en.php)

## Philosophy

### What “NepotismFree” Means

NepotismFree rejects architectural privilege.

No dependency is allowed into the system just because it is:
- nearby
- guessable
- convenient
- or “usually works”

Every dependency must earn its place by being:
- explicitly declared
- unambiguous
- verifiable
- and rejectable

If the container cannot prove correctness, it fails.

---

### Explicit Over Magic

Most DI containers attempt to be helpful by guessing:
- which implementation to use
- which scalar value fits
- which fallback is “safe”

NepotismFree does none of this.

- Interfaces **never** resolve without bindings
- Scalars **never** resolve without explicit arguments
- Union / ambiguous types are rejected
- There are no silent defaults

If configuration is incomplete, the system fails fast.

---

### Fail Fast Is a Feature

A system that fails loudly is honest.  
A system that silently “works” is dangerous.

NepotismFree treats errors as **architectural signals**, not inconveniences.

Every failure:
- happens early
- explains the exact cause
- never masks another error

---

### No Service Locator. Ever.

Injecting the container is forbidden.

There is no global access.
There is no runtime resolution escape hatch.
There are no back doors.

If a service needs something, it must declare it explicitly in its constructor.

---

### Immutable by Design

The container lifecycle is strictly divided:

- **Builder phase** → configuration
- **Runtime phase** → resolution

Once built:
- no bindings can be added
- no lifecycles can be changed
- no tags can be modified

Mutation after build is a hard error.

---

## 📦 Example Usage

A complete, real-world example application is available in a separate repository:

👉 **[NepotismFree-DI Example – Notification System](https://github.com/Pedhot-Dev/NepotismFree-DI-Example)**

This example demonstrates:

- Proper constructor-based dependency injection
- Clean separation between Application, Domain, and Infrastructure layers
- Avoidance of service locator and closure-based wiring
- Honest adaptation to current container capabilities
- A realistic use case where DI is actually justified

The repository is intentionally kept small and focused, serving as a
**canonical reference** for how this container is meant to be used in practice.

> Note:  
> Some design choices (such as using a dedicated collection object)
> are deliberate trade-offs made to keep the application honest and
> aligned with the current DI container API.

---

## Installation

```bash
composer require pedhot-dev/nepotismfree-di
```

## Usage

### Bootstrapping

```php
use PedhotDev\NepotismFree\Builder\ContainerBuilder;

$builder = new ContainerBuilder();

// 1. Interface Binding
$builder->bind(LoggerInterface::class, FileLogger::class);

// 2. Singleton Definition (Default are Prototypes if not specified)
$builder->singleton(DatabaseConnection::class);

// 3. Scalar/Argument Binding
$builder->bindArgument(FileLogger::class, 'logPath', '/var/log/app.log');

// 4. Contextual Binding (V2)
$builder->bindContext(LoggerInterface::class, SysLogger::class, Database::class);

// 5. Tagging (V2)
$builder->tag('events', AppEventHandler::class);

// 6. Modules (V2)
$builder->addModule(new AuthModule());

// 7. Factory Binding (for complex construction)
$builder->bind(MailerInterface::class, function (ContainerInterface $c) {
    return new SmtpMailer($c->get(Config::class)->get('smtp'));
});

// Build and validate
$builder->validate();
$container = $builder->build();
```

### Resolution

```php
$logger = $container->get(LoggerInterface::class);

// Resolving tagged services
$handlers = $container->getTagged('events');
foreach ($handlers as $handler) {
    $handler->handle();
}
```

## Advanced Features

- **Compilation**: Lock your container for production by calling `$builder->compile($path)`.
- **Strict Modules**: Encapsulate logic. Only "exposed" services can be accessed from outside the module.
- **Fail-Fast Validation**: Call `$builder->validate()` to analyze the whole graph before the first service is ever resolved.

## Non-Goals

- **Service Location:** We do not encourage passing the container around.
- **Auto-Discovery:** We do not scan your filesystem for classes.
- **YAML/XML Config:** pure PHP configuration only.

## When NOT to use this

- If you want "rapid prototyping" where everything "just works" without config.
- If you rely heavily on "Autowiring" of scalar values by name guessing.
- If you need to mutate the container at runtime (e.g., during tests).

## License

MIT
