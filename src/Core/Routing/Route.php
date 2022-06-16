<?php

namespace Wepesi\Core\Routing;

use Exception;

class Route{
    private string $_path;
    private $callable;
    private array $_matches;
    private array $_params;
    private array $_get_params, $middleware_tab;
    private bool $middleware_exist;

    function __construct($path, $callable)
    {
        $this->_path = trim($path, '/');
        $this->callable = $callable;
        $this->_matches = [];
        $this->_params = [];
        $this->_get_params = [];
        $this->middleware_tab = [];
        $this->middleware_exist = false;
    }

    /**
     * @param $url
     * @return bool
     */
    function match($url): bool
    {
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->_path);
        $regex = "#^$path$#i";
        if (!preg_match($regex, $url, $matches)) {
            return false;
        }
        // remove the url path on the array key
        array_shift($matches);
        array_shift($_GET);
        $this->_matches = $matches;
        foreach ($matches as $key => $val) {
            $_GET[$this->_get_params[$key]] = $val;
        }
        return true;
    }

    /**
     * @throws Exception
     */
    function call()
    {
        try {
            if (count($this->middleware_tab) > 0) {
                $this->middleware_exist = false;
                foreach ($this->middleware_tab as $middleware) {
                    $this->controllerMiddleware($middleware, true);
                }
                $this->middleware_tab = [];
            }
            $this->controllerMiddleware($this->callable);
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * @param $match
     * @return string
     */
    private function paramMatch($match): string
    {
        //
        if (isset($this->_params[$match[1]])) {
            return '(' . $this->_params[$match[1]] . ')';
        }
        array_push($this->_get_params, $match[1]);
        return '([^/]+)';
    }

    function with($param, $regex): \Wepesi\App\Core\Route
    {
        $this->_params[$param] = str_replace('(', '(?:', $regex);
        return $this;
    }

    /**
     * @return array
     */
    function getmatch(): array
    {
        return $this->_matches;
    }

    function getUrl($params)
    {
        $path = $this->_path;
        foreach ($params as $k => $v) {
            $path = str_replace(":$k", $v, $path);
        }
        return $path;
    }

    function middleware($middleware): Route
    {
        $this->middleware_tab[] = $middleware;
        return $this;
    }

    private function controllerMiddleware($callable, bool $is_middleware = false): void
    {
        $controller = !$is_middleware ? 'controller' : 'middleware';
        try {
            if (is_string($callable) || is_array($callable)) {
                $params = is_string($callable) ? explode('#', $callable) : $callable;
                if (count($params) != 2) {
                    throw new Exception("Error : on `$controller` class/method is not well defined");
                }
                $classCallable = $params[0];
                $class_method = $params[1];
                $is_middleware ? MiddleWare::get($classCallable) : Controller::get($classCallable);
                if (!class_exists($classCallable, true)) {
                    throw new Exception("$classCallable class not defined, not a valid $controller");
                }
                $class_instance = new $classCallable;
                if (!method_exists($class_instance, $class_method)) {
                    throw new Exception("method : $class_method does not belong the class : $classCallable.");
                }
                call_user_func_array([$class_instance, $class_method], $this->_matches);
            } else {
                $closure = !$is_middleware ? $this->callable : $callable;
                if (isset($closure) && is_callable($closure, true)) {
                    call_user_func_array($closure, $this->_matches);
                }
            }
            return;
        } catch (Exception $ex) {
            print_r($ex->getMessage());
            die();
        }
    }
}