<?php
    class Controller{
        
        static function useModel($file)        {
            if (is_file(ROOT . 'corp/' . $file . ".php")) {
                require_once(ROOT . 'corp/' . $file . '.php');
            }
        }
        static function useController($file)        {
            if (is_file(ROOT . 'controller/' . $file . ".php")) {
                require_once(ROOT . 'controller/' . $file . '.php');
            }
        }       
    }
?>