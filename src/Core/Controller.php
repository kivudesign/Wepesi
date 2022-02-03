<?php

namespace Wepesi\Core;
    class Controller{
        /**
         * @param $filename
         * @return void
         */
        private static function useModel($filename){
            $file = checkFileExtension($filename);
            if (is_file(ROOT . 'corp/' . $file)) {
                require_once(ROOT . 'corp/' . $file );
            }
        }

        /**
         * 
         * @param string $filename
         * @return void
         */
        static function match_Controller(string $filename){
            $directories = getSubDirectories("controller");
            foreach($directories as $dir){
                $file=$dir."/". checkFileExtension($filename);
                if (is_file( $file )) {
                    require_once($file );
                }
            }
        }       
    }
