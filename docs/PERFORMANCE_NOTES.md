# Performance Notes

## Reflection Cost Analysis
NepotismFree DI uses `ReflectionClass` to inspect constructors. Reflection in PHP (especially 8.2+) is fast, but not free.
- **First Resolution**: ~0.1-0.2ms overhead per class to inspect dependencies.
- **Subsequent Resolutions**: We cache `ReflectionClass` instances in `Resolver::$reflectionCache`, reducing overhead to near-zero for repeated inspections of PROTOTYPE definitions.
- **Singletons**: Once resolved, the instance is cached in `Container::$instances` array. Access is `O(1)` array lookup.

## Trade-offs
1.  **Strictness vs Speed**: We deliberately check for circular dependencies using recursion tracking (`$this->building`). This adds a small array manipulation overhead but guarantees deterministic failure on infinite loops.
2.  **Runtime vs Compiled**: This is a *runtime* container. It does not dump a PHP file (like Symfony `ContainerBuilder` -> `ProjectServiceContainer`).
    - *Pro*: No build step, immediate feedback during development.
    - *Con*: "Cold boot" performance is slower than a hard-coded factory array.
    - *Mitigation*: For long-running processes (Swoole, RoadRunner, Workers), the boot cost is paid once at startup, making runtime reflection irrelevant for request processing (assuming services are Singletons).

## Optimizations
- **Singleton Caching**: We check the singleton cache *before* any other logic.
- **Argument Map**: `Registry` uses simple arrays (`$arguments[$class][$param]`) which are highly optimized in PHP.
- **Avoidance of Magic**: using `__get` or `__call` is slow. We use explicit methods `get()` and `bind()`.
