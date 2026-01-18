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
The `Compiler` is an optional build-time tool. It takes the validated `Registry` and generates a static PHP class. This generated class replaces the dynamic `Resolver` and `Inspector` in production, providing the highest possible performance without changing the container's behavior or strictness.
