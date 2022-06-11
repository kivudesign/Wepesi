<?php

namespace Wepesi\Core;
class Input
{
    static function exists($type = "POST")
    {
        switch ($type) {
            case "POST":
                return !empty($_POST) || self::_put()||!empty($_FILES);
            default:
                return false;
        }
    }

    /**
     * @param $item
     * @return mixed|null
     */
    static function get($item)
    {
        $object_data = self::put();
        if (isset($_POST[$item])) {
            return $_POST[$item];
        } else if (isset($_GET[$item])) {
            return $_GET[$item];
        } else if (isset($object_data[$item])) {
            return $object_data[$item];
        }
        return null;
    }

    /**
     * Extract header data information
     * @param $item
     * @return mixed|null
     */
    static function header($item)
    {
        $headers = getallheaders();
        if (isset($headers[$item])) {
            return $headers[$item];
        }
        return null;
    }

    /**
     * extract data submitted as json on POST or PUT or PATCH method
     * @return array|null
     */
    private static function put(): ?array
    {
        if (file_get_contents('php://input')) {
            parse_str(file_get_contents('php://input'), $file_input);
            if ($file_input) return (array)$file_input;
            //
            $file_input = file_get_contents('php://input');
            if (json_decode($file_input)) {
                return (array)json_decode($file_input, TRUE);
            } else {
                return self::extractFromFormData($file_input);
            }
        }
        return null;
    }

    private static function body(){
        return isset($_POST) && !empty($_POST) ? $_POST : self::put();
    }
    private static function extractFromFormData($file_input){
        $fragma = [];
        $explode = explode("\r", implode("\r", explode("\n", $file_input)));
        $len_Arr = count($explode);
        for ($i = 1; $i < $len_Arr; $i++) {
            if (!strchr($explode[$i], "----------------------------")) {
                if (strlen($explode[$i]) > 0) {
                    $replaced = str_replace("Content-Disposition: form-data; name=", "", $explode[$i]);
                    array_push($fragma, $replaced);
                }
            }
        }
        $len_object = count($fragma);
        $object = [];
        for ($j = 0; $j < $len_object; $j++) {
            if ($j == 0 || ($j + 1) % 2 != 0) {
                $key = str_replace("\"", "", $fragma[$j]);
                $object = array_merge($object, [$key => trim($fragma[($j + 1)])]);
            }
        }
        return $object;
    }
}