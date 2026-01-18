# Core Concepts

## The Mental Model: Deterministic Construction

To use this container effectively, you must shift from a "dynamic runtime resolution" mindset to a "static build-time configuration" mindset. The container is a tool for constructing a fixed object graph, not a dynamic registry for looking up objects by name at runtime.

### Bind vs. Autowire

*   **Autowire (Default for Concrete Classes)**: When you request a concrete class that hasn't been explicitly bound, the container uses reflection to inspect its constructor and resolve its dependencies. This only works if all dependencies are themselves resolvable concrete classes or have explicit bindings.
*   **Bind (Required for Interfaces)**: An interface has no constructor and cannot be instantiated. You MUST bind it to a concrete implementation or a factory closure. The container refuses to "guess" which implementation to use if multiple exist.

### Interface vs. Concrete Class

Favor binding to interfaces in your application code to maintain decoupling. However, remember that the container requires an explicit decision for every interface. This explicitness prevents "accidental implementation" where a codebase depends on whatever class happens to be loaded first.

### Multiple Implementations & Explicit Decisions

If a project has multiple implementations of the same interface (e.g., `S3Storage` and `LocalStorage` both implementing `StorageInterface`), you must:
1.  Bind the interface to a default implementation. OR
2.  Use **Contextual Binding** to specify which implementation a specific consumer should receive.
The container will never provide a "random" implementation if the binding is missing.

### Why Runtime Resolution is Forbidden

Common DI containers allow you to fetch services from the container at any time (Service Location). This container forbids that pattern for three reasons:
1.  **Transparency**: When services are injected via constructors, a component's dependencies are visible in its signature.
2.  **Testability**: Constructor injection makes it trivial to mock dependencies during unit testing.
3.  **Graph Integrity**: Allowing runtime "gets" makes it impossible to statically validate the dependency graph, as the container doesn't know what might be requested later.

### Correct vs. Incorrect Thinking

| Incorrect Assumption (Common) | Correct Thinking (This Project) |
| :--- | :--- |
| "I can just get the container and fetch what I need." | "All my dependencies must be in my constructor." |
| "The container will find the implementation automatically." | "I must explicitly bind every interface I use." |
| "I'll add a setter for this optional dependency." | "Optional dependencies are constructor arguments with defaults." |
| "I can change a binding if I need a different mock now." | "The container is built once and is immutable thereafter." |
