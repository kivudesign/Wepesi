<?php

namespace Wepesi\Core\Routing;
use Wepesi\Core\Controller;
use Exception;

class Route{
    private $_path;
    private $callable;
    private $_matches=[];
    private $_params=[];
    private $_get_params=[];

    function __construct($path,$callable){
        $this->_path=trim($path,"/");
        $this->callable= $callable;
    }

    /**
     * This module help to recover all information from the $_GET method or url
     * @param $url
     * @return bool
     */
    function match($url):bool{
        $url = trim($url,"/");
        $path = preg_replace_callback('#:([\w]+)#',[$this,'paramMatch'],$this->_path);
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
     * This module Help to determine
     * if we going to call a callable function or controller class
     * @throws Exception
     */
    function call(){
        try{
            /**
             * check if callable either is a string or an array
             */
            if (is_string($this->callable) || is_array($this->callable)) {
                $params = is_string($this->callable)?explode("#", $this->callable):$this->callable;
                if(count($params) != 2){
                    throw new Exception("Error on class or method is not well defined");
                }
                $classController=$params[0];
                $class_method=$params[1];
                Controller::match_Controller($classController);
                if (!class_exists($classController,true)) {
                    throw new Exception("class : <b> $classController</b> is not defined. on controller folder");
                }
                $class_instance = new $classController;
                if(!method_exists($class_instance,$class_method)) {
                    throw new Exception("method :<b> $class_method</b> does not belong the class : <b> $classController</b>.");
                }
                call_user_func_array([$class_instance, $class_method], $this->_matches);
            } else {
                if(isset($this->callable) && is_callable($this->callable, true)){
                    return call_user_func_array($this->callable, $this->_matches);
                }
            }
        }catch(Exception $ex){
            echo $ex->getMessage();
        }
    }

    /**
     * This module will help to verify is the params define on the routing
     * match with the data pass throughout the route call
     * @param $match
     * @return string
     */
    private function paramMatch($match):string{
        //
        if(isset($this->_params[$match[1]])){
            return "(".$this->_params[$match[1]].")";
        }
        array_push($this->_get_params,$match[1]);
        return "([^/]+)";
    }
    /*
     * 
     */
    function with($param,$regex): Route
    {
        $this->_params[$param]=str_replace('(','(?:',$regex);
        return $this;
    }

    /**
     * @return array
     */
    function getmatch():array{
        return $this->_matches;
    }

    /**
     * Get all the url define on the route
     * 
     * @param $params
     * @return array|string|string[]
     */
    function geturl($params){
        $path=$this->_path;
        foreach($params as $k=>$v){
            $path=str_replace(":$k",$v,$path);
        }
        return $path;
    }
}