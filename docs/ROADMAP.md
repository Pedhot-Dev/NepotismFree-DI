# Roadmap

The evolution of this project is guided by the core principles of strictness, determinism, and phase separation. We prioritize solidifying current guarantees over expanding the feature set.

## Current Guarantees

- **Strict Class/Interface Resolution**: Deterministic constructor injection.
- **Module Isolation**: Explicit service exposure and boundary enforcement.
- **Static Validation**: Detection of circular dependencies and missing bindings.
- **Production Optimization**: Optional compilation of the container to static PHP.

## Future Directions

### Short-Term (Stability & Clarity)
- **Enhanced Diagnostics**: Improving the clarity of error messages when validation fails (e.g., printing the full path of a circular dependency).
- **Documentation Expansion**: More "how-to" guides for common architectural patterns within the strict constraints.

### Medium-Term (Performance & Tooling)
- **AOT (Ahead-Of-Time) Validation CLI**: A standalone tool to validate the container configuration without running the full application.
- **Registry Serialization**: Exploring faster ways to load the `Registry` state without full re-configuration during development.

### Long-Term (Advanced Features - within strict limits)
- **Conditional Compilation**: Allowing different compiled versions based on environment (e.g., development vs. production) while maintaining the same dynamic behavior.
- **Type-Safe Factory Generation**: Improving the ergonomics of manually defined factories.

## Constraints on Future Goals

All future features must pass the "Strictness test":
1. Does it introduce magic or guesswork? (If yes, reject)
2. Does it allow runtime mutation? (If yes, reject)
3. Does it break the builder/runtime phase separation? (If yes, reject)
4. Does it encourage Service Location? (If yes, reject)

We believe that a smaller, stricter container is more valuable than a flexible, ambiguous one.
