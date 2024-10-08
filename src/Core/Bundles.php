<?php

namespace Wepesi\Core;

/**
 *
 */
class Bundles
{
    private static array $header_link = [];

    /**
     * manage to add a JavaScript script on the page
     * @param string $file
     */
    static function insertCSS(string $file)
    {
        if (is_file(Application::getRootDir() . '/assets/css/' . $file . '.css')) {
            $href = WEB_ROOT . "assets/css/$file.css";
            $link = <<<EOF
                    <link rel="stylesheet" type="text/css" href="$href"/>
                EOF;
            echo $link . PHP_EOL;
        }
    }

    /**
     * help to manage metadata
     * @param array $meta_data
     * @return MetaData
     */
    static function metaData(array $meta_data = []): MetaData
    {
        return new MetaData();
    }

    public static function getHeaderJS()
    {
        if (count(self::$header_link) > 0) {
            foreach (self::$header_link as $link) {
                self::insertJS($link);
            }
        }
    }

    /**
     * manage to add  javascript script on the page
     * @param string $file
     */
    static function insertJS(string $file, bool $is_module = false, bool $not_void = false)
    {
        if (is_file(ROOT . 'assets/js/' . $file . '.js')) {
            $src = WEB_ROOT . "assets/js/$file.js";
            $type = $is_module ? 'type="module"' : '';
            $link = <<<EOF
                    <script  src="$src" $type></script>
                EOF;
            if ($not_void) {
                return $link;
            }
            echo $link . PHP_EOL;
        }
    }
}

