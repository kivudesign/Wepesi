<?php

namespace Wepesi\Core;

/**
 *
 */
class Escape{
    /**
     * @param string $input
     * @return string
     */
    static function encode(string $input)
    {
        $text = $input;
        if ($input != strip_tags($input)) {
            $text = htmlentities($input, ENT_QUOTES, 'UTF-8');
        }
        return $text;
    }

    /**
     * @param string $input
     * @return string
     */
    static function decode(string $input)
    {
        return html_entity_decode($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @param int $length
     * @return string
     */
    static function randomString(int $length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param string $link
     * @return string
     */
    static function addSlaches(string $link): string
    {
        $sub_string = substr($link, 0, 1);
        $new_link = substr($link, 1);
        if ($sub_string == '/') {
            $link = substr(self::addSlaches($new_link),1);
        }
        return $link == '' ? $link : '/' . $link;
    }

    /**
     * @param $fileName
     * @return mixed|string
     */
    static function checkFileExtension($fileName)
    {
        $file_parts = pathinfo($fileName);
        return isset($file_parts['extension']) ? $fileName : $fileName . '.php';
    }
}