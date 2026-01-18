# Architectural Decisions (ADR)

This document records the key architectural decisions that define the constraints and behavior of the container.

## 1. Constructor-Only Injection

- **Decision**: Only constructor injection is supported. Property and setter injection are explicitly excluded.
- **Rationale**: Constructor injection ensures that a component cannot be instantiated in an incomplete or invalid state. It makes dependencies explicit and transparent in the class signature.
- **Alternatives Rejected**: 
    - *Setter Injection*: Rejected due to risk of "temporal coupling" (calling methods in the wrong order) and incomplete object states.
    - *Property Injection*: Rejected because it requires reflection-based modification of private state and hides dependencies.

## 2. No Service Locator Pattern

- **Decision**: The container explicitly forbids being injected into managed services and detects such attempts at runtime.
- **Rationale**: Injecting the container (Service Locating) hides true dependencies and creates a hard dependency on the DI infrastructure.
- **Alternatives Rejected**: 
    - *Providing a ContainerInterface binding*: Rejected to enforce architectural decoupling.

## 3. Deterministic Resolution (No Magic)

- **Decision**: Ambiguous dependencies (unbound interfaces or missing scalar values) result in immediate failure.
- **Rationale**: "Magic" fallbacks or guessing intent lead to non-deterministic systems that are difficult to debug and maintain.
- **Alternatives Rejected**: 
    - *Auto-resolving interfaces to the first discovered implementation*: Rejected for being non-deterministic.

## 4. Phase Separation (Build vs. Runtime)

- **Decision**: The project strictly separates the configuration phase (`ContainerBuilder`) from the execution phase (`Container`).
- **Rationale**: Immutability at runtime ensures predictability and performance. It allows for optional ahead-of-time (AOT) compilation.
- **Alternatives Rejected**: 
    - *Allowing runtime binding updates*: Rejected to preserve graph integrity and performance.

## 5. Optional Compilation

- **Decision**: The container can be optionally compiled into a static PHP file.
- **Rationale**: Performance is a priority for production environments, but the complexity of a forced compile step should be avoided during development.
- **Alternatives Rejected**: 
    - *Mandatory dynamic-only resolution*: Rejected due to reflection overhead in high-traffic applications.
