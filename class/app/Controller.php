<?php
    class Controller{
        
        static function useModel($fileName)        {
            $file= check_file_extention($fileName);
            if (is_file(ROOT . 'corp/' . $file )) {
                require_once(ROOT . 'corp/' . $file);
            }
        }
        static function useController($fileName)        {
            $directorie= getSubDirectories('controller');
            foreach($directorie as $dir){
                $file=$dir."/". check_file_extention($fileName);
                if (is_file($file)) {
                    require_once($file );
                }
            }            
        }       
    }
?>