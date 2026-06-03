<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core\Routing\Traits;

use InvalidArgumentException;
use Wepesi\Core\Application;
use Wepesi\Core\Http\Response;

/**
 *
 */
trait routeBuilder
{
    /**
     * @param $callable
     * @param array $matches
     * @param bool $is_middleware
     * @return void
     * @link http://github.com/kivudesign/Wepesi
     */
    protected function routeFunctionCall($callable, array $matches = [], bool $is_middleware = false): void
    {
        $class_element = !$is_middleware ? 'controller' : 'middleware';
        try{
        if (is_string($callable) || is_array($callable)) {
            $callable_params = is_string($callable) ? explode('#', $callable) : $callable;
            if (count($callable_params) != 2) {
                throw new InvalidArgumentException("Error : on `$class_element` class/method is not well defined");
            }

            $class_name = $callable_params[0];
            $class_method_name = $callable_params[1];
            if (!class_exists($class_name, true)) {
                throw new InvalidArgumentException("$class_name class not defined, not a valid $class_element", 500);
            }

            Application::container()->call([$class_name, $class_method_name], $matches);
        } else if (isset($callable) && is_callable($callable, true)) {
            Application::container()->call($callable, $matches);
        }
        }catch (InvalidArgumentException $exception){
            Response::setStatusCode(500);
            error_log($exception->getMessage(), $exception->getCode());
            throw new InvalidArgumentException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param $controller
     * @param array $matches
     * @return void
     */
    protected function executeController($controller, array $matches = []): void
    {
        $this->routeFunctionCall($controller, $matches);
    }

    /**
     * @param $middleware
     * @param array $matches
     * @return void
     */
    protected function executeMiddleware($middleware, array $matches = []): void
    {
        $this->routeFunctionCall($middleware, $matches, true);
    }

}