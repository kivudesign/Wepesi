<?php

namespace Wepesi\Core;

    class View{
        private array $data=[];
        const ERROR=ROOT."views/404.php";
        private string $render;

        function __construct()
        {
            $this->render=self::ERROR;
        }

        /**
         * call this method to display file content
         * @param string $file_name
         */
        function display(string $file_name){
            $file = checkFileExtension($file_name);
            if (is_file(ROOT . 'views/' . $file)) {
                $this->render = ROOT . 'views/' . $file;
            }
        }

        /**
         * assign variables data to be displayed on file_page
         * @param string $variable
         * @param $value
         */
        function assign(string $variable,$value){
            $this->data[$variable]=$value;
        }
        function __destruct()
        {
            extract($this->data);
            if (is_file($this->render)) {
                include($this->render);
            }else{
                print_r(["exception"=>"file path is not correct.?"]);
            }
        }
    }
