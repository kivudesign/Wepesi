<?php

namespace Wepesi\Core\DI;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use Wepesi\Core\DI\Contracts\ContainerContracts;

class Container implements ContainerContracts
{
    /**
     * @var array<string, mixed>
     */
    private array $bindings = [];

    /**
     * @var array<string, mixed>
     */
    private array $instances = [];

    /**
     * @var array<int, string>
     */
    private array $resolutionStack = [];

    /**
     * Register a service factory or implementation.
     *
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @return void
     */
    public function bind(string $abstract, Closure|string|null $concrete = null): void
    {
        $this->bindings[$abstract] = $concrete ?? $abstract;
    }

    /**
     * Register a shared singleton service.
     *
     * @param string $abstract
     * @param Closure|string|null $concrete
     *
     * @return void
     */
    public function singleton(string $abstract, Closure|string|null $concrete = null): void
    {
        $this->bindings[$abstract] = function (ContainerContracts $container, array $parameters = []) use ($abstract, $concrete) {
            if (!isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $container->build($concrete ?? $abstract, $parameters);
            }

            return $this->instances[$abstract];
        };
    }

    /**
     * Register an already-created instance.
     *
     * @param string $abstract
     * @param mixed $instance
     * @return void
     */
    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve a service from the container.
     *
     * @template T
     * @param class-string<T>|string $abstract
     * @param array<int|string, mixed> $parameters
     *
     * @return T|mixed
     */
    public function get(string $abstract, array $parameters = []): mixed
    {
        if (empty($parameters) && isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            return $this->build($abstract, $parameters);
        }

        $concrete = $this->bindings[$abstract];

        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        return $this->build($concrete, $parameters);
    }

    /**
     * Alias for get().
     *
     * @param string $abstract
     * @param array<int|string, mixed> $parameters
     *
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        return $this->get($abstract, $parameters);
    }

    /**
     * Check if a service has been registered.
     *
     * @param string $abstract
     * @return bool
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Build an object using reflection and constructor injection.
     *
     * @param Closure|string $concrete
     * @param array<int|string, mixed> $parameters
     *
     * @return mixed
     */
    public function build(Closure|string $concrete, array $parameters = []): mixed
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        $this->guardAgainstCircularDependency($concrete);
        $this->resolutionStack[] = $concrete;

        if (!class_exists($concrete)) {
            throw new ContainerException("Class [$concrete] does not exist.");
        }
        try {


            try {
                $reflection = new ReflectionClass($concrete);
            } catch (ReflectionException $exception) {
                throw new ContainerException($exception->getMessage(), 0, $exception);
            }

            if (!$reflection->isInstantiable()) {
                throw new ContainerException("Class [$concrete] is not instantiable.");
            }

            $constructor = $reflection->getConstructor();

            if ($constructor === null) {
                $instance = new $concrete();

                $this->initializeInstance($instance);

                return $instance;
            }

            $dependencies = $this->resolveDependencies(
                $constructor->getParameters(),
                $parameters,
                $concrete
            );

            $instance = $reflection->newInstanceArgs($dependencies);

            $this->initializeInstance($instance);

            return $instance;

        } finally {
            array_pop($this->resolutionStack);
        }
    }

    /**
     * Initialize a newly created object if it supports framework initialization.
     *
     * @param object $instance
     * @return void
     */
    private function initializeInstance(object $instance): void
    {
        if (method_exists($instance, '__wepesiInit')) {
            $instance->__wepesiInit();
        }
    }

    /**
     * Call a callable while resolving class-typed parameters.
     *
     * @param callable|array|string $callable
     * @param array<int|string, mixed> $parameters
     * @return mixed
     */
    public function call(callable|array|string $callable, array $parameters = []): mixed
    {
        if (is_string($callable) && str_contains($callable, '#')) {
            [$class, $method] = explode('#', $callable, 2);
            $callable = [$this->get($class), $method];
        }

        if (is_array($callable) && is_string($callable[0])) {
            $callable[0] = $this->get($callable[0]);
        }

        if (!is_callable($callable)) {
            throw new ContainerException('The provided value is not callable.');
        }

        try {
            $reflection = is_array($callable)
                ? new ReflectionMethod($callable[0], $callable[1])
                : new ReflectionFunction($callable);
        } catch (ReflectionException $exception) {
            throw new ContainerException($exception->getMessage(), 0, $exception);
        }

        $dependencies = $this->resolveDependencies(
            $reflection->getParameters(),
            $parameters
        );

        return call_user_func_array($callable, $dependencies);
    }

    /**
     * Resolve constructor, function, or method dependencies.
     *
     * @param array<int, \ReflectionParameter> $reflectionParameters
     * @param array<int|string, mixed> $parameters
     * @param string|null $concrete
     * @return array<int, mixed>
     */
    private function resolveDependencies(array $reflectionParameters, array $parameters = [], ?string $concrete = null): array
    {
        $dependencies = [];

        foreach ($reflectionParameters as $index => $parameter) {
            if (array_key_exists($parameter->getName(), $parameters)) {
                $dependencies[] = $parameters[$parameter->getName()];
                continue;
            }

            if (array_key_exists($index, $parameters)) {
                $dependencies[] = $parameters[$index];
                continue;
            }

            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $dependencies[] = $this->get($type->getName());
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
                continue;
            }

            $target = $concrete ? " for class [$concrete]" : '';

            throw new ContainerException(
                "Unable to resolve parameter [{$parameter->getName()}]$target."
            );
        }

        return $dependencies;
    }

    /**
     * Detect circular dependencies before resolving a class.
     *
     * @param string $concrete
     * @return void
     */
    private function guardAgainstCircularDependency(string $concrete): void
    {
        if (!in_array($concrete, $this->resolutionStack, true)) {
            return;
        }

        $cycle = array_merge(
            array_slice(
                $this->resolutionStack,
                array_search($concrete, $this->resolutionStack, true)
            ),
            [$concrete]
        );

        throw new ContainerException(
            'Circular dependency detected: ' . implode(' -> ', $cycle)
        );
    }
}
