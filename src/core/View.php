<?php

namespace Wepesi\App\Core;

    class View{
        private $data=[];
        private $render=false;

        function __construct($filename=404)
        {
            $file= checkFileExtension($filename);
            if (is_file(ROOT . "views/" . $file)) { 
                $this->render=ROOT . "views/" . $file; 
            }else{
                $this->render=ROOT . "error/$filename.php";
            }
        }

        function assign($variable,$value){
            $this->data[$variable]=$value;
        }
        function __destruct()
        {
            extract($this->data);
            include($this->render);
        }

    }
