<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core\Routing;

use Wepesi\Core\Application;
use Wepesi\Core\CoreException\RoutingException;
use Wepesi\Core\Response;
use Wepesi\Core\Routing\Traits\routeBuilder;

/**
 *  Wepesi API Router provider
 */
class  Router
{
    /**
     * @var array|null
     */
    protected array $baseMiddleware;
    /**
     * @var string|mixed|null
     */
    private ?string $url;
    /**
     * @var array
     */
    private array $routes;
    /**
     * @var array
     */
    private array $_nameRoute;
    /**
     * @var string
     */
    private string $baseRoute;
    /**
     * @var null
     */
    private $notFoundCallback;

    use routeBuilder;

    /**
     *
     */
    public function __construct()
    {
        $this->baseRoute = '';
        $this->url = $_SERVER['REQUEST_URI'];
        $this->routes = [];
        $this->_nameRoute = [];
        $this->notFoundCallback = null;
        $this->baseMiddleware = [];
    }

    /**
     * @return mixed|void
     */
    protected function getMethodeUrl()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * @param $path
     * @param $callable
     * @param null $name
     * @return Route
     */
    public function get(string $path, $callable, $name = null): Route
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
        return $route;
    }

    /**
     * @param $path
     * @param $callable
     * @param null $name
     * @return Route
     */
    public function post(string $path, $callable, $name = null): Route
    {
        return $this->add($path, $callable, $name, 'POST');
    }
    /**
     *
     */
    public function put(string $path, $callable, $name = null): Route
    {
        return $this->add($path, $callable, $name, 'PUT');
    }
    /**
     *
     */
    public function delete(string $path, $callable, $name = null): Route
    {
        return $this->add($path, $callable, $name, 'DELETE');
    }

    /**
     * The group method help to group a collection of routes in to a sub-route pattern.
     * The sub-route pattern is prefixed into all following routes defined in the scope.
     * @param array|string $base_route can be a string or an array to defined middleware for the group routing
     * @param callable $callable a callable method can be a controller method or an anonymous callable method
     */
    public function group($base_route,callable $callable)
    {
        $pattern = $base_route;
        if (is_array($base_route)) {
            $pattern = $base_route['pattern'] ?? '';
            if (isset($base_route['middleware'])) {
                $this->validateMiddleware($base_route['middleware']);
            }
        }
        $cur_base_route = $this->baseRoute;
        $this->baseRoute .= $pattern;
        call_user_func($callable);
        $this->baseRoute = $cur_base_route;
    }

    /**
     * API base group routing
     * @param string|array $base_route it can be defined as we did for group routing, but you don't need to specify api, it will be added automatically
     * @param callable $callable
     * @return null
     */
    public function api($base_route,callable $callable){
        $api_pattern = '/api';
        if (is_array($base_route)) {
            $base_route['pattern'] = $api_pattern . (isset($base_route['pattern']) ? $this->trimPath($base_route['pattern']) : '');
        } else {

            $base_route = $api_pattern . $this->trimPath($base_route);
        }
        return $this->group($base_route,$callable);
    }

    /**
     * @param string $path
     * @return string
     */
    private function trimPath(string $path) :string {
        $trim_path = trim($path,'/');
        return strlen($trim_path) > 0 ? '/' . $trim_path : '';
    }

    /**
     * @param string $name
     * @param array $params
     * @return \Exception[]|RoutingException[]
     */
    public function url(string $name, array $params = [])
    {
        try {
            if (! isset($this->_nameRoute[$name])) {
                throw new RoutingException('No route match');
            }
            return $this->_nameRoute[$name]->geturl($params);
        } catch (RoutingException $ex) {
            return ['RoutingException' => $ex];
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
                Response::setStatusCode(404);
            }
        } catch (RoutingException $ex) {
            Application::dumper($ex);
            Response::setStatusCode(500);
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