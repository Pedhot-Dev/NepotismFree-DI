# Failure Modes

This container is designed to "fail loud and fail fast". We prefer an immediate, clear exception over silent misbehavior or unpredictable runtime defaults.

## Intentional Failure Cases

### 1. Circular Dependency
*   **Condition**: Service A depends on Service B, which depends on Service A (directly or indirectly).
*   **Exception**: `PedhotDev\NepotismFree\Exception\CircularDependencyException`
*   **Rationale**: Circular dependencies indicate a flaw in application design. Solving them via "lazy proxies" or setter injection often hides deeper architectural issues. Explicit failure forces a cleaner design.

### 2. Missing Binding for Interface
*   **Condition**: A service depends on an interface, but no implementation has been bound to it.
*   **Exception**: `PedhotDev\NepotismFree\Exception\DefinitionException`
*   **Rationale**: Determinism requires an explicit choice. Providing a "default" implementation automatically would violate the principle of explicitness.

### 3. Ambiguous Dependency
*   **Condition**: Multiple implementations exist for a dependency, and the container cannot determine which one to inject for a specific consumer.
*   **Exception**: `PedhotDev\NepotismFree\Exception\DefinitionException`
*   **Rationale**: Guessing which service to inject leads to non-deterministic behavior that is difficult to debug.

### 4. Module Boundary Violation
*   **Condition**: Service A (in Module 1) attempts to resolve Service B (in Module 2), but Service B has not been explicitly marked as "exposed" in its module definition.
*   **Exception**: `PedhotDev\NepotismFree\Exception\ModuleBoundaryException`
*   **Rationale**: Enforces encapsulation and "Internal" service privacy. This prevents leaked implementation details from becoming public APIs.

### 5. Runtime Modification Attempt
*   **Condition**: Attempting to add a binding or configure a module after the container has been built (`$builder->build()`).
*   **Exception**: `PedhotDev\NepotismFree\Exception\DefinitionException`
*   **Rationale**: Ensures container immutability. Once the runtime phase begins, the dependency graph is locked.

### 6. Invalid Scalar Argument
*   **Condition**: An required scalar argument (string, int, bool) is missing from the configuration.
*   **Exception**: `PedhotDev\NepotismFree\Exception\DefinitionException`
*   **Rationale**: Prevents services from being initialized with `null` or uninitialized states that they are not designed to handle.

## Why Failing Fast is Correct
Failing during the **Build Phase** is always preferred over failing during the **Runtime Phase**. By validating the entire graph before the container is returned, we ensure that if a container is successfully created, it is structurally sound. This significantly reduces "surprise" errors in production.
