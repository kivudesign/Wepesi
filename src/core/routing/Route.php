<?php

namespace Wepesi\App\Core;
use Exception;

    class Route{
        private $_path;
        private $_collable;
        private $_matches=[];
        private $_params=[];

        function __construct($path,$collable){
            $this->_path=trim($path,"/");
            $this->_collable= $collable; 
        }

        /**
         * @param $url
         * @return bool
         */
        function match($url):bool{
            $url=trim($url,"/");
            $path=preg_replace_callback('#:([\w]+)#',[$this,'paramMatch'],$this->_path);
            $regex="#^$path$#i";
            if(!preg_match($regex,$url,$matches)){
                return false;
            }
            array_shift($matches);
            $this->_matches=$matches;
            return true;
        }

        /**
         * @throws Exception
         */
        function call(){
            try{
                // get the class_name and the methode to be call
                if (is_string($this->_collable)) {
                    $params = explode("#", $this->_collable);
                    $classController=$params[0];$method=$params[1];
                    Controller::useController($classController);
                    if (!class_exists($classController,true)) {
                        throw new Exception("class : <b> $classController</b> is not defined.");
                    }
                    $class_instance = new $classController;
                    if(!method_exists($class_instance,$method)) {
                        throw new Exception("method :<b> $method</b> does not belong the class : <b> $classController</b>.");
                    }
                    call_user_func_array([$class_instance, $method], $this->_matches);
                } else {
                    if(isset($this->_collable) && is_callable($this->_collable, true)){
                        return call_user_func_array($this->_collable, $this->_matches);
                    }
                }
            }catch(Exception $ex){
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
                return "(".$this->_params[$match[1]].")";
            }
            return "([^/]+)";
        }
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
        function geturl($params){
            $path=$this->_path;
            foreach($params as $k=>$v){
                $path=str_replace(":$k",$v,$path);
            }
            return $path;
        }
    }