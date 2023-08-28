<?php

namespace Wepesi\Core;

/**
 *
 */
class Escape
{
    /**
     * @param string $input
     * @return string
     */
    public static function encode(string $input): string
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
    public static function decode(string $input): string
    {
        return html_entity_decode($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @param int $length
     * @return string
     */
    public static function randomString(int $length = 8): string
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
    public static function addSlaches(string $link): string
    {
        $sub_string = substr($link, 0, 1);
        $new_link = substr($link, 1);
        if ($sub_string == '/') {
            $link = substr(self::addSlaches($new_link), 1);
        }
        return $link == '' ? $link : '/' . $link;
    }

    /**
     * @param $fileName
     * @return mixed|string
     */
    public static function checkFileExtension($fileName)
    {
        $file_parts = pathinfo($fileName);
        return isset($file_parts['extension']) ? $fileName : $fileName . '.php';
    }

    /**
     * @param array $data_arr
     * @return array
     */
    public static function removeDuplicateAssocArray(array $data_arr): array
    {
        return array_values(array_map('unserialize', array_unique(array_map('serialize', $data_arr))));
    }
}