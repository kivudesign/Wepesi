<?php
    class Controller{
        
        static function useModel($fileName)        {
            $file= check_file_extention($fileName);
            if (is_file(ROOT . 'corp/' . $file )) {
                require_once(ROOT . 'corp/' . $file);
            }
        }
        static function useController($fileName)        {
            $file= check_file_extention($fileName);
            if (is_file(ROOT . 'controller/' . $file)) {
                require_once(ROOT . 'controller/' . $file );
            }
        }       
    }
?>