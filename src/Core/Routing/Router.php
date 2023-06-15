<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core\Routing;

use Wepesi\Core\Application;
use Wepesi\Core\CoreException\RoutingException;
use Wepesi\Core\Response;
use Wepesi\Core\Routing\Traits\routeBuilder;

class  Router
{
    protected ?array $baseMiddleware;
    private ?string $url;
    private array $routes;
    private array $_nameRoute;
    private string $baseRoute;
    private $notFoundCallback;

    use routeBuilder;

    /**
     *
     */
    function __construct()
    {
        $this->baseRoute = '';
        $this->url = $this->getMethodeUrl();
        $this->routes = [];
        $this->_nameRoute = [];
        $this->notFoundCallback = null;
        $this->baseMiddleware = null;
    }

    /**
     * @return mixed|void
     */
    protected function getMethodeUrl()
    {
        foreach ($_GET as $url) return $url;
    }

    /**
     * @param $path
     * @param $callable
     * @param null $name
     * @return Route
     */
    public function get($path, $callable, $name = null): Route
    {
        return $this->add($path, $callable, $name, 'GET');
    }

    /**
     * @param string $pattern
     * @param $callable
     * @param string|null $name
     * @param string $methode
     * @return Route
     */
    private function add(string $pattern, $callable, ?string $name, string $methode): Route
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;
        $route = new Route($pattern, $callable, $this->baseMiddleware);
        $this->routes[$methode][] = $route;

        if ($name == null && is_string($callable)) {
            $name = $callable;
        }

        if ($name) {
            $this->_nameRoute[$name] = $route;
        }
        $this->baseMiddleware = null;
        return $route;
    }

    /**
     * @param $path
     * @param $callable
     * @param null $name
     * @return Route
     */
    public function post($path, $callable, $name = null): Route
    {
        return $this->add($path, $callable, $name, 'POST');
    }

    /**
     * The group method help to group a collection of routes in to a sub-route pattern.
     * The sub-route pattern is prefixed into all following routes defined in the scope.
     * @param string $base_route be a string or used as array to defined middleware for the group routing
     * @param array|string $callable a callable method can be a controller method or an anonymous callable method
     */
    public function group(string $base_route, $callable)
    {
        $pattern = $base_route;
        if (is_array($base_route)) {
            $pattern = $base_route['pattern'] ?? '/';
            if (isset($base_route['middleware'])) {
                $this->baseMiddleware = $this->validateMiddleware($base_route['middleware']);
            }
        }
        $cur_base_route = $this->baseRoute;
        $this->baseRoute .= $pattern;
        call_user_func($callable);
        $this->baseRoute = $cur_base_route;
    }

    /**
     * @param $name
     * @param array $params
     * @return string
     */
    public function url($name, array $params = []): string
    {
        try {
            if (!isset($this->_nameRoute[$name])) {
                throw new RoutingException('No route match');
            }
            return $this->_nameRoute[$name]->geturl($params);
        } catch (RoutingException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * @return mixed|void
     */
    protected function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the 404 handling function.
     *
     * @param object|callable|string $match_fn The function to be executed
     * @param $callable
     */
    public function set404($match_fn, $callable = null)
    {
        if (!$callable) {
            $this->notFoundCallback = $match_fn;
        } else {
            $this->notFoundCallback = $callable;
        }
    }

    /**
     * @return void
     */
    public function run()
    {
        try {
            if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
                throw new RoutingException('Request method is not defined ', 501);
            }
            $routesRequestMethod = $this->routes[$_SERVER['REQUEST_METHOD']];
            $i = 0;
            foreach ($routesRequestMethod as $route) {
                if ($route->match($this->url)) {
                    return $route->call();
                } else {
                    $i++;
                }
            }
            if (count($routesRequestMethod) === $i) {
                $this->trigger404($this->notFoundCallback);
            }
        } catch (RoutingException $ex) {
            Application::dumper($ex);
            exit;
        }
    }

    /**
     * @return void
     */
    protected function trigger404($match = null)
    {
        if ($match) {
            $this->routeFunctionCall($match);
        } else {
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: application/json');
            $result = [
                'status' => '404',
                'message' => 'route not defined'
            ];
            Response::send($result, 404);
        }
    }

    /**
     * validate middleware data structure
     * @param $middleware
     * @return callable[]
     */
    private function validateMiddleware($middleware): array
    {
        $valid_middleware = $middleware;
        if ((is_array($middleware) && count($middleware) == 2 && is_string($middleware[0]) && is_string($middleware[1])) || is_callable($middleware)) {
            $valid_middleware = [$middleware];
        }
        return $valid_middleware;
    }
}