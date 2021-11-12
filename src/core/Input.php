<?php

namespace Wepesi\App\Core;
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

    static function get($item)
    {
        $object_data = self::_put();
        if (isset($_POST[$item])) {
            return $_POST[$item];
        } else if (isset($_GET[$item])) {
            return $_GET[$item];
        } else if (isset($object_data[$item])) {
            return $object_data[$item];
        }
        return "";
    }

    static function header($item)
    {
        $headers = getallheaders();
        if (isset($headers[$item])) {
            return $headers[$item];
        }
        return false;
    }

    private static function _put()
    {
        if (file_get_contents("php://input")) {
            parse_str(file_get_contents("php://input"),$file_input);
            if($file_input) return (array)$file_input;
            //
            $file_input = file_get_contents("php://input");
            if (json_decode($file_input)) {
                return (array)json_decode($file_input, TRUE);
            } else {
                return extractFormData($file_input);
            }
        }
        return false;
    }

    static function put(){
        return self::_put();
    }

    static function body(){
        return isset($_POST) && !empty($_POST) ? $_POST : self::_put();
    }
    private function extractFormData($file_input){
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