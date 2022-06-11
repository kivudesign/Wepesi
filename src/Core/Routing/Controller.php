<?php

namespace Wepesi\Core\Routing;

use Wepesi\Core\Config;

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
    static function get(string $filename){
        $controller = Config::get('controller') == WEB_ROOT ? 'controller' : Config::get('controller');
        $directories = getSubDirectories($controller);
        foreach ($directories as $dir) {
            // create the file path
            $file = $dir . '/' . checkFileExtension($filename);
            if (is_file($file)) {
                require_once($file);
            }
        }
    }
}
