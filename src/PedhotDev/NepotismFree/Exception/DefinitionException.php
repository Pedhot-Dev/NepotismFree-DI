<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Exception;

/**
 * Thrown when a binding definition is invalid or configuration is malformed.
 */
class DefinitionException extends ContainerException
{
    public static function ambiguousType(string $service, string $paramName, string $type): self
    {
        return new self(sprintf(
            "Cannot resolve parameter '$%s' of type '%s' in class '%s'. No explicit binding provided.",
            $paramName,
            $type,
            $service
        ));
    }
    
    public static function untypedParameter(string $service, string $paramName): self
    {
        return new self(sprintf(
            "Parameter '$%s' in class '%s' has no type. Loose typing is forbidden.",
            $paramName,
            $service
        ));
    }

    public static function unresolvableBuiltin(string $service, string $paramName, string $type): self
    {
        return new self(sprintf(
            "Cannot resolve built-in type '%s' for parameter '$%s' in class '%s'. Explicit binding of scalar/built-in values is required.",
            $type,
            $paramName,
            $service
        ));
    }

    public static function containerInjectionForbidden(): self
    {
        return new self(
            "Injection of the Container is forbidden. Do not depend on the container at runtime to avoid the Service Locator anti-pattern."
        );
    }

    public static function builderIsImmutable(): self
    {
        return new self(
            "The builder is immutable. Mutation after build() is forbidden. All bindings must be defined before build()."
        );
    }

    public static function unsafeScopeInjection(string $consumer, string $dependency): self
    {
        return new self(sprintf(
            "Unsafe scope injection: Service '%s' (PROCESS scope) cannot depend on service '%s' (TICK scope). This would lead to stale instances and memory leaks in long-running processes.",
            $consumer,
            $dependency
        ));
    }
}
