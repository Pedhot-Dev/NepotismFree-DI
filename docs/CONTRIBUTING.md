# Contributing Guidelines

Thank you for considering contributing to this project. To maintain the project's architectural integrity, all contributors must adhere to the following principles.

## Core Architectural Principles

Any proposed change must respect these non-negotiable constraints:

1.  **Immutability**: The container must remain immutable after the `build()` call.
2.  **Determinism**: Resolution logic must be predictable and side-effect free.
3.  **Explicitness**: No features that introduce "magic" resolution or silent fallbacks will be accepted.
4.  **Constructor-Only**: No support for setter, property, or private injection will be added.
5.  **Phase Separation**: Keep a clear distinction between build-time (configuration) and runtime (resolution).

## Forbidden Features

The following features will be automatically rejected:
- **Lazy Loading / Proxy generation**: We prefer explicit graph design over hidden runtime proxies.
- **Service Locator Support**: Features that make it easier to inject the container into services.
- **Optional Dependencies via Setters**: All dependencies must be visible in the constructor.
- **Auto-discovery of Implementations**: We require explicit binding for interfaces.
- **Dynamic Definition Re-binding**: Changing service definitions after construction.

## Testing Requirements

Every bug fix or feature must include a unit test that asserts:
- The intended behavior.
- Failure cases (negative tests are as important as positive ones).
- Architectural invariants (e.g., ensuring a new component doesn't break circular dependency detection).

Tests should be failure-oriented and assert only one invariant per test case.

## Code Quality

- **Type Safety**: Strictly typed PHP 8.1+ features must be used everywhere.
- **Clarity over Cleverness**: Code should be readable and explain its intent through naming and structure rather than complex comments.
- **Zero Dependencies**: The core container must not have any external dependencies (composer `require` section for production).
