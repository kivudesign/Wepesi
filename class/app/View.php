<?php
    namespace Demo;
    class View{
        private $data=[];
        private $render=false;

        function __construct($fileName){
            $file= ROOT . "views/" .  check_file_extention($fileName);
            if (is_file($file )) { 
                $this->render=$file ; 
            }
        }

        function assign($variable,$value){
            $this->data[$variable]=$value;
        }
        function __destruct(){
            extract($this->data);
            include($this->render);
        }
    }
