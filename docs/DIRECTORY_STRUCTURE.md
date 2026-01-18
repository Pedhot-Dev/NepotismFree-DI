```text
.
├── src
│   └── PedhotDev
│       └── NepotismFree
│           ├── Builder
│           │   └── ContainerBuilder.php
│           ├── Component
│           │   ├── Compiler
│           │   │   └── Compiler.php
│           │   ├── Inspector
│           │   │   └── Inspector.php
│           │   └── Validation
│           │       └── Validator.php
│           ├── Contract
│           │   ├── Exception
│           │   │   ├── ContainerExceptionInterface.php
│           │   │   └── NotFoundExceptionInterface.php
│           │   ├── ContainerInterface.php
│           │   ├── ModuleConfiguratorInterface.php
│           │   └── ModuleInterface.php
│           ├── Core
│           │   ├── Container.php
│           │   ├── ModuleAccessPolicy.php
│           │   ├── Registry.php
│           │   └── Resolver.php
│           ├── Exception
│           │   ├── CircularDependencyException.php
│           │   ├── ContainerException.php
│           │   ├── DefinitionException.php
│           │   ├── ModuleBoundaryException.php
│           │   └── NotFoundException.php
│           └── NepotismFree.php
├── tests
│   └── Unit
├── composer.json
├── composer.lock
├── LICENSE
├── phpunit.xml
├── README.md
└── virion.yml
```

## Directory & Component Responsibilities

### src/PedhotDev/NepotismFree/Builder
**Responsibility**: Build-time / Public API
Contains the logic for assembling the container configuration.
*   `ContainerBuilder.php`: The primary entry point for configuring bindings, singletons, and modules. It orchestrates the transition from configuration to a finalized `Container`.

### src/PedhotDev/NepotismFree/NepotismFree.php
**Responsibility**: Public / Entry Point
A static factory class for creating `ContainerBuilder` instances.

### src/PedhotDev/NepotismFree/Component
**Responsibility**: Build-time / Internal Infrastructure
Specific tools used during the construction and validation of the container graph.
*   `Compiler.php`: Transforms the in-memory `Registry` into an optimized static PHP class for production.
*   `Inspector.php`: Uses reflection to analyze class constructors and determine dependency requirements.
*   `Validator.php`: Performs static analysis on the `Registry` to detect circular dependencies and missing bindings.

### src/PedhotDev/NepotismFree/Contract
**Responsibility**: Shared / Public API
Defines the interfaces for interoperability and module encapsulation.
*   `ContainerInterface.php`: The standard runtime interface for service retrieval.
*   `ModuleInterface.php`: Interface for defining encapsulated service groups (modules).
*   `ModuleConfiguratorInterface.php`: A restricted subset of the builder API specifically for module configuration.

### src/PedhotDev/NepotismFree/Core
**Responsibility**: Runtime & Storage / Internal
The foundational engine of the DI system.
*   `Container.php`: The immutable runtime object that provides access to services.
*   `Resolver.php`: The engine responsible for the deterministic instantiation of services based on the `Registry`.
*   `Registry.php`: The underlying data store for all service definitions and container state.
*   `ModuleAccessPolicy.php`: Enforces architectural boundaries by controlling which services are visible across module lines.

### src/PedhotDev/NepotismFree/Exception
**Responsibility**: Shared / Public API
Domain-specific exceptions representing intentional failure modes of the container.
