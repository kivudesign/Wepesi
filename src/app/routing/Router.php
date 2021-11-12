<?php

namespace Wepesi\App\Core;

use Exception;
    class  Router{

        private  $_url;
        private  $routes=[];
        private  $_nameRoute=[];

        function __construct()
        {
            $this->_url=$this->getMethodeUrl();
        }

        /**
         * @return mixed|void
         */
        private function getMethodeUrl(){
            foreach($_GET as $url) return $url;
        }

        /**
         * @return mixed|void
         */
        function geturl(){
            return $this->_url;
        }

        /**
         * @param $path
         * @param $collable
         * @param null $name
         * @return Route
         */
        function get($path, $collable,$name=null): Route
        {
            return $this->add($path,$collable,$name,"GET");
        }

        /**
         * @param $path
         * @param $collable
         * @param null $name
         * @return Route
         */
        function post($path, $collable,$name=null): Route
        {
           return $this->add($path,$collable,$name,"POST");
        }

        /**
         * @param $path
         * @param $collable
         * @param $name
         * @param $methode
         * @return Route
         */
        private function add($path,$collable,$name,$methode): Route
        {
            $route = new Route($path, $collable);
            $this->routes[$methode][] = $route;

            if(is_string($collable) && $name==null){
                $name=$collable;
            }

            if($name){
                $this->_nameRoute[$name]=$route;
            }
            return $route;
        }

        /**
         * @throws Exception
         */
        function url($name, $params=[]): string
        {
            try{
                if(!isset($this->_nameRoute[$name])){
                    throw new Exception('No route match');
                }
                return  $this->_nameRoute[$name]->geturl($params);
            }catch(Exception $ex){
                return $ex->getMessage();
            }
        }

        /**
         * @throws Exception
         */
        function run(){
            try{
                if(!isset($this->routes[$_SERVER['REQUEST_METHOD']])){
                    throw new Exception('Request method is not defined ');
                }
                $routesRequestMethod= $this->routes[$_SERVER['REQUEST_METHOD']];
                $i=0;
                foreach($routesRequestMethod as $route){
                    if($route->match($this->_url)){
                        return $route->call();
                    }else{
                        $i++;
                    }
                }
                if(count($routesRequestMethod)===$i){
                    new View();
                }
            }catch(Exception $ex){
                echo $ex->getMessage();
            }
        }        
    }