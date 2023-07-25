<?php

namespace Wepesi\Core;

/**
 *
 */
class Bundles
{
    /**
     * @param string $file
     * @return void
     */
    static function insertCSS(string $file)
    {
        if (is_file(ROOT . "assets/css/" . $file . ".css")) {
            print '<link rel="stylesheet" type="text/css" href="' . WEB_ROOT . 'assets/css/' . $file . '.css"/>' . "\n";
        }
    }

    /**
     * @param string $file
     * @return void
     */
    static function insertJS(string $file)
    {
        if (is_file(ROOT . "assets/js/" . $file . ".js")) {
            print '<script  src="' . WEB_ROOT . 'assets/js/' . $file . '.js"></script>' . "\n";
        }
    }

    /**
     * @param $file_name
     * @return void
     */
    static function insertIMG($file_name)
    {
        if (is_file(ROOT . "assets/img/" . $file_name)) {
            print '<img src="' . WEB_ROOT . 'assets/img/' . $file_name . '" alt="">';
        } else {
            print $file_name;
        }
    }
}

