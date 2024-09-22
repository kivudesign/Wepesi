<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core\Routing\Traits;

use Wepesi\Core\Application;

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
     */
    protected function routeFunctionCall($callable, array $matches = [], bool $is_middleware = false): void
    {
        $class_element = !$is_middleware ? 'controller' : 'middleware';
        try {
            if (is_string($callable) || is_array($callable)) {
                $callable_params = is_string($callable) ? explode('#', $callable) : $callable;
                if (count($callable_params) != 2) {
                    throw new \InvalidArgumentException("Error : on `$class_element` class/method is not well defined");
                }

                $class_name = $callable_params[0];
                $class_method_name = $callable_params[1];
                if (!class_exists($class_name, true)) {
                    throw new \InvalidArgumentException("$class_name class not defined, not a valid $class_element", 500);
                }

                $reflection = new \ReflectionClass($class_name);
                $class_instance = $reflection->newInstance();

                if (!$reflection->isInstance($class_instance)) {
                    throw new \ReflectionException('Only instantiable class can be used. Not abstract,interface, or trait can be used.');
                }
                if (!method_exists($class_instance, $class_method_name)) {
                    throw new \BadMethodCallException("method : $class_method_name does not belong the class : $class_name.", 500);
                }
                call_user_func_array([$class_instance, $class_method_name], $matches);
            } else if (isset($callable) && is_callable($callable, true)) {
                call_user_func_array($callable, $matches);
            }
            return;
        } catch (\Exception $ex) {
            Application::dumper($ex);
        }
    }

    protected function executeController($controller, array $matches = []): void
    {
        $this->routeFunctionCall($controller, $matches);
    }

    protected function executeMiddleware($controller, array $matches = []): void
    {
        $this->routeFunctionCall($controller, $matches, true);
    }

}