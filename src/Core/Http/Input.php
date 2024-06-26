<?php

namespace Wepesi\Core\Http;
/**
 *
 */
class Input
{
    /**
     * check if the submission form is a port action
     * @param $type
     * @return bool
     */
    public static function exists($type = "POST"): bool
    {
        switch ($type) {
            case "POST":
                return !empty($_POST) || self::put() || !empty($_FILES);
            default:
                return false;
        }
    }

    /**
     * extract data submitted as json on POST or PUT or PATCH method
     * @return array
     */
    private static function put(): array
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
        return [];
    }

    /**
     * extract data from submit form as form data
     * @param $file_input
     * @return array
     */
    private static function extractFromFormData($file_input): array
    {
        $form_data_fragmentation = [];
        $explode = explode("\r", implode("\r", explode("\n", $file_input)));
        $len_Arr = count($explode);
        for ($i = 1; $i < $len_Arr; $i++) {
            if (!strchr($explode[$i], "----------------------------")) {
                if (strlen($explode[$i]) > 0) {
                    $replaced = str_replace("Content-Disposition: form-data; name=", "", $explode[$i]);
                    $form_data_fragmentation[] = $replaced;
                }
            }
        }
        $len_object = count($form_data_fragmentation);
        $object = [];
        for ($j = 0; $j < $len_object; $j++) {
            if ($j == 0 || ($j + 1) % 2 != 0) {
                $key = str_replace("\"", "", $form_data_fragmentation[$j]);
                $object = array_merge($object, [$key => trim($form_data_fragmentation[($j + 1)])]);
            }
        }
        return $object;
    }

    /**
     * Access data information from $_GET action of url request
     * @param $item
     * @return mixed|null
     */
    static function get($item)
    {
        return $_GET[$item] ?? null;
    }

    /**
     * Access HEADER data information from header
     * Extract header data information
     * @param $item
     * @return mixed|null
     */
    public static function header($item)
    {
        $headers = getallheaders();
        if (isset($headers[$item])) {
            return $headers[$item];
        }
        return null;
    }

    /**
     * Lists params submitted via POST, PATCH or PUT action event
     * @return array
     */
    public static function body(): ?array
    {
        return !empty($_POST) ? $_POST : (!empty(self::put()) ? self::put() : null);
    }

    /**
     * Access data information from POST or PUT action event
     * @param $item
     * @return mixed|string|null
     */
    public static function post($item){
        $self_put = self::put();
        if (isset($_POST[$item])) {
            return $_POST[$item];
        } else if (isset($self_put[$item])) {
            return $self_put[$item];
        }
        return null;
    }

    /**
     * Access data information from file upload event
     * @param string $item
     * @return mixed|null
     */
    public static function file(string $item) {
        return $_FILES[$item] ?? null;
    }
}
