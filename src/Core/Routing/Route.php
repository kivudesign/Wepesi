<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core\Routing;

use Wepesi\Core\Routing\Traits\routeBuilder;

class Route{
    private string $pattern;
    private $callable;
    private array $_matches;
    private array $_params;
    private array $_get_params,$middleware_tab;

    /**
     *
     */
    use routeBuilder;

    /**
     * @param $path
     * @param $callable
     */
    function __construct($path,$callable,$middleware = null){
        $this->pattern = trim($path, '/');
        $this->callable = $callable;
        $this->_matches = [];
        $this->_params = [];
        $this->_get_params = [];
        $this->middleware_tab = $middleware ?? [];
    }

    /**
     * @param $url
     * @return bool
     */
    function match($url):bool{
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#',[$this,'paramMatch'],$this->pattern);
        $regex = "#^$path$#i";
        if(!preg_match($regex,$url,$matches)){
            return false;
        }
        // remove the url path on the array key
        array_shift($matches);
        array_shift($_GET);
        $this->_matches = $matches;
        foreach ($matches as $key => $val){
            $_GET[$this->_get_params[$key]] = $val;
        }
        return true;
    }

    /**
     *
     */
    public function call(){
        try{
            if (count($this->middleware_tab) > 0) {
                foreach ($this->middleware_tab as $middleware) {
                    $this->routeFunctionCall($middleware, true,$this->_matches);
                }
                $this->middleware_tab = [];
            }
            $this->routeFunctionCall($this->callable,false,$this->_matches);
        }catch (\Exception $ex){
            echo $ex->getMessage();
        }
    }

    /**
     * @param $match
     * @return string
     */
    private function paramMatch($match):string{
        //
        if(isset($this->_params[$match[1]])){
            return '(' .$this->_params[$match[1]]. ')';
        }
        $this->_get_params[] = $match[1];
        return '([^/]+)';
    }

    /**
     * @param $param
     * @param $regex
     * @return $this
     */
    public function with($param,$regex): Route
    {
        $this->_params[$param] = str_replace('(','(?:',$regex);
        return $this;
    }

    /**
     * @return array
     */
    public function getMatch():array{
        return $this->_matches;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param $params
     * @return array|string|string[]
     */
    public function getUrl($params){
        $path = $this->pattern;
        foreach($params as $k => $v){
            $path = str_replace(":$k",$v,$path);
        }
        return $path;
    }

    /**
     * @param $middleware
     * @return $this
     */
    public function middleware($middleware): Route
    {
        $this->middleware_tab[] = $middleware;
        return $this;
    }
}