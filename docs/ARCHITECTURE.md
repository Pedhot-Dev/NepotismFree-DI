# Architecture

## Philosophy: Strict by Design

This container is built on the principle that **explicitness is better than magic**. Conventional DI containers often prioritize developer convenience at the cost of architectural clarity and determinism. This project rejects that trade-off by enforcing strict rules that make the dependency graph predictable and verifiable.

## Core Design Goals

### 1. Determinism
Every resolution must be deterministic. Given a specific configuration, the container will always produce the same object graph. There are no runtime-dependent fallbacks or random selections for ambiguous dependencies.

### 2. Explicitness
All dependencies must be explicitly defined or follow strict autowiring rules for concrete classes. The container will never "guess" your intent. If a dependency is missing or ambiguous, the system fails immediately.

### 3. Compile-ability
The architecture separates graph construction from service resolution. This separation allows the entire container to be compiled into a flat PHP file, eliminating reflection overhead and graph traversal at runtime.

## Non-Goals

- **No Magic**: No property injection, no setter injection, and no auto-resolution of interfaces to random implementations.
- **No Service Locator**: The container is NOT a service locator and should never be injected into managed services.
- **No Runtime Mutation**: Once built, the container is immutable. New bindings or configuration changes are forbidden at runtime.

## Scope Model: Lifecycle Management
To support long-running processes (e.g., game servers, workers), the container implements a multi-tier scope model:
1. **PROCESS**: Global singletons, shared for the entire lifetime of the process.
2. **TICK**: Scoped services, shared within a logical execution cycle (managed by `ScopeManager`).
3. **PROTOTYPE**: Transient services, a new instance is created on every request.

The system enforces **Scope Safety**: a service with a wider scope cannot depend on a service with a narrower scope (e.g., a `PROCESS` singleton cannot depend on a `TICK` service), preventing memory leaks and stale instance bugs.

## Lifecycle: Build Phase vs. Runtime Phase

The system distinguishes between two distinct phases of operation:

### Build Phase (Mutable)
*   **Responsibility**: Collection service definitions, configuring modules, and performing static analysis.
*   **Components**: `ContainerBuilder`, `Registry`, `Validator`.
*   **Outcome**: A validated dependency graph ready for resolution.

### Runtime Phase (Immutable)
*   **Responsibility**: Instantiating services and enforcing access boundaries.
*   **Components**: `Container`, `Resolver`, `ModuleAccessPolicy`.
*   **Outcome**: Reliable, high-performance service delivery.

## The Compiler
The `Compiler` is a build-time tool that generates a semantic-preserving PHP container. Unlike simplistic compilers, this one:
- **Eliminates Reflection**: Generates exact `new` calls for all services.
- **Bakes Context**: Pre-resolves contextual and argument bindings at compile time.
- **Preserves DI Semantics**: Handles recursion, scopes, and group resolution (tags) identically to the runtime container.
- **Supports Factories**: Safely integrates runtime factory closures via constructor injection.
