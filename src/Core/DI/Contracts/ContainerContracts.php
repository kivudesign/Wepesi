<?php
/*
 * Copyright (c) 2026. Wepesi Dev Framework
 */

namespace Wepesi\Core\DI\Contracts;

use Closure;

interface ContainerContracts
{
    /**
     * Register a binding in the container.
     *
     * A binding maps an abstract type or identifier to a concrete implementation.
     * The concrete value may be a class name, a factory closure, or null. When null
     * is provided, the abstract type is used as the concrete implementation.
     *
     * @param string $abstract The abstract type, interface, class name, or service identifier.
     * @param Closure|string|null $concrete The concrete implementation or factory closure.
     *
     * @return void
     */
    public function bind(string $abstract, Closure|string|null $concrete = null): void;

    /**
     * Register a shared binding in the container.
     *
     * A singleton binding is resolved only once. The same resolved instance will be
     * returned every time the abstract type is requested from the container.
     *
     * @param string $abstract The abstract type, interface, class name, or service identifier.
     * @param Closure|string|null $concrete The concrete implementation or factory closure.
     *
     * @return void
     */
    public function singleton(string $abstract, Closure|string|null $concrete = null): void;

    /**
     * Register an existing instance in the container.
     *
     * The provided instance will be stored directly and returned whenever the
     * abstract type is requested.
     *
     * @param string $abstract The abstract type, interface, class name, or service identifier.
     * @param mixed $instance The already-created instance to store in the container.
     *
     * @return void
     */
    public function instance(string $abstract, mixed $instance): void;

    /**
     * Resolve an abstract type from the container.
     *
     * This method retrieves the concrete instance associated with the given
     * abstract type or service identifier.
     *
     * @param string $abstract The abstract type, interface, class name, or service identifier.
     * @param array<int|string, mixed> $parameters
     *
     * @return mixed The resolved service instance.
     */
    public function get(string $abstract, array $parameters = []): mixed;

    /**
     * Resolve an abstract type from the container.
     *
     * This method creates or retrieves an instance for the given abstract type.
     * If the type has been registered as a singleton or instance, the shared
     * instance should be returned.
     *
     * @param string $abstract The abstract type, interface, class name, or service identifier.
     * @param array<int|string, mixed> $parameters
     *
     * @return mixed The resolved service instance.
     */
    public function make(string $abstract, array $parameters = []): mixed;

    /**
     * Determine whether an abstract type is registered in the container.
     *
     * This checks if the container has a binding, singleton, or instance registered
     * for the given abstract type or service identifier.
     *
     * @param string $abstract The abstract type, interface, class name, or service identifier.
     *
     * @return bool True if the abstract type is registered, false otherwise.
     */
    public function has(string $abstract): bool;

    /**
     * Build a concrete implementation.
     *
     * The concrete value may be a class name or a factory closure. If a closure is
     * provided, it should be executed to produce the instance. If a class name is
     * provided, the container should attempt to instantiate it.
     *
     * @param Closure|string $concrete The concrete class name or factory closure to build.
     * @param array<int|string, mixed> $parameters
     *
     * @return mixed The built instance or resolved value.
     */

    public function build(Closure|string $concrete, array $parameters = []): mixed;

    /**
     * Call a callable while resolving class-typed parameters.
     *
     * @param callable|array|string $callable
     * @param array<int|string, mixed> $parameters
     *
     * @return mixed
     */

    public function call(callable|array|string $callable, array $parameters = []): mixed;
}