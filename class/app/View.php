<?php
    namespace Demo;
    class View{
        private $data=[];
        private $render=false;

        function __construct($fileName){
            $file= check_file_extention($fileName);
            if (is_file(ROOT . "views/" . $file )) { 
                $this->render=ROOT . "views/" . $file ; 
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
