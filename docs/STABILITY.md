# Stability Contract

## Project Version: 0.1.0 (Development)

This project is currently in early development (**pre-1.0**). This phase is dedicated to solidifying the architectural foundations, formalizing requirements, and refining the core resolution engine.

### 1. Stability Guarantees
There are **no stability guarantees** for the public API at this stage. Any component, interface, or class signature can change between minor or patch releases without prior notice.

### 2. Versioning Philosophy
Until the codebase reaches version 1.0.0, we follow a flexible development versioning approach. Incremental version numbers represent progress in development, but do not imply backward compatibility.

### 3. Public API vs. Internal Implementation

- **Public API (Unstable)**: 
    - `PedhotDev\NepotismFree\Builder\ContainerBuilder`
    - `PedhotDev\NepotismFree\Contract\ContainerInterface`
    - `PedhotDev\NepotismFree\Contract\ModuleInterface`
- **Internal (Subject to radical change)**:
    - Everything under `PedhotDev\NepotismFree\Core\*`
    - Everything under `PedhotDev\NepotismFree\Component\*`
    - Internal exception handling and diagnostic logic.

### 4. Migration Paths
There are **no documented migration paths** for pre-1.0 versions. Users are expected to track changes and adjust their implementations manually when updating. This strictness ensures that we can prioritize architectural correctness over legacy support during this critical development phase.
