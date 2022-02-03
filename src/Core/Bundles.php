<?php

namespace Wepesi\Core;

    class Bundles{
        static function insertCSS(string $file){
            if(is_file("layout/style/" . $file . ".css")){
                print '<link rel="stylesheet" type="text/css" href="'.WEB_ROOT.'layout/style/'.$file.'.css"/>'."\n";
            }
        }

        static function insertJS(string $file){
        if (is_file("layout/js/" . $file . ".js")) {
                print '<script  src="'.WEB_ROOT.'layout/js/'.$file.'.js"></script>';
            }
        }
        static function insertIMG($file){
            if (is_file("layout/img/" . $file ) ) {
                    print '<img src="'.WEB_ROOT.'layout/img/'.$file.'" alt="">';
            }else{
                print $file;
            }
        }
    }
?>
