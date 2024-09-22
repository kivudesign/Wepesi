<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Routing\Providers\Contracts;

/**
 * @template T
 */
interface RouterContract
{
    /**
     * @param string $path
     * @param $callable
     * @param $name
     * @return RouteContract
     */
    public function get(string $path, $callable, $name = null): RouteContract;
    /**
     * @param string $path
     * @param $callable
     * @param $name
     * @return RouteContract
     */
    public function post(string $path, $callable, $name = null): RouteContract;

    /**
     * @param string $path
     * @param $callable
     * @param $name
     * @return RouteContract
     */
    public function put(string $path, $callable, $name = null): RouteContract;

    /**
     * @param string $path
     * @param $callable
     * @param $name
     * @return RouteContract
     */
    public function delete(string $path, $callable, $name = null): RouteContract;

    /**
     *
     */
    public function run();
}
