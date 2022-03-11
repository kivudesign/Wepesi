<?php

namespace Wepesi\Core;

    class View{
        private array $data=[];
        const ERROR=ROOT."views/404.php";
        private string $render=self::ERROR;
        private $i18n;

        function __construct(string $filename=null)
        {
            $file= checkFileExtension($filename);
            if (is_file(ROOT . "views/" . $file)) {
                $this->render=ROOT . "views/" . $file; 
            }
        }

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
