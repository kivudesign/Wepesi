<?php
    class View{
        private $data=[];
        private $render=false;

        function __construct($file){
            if (is_file(ROOT . "views/" . $file . ".php")) { 
                $this->render=ROOT . "views/" . $file . ".php"; 
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
