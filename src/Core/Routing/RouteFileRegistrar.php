<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Routing;


use Wepesi\Core\Routing\Providers\Contracts\RouterContract;
use Wepesi\Core\Routing\Providers\Contracts\RouterProviders;

/**
 * @template T
 * @template-implements RouterProviders<T>
 */
final class RouteFileRegistrar implements RouterProviders
{
    /**
     * @var RouterContract
     */
    protected RouterContract $router;

    /**
     * Create a new route file registrar instance.
     *
     * @param Router $router
     */
    public function __construct(RouterContract $router)
    {
        $this->router = $router;
    }

    /**
     * Require the given routes file.
     *
     * @param class-string<T> $routes
     * @return void
     */
    public function register(string $routes): void
    {
        $router = $this->router;
        if (is_file($routes)) {
            require $routes;
        }
    }
}