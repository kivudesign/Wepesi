<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core\Routing\Traits;

use Wepesi\Core\Application;

trait routeBuilder
{
    protected function routeFunctionCall($callable, bool $is_middleware = false,array $matches = []): void
    {
        $controller = !$is_middleware? 'controller' : 'middleware';
        try {
            if (is_string($callable) || is_array($callable)) {
                $params = is_string($callable) ? explode('#', $callable) : $callable;
                if (count($params) != 2) {
                    throw new \InvalidArgumentException("Error : on `$controller` class/method is not well defined");
                }

                $classCallable = $params[0];
                $class_method = $params[1];
                if (!class_exists($classCallable, true)) {
                    throw new \InvalidArgumentException("$classCallable class not defined, not a valid $controller",500);
                }

                $reflection = new \ReflectionClass($classCallable);
                $class_instance = $reflection->newInstance();

                if(!$reflection->isInstance($class_instance)){
                    throw new \ReflectionException('Only instantiable class can be used. Not abstract,interface, or trait can be used.');
                }
                if (!method_exists($class_instance, $class_method)) {
                    throw new \BadMethodCallException("method : $class_method does not belong the class : $classCallable.",500);
                }
                call_user_func_array([$class_instance, $class_method], $matches);
            } else {
                $closure = $callable;
                if (isset($closure) && is_callable($closure, true)) {
                    call_user_func_array($closure, $matches);
                }
            }
            return;
        } catch (\Exception $ex) {
            Application::dumper($ex);
        }
    }

}