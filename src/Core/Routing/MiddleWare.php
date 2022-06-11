<?php


namespace Wepesi\Core\Routing;

use Wepesi\Core\Config;

class MiddleWare
{
    static function get($middleWareFilename)
    {
        /**
         * Find out middleware location,
         * it can be either on middleware folder or controller folder.
         */
        $controller = Config::get('middleware') == WEB_ROOT ? 'middleware' : Config::get('controller');
        $directories = getSubDirectories($controller);
        foreach ($directories as $dir) {
            $file = $dir . '/' . checkFileExtension($middleWareFilename);
            if (file_exists($file) && is_file($file)) {
                include $file;
            }
        }
    }
}