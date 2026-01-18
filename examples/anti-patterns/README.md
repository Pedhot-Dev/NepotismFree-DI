# Anti-Patterns

This directory contains examples of **incorrect** usage of the NepotismFree DI container. These examples are designed to fail, illustrating the architectural boundaries and strictness of the system.

## Purpose of these Examples

To use a strict DI container effectively, developers often need to "unlearn" habits from more permissive frameworks. These anti-patterns serve as a reference for what the container **refuses** to do by design.

## Why these patterns are forbidden

1.  **Service Locating**: Injecting the container (or a container-like factory) into your services hides their dependencies and couples your business logic to the DI infrastructure.
2.  **Ambiguity**: Allowing the container to "guess" which implementation to use leads to non-deterministic behavior and "surprise" errors in production.
3.  **Bypassing the Graph**: Manually instantiating services (`new`) inside components that are managed by the container makes it impossible to statically analyze the dependency tree and mocks for testing.
4.  **Incomplete Definitions**: The container requires every required dependency to be fully resolvable. Missing scalar values or unbound interfaces result in immediate failure to preserve graph integrity.

**These failures are intentional and desirable.** They force architectural clarity and prevent the system from degrading into a "magic" dependency solver.
