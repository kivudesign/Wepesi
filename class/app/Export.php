<?php

class Export{

    static function cleanData(&$str){
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);

        // force certain number/date formats to be imported as strings
        if (preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
            $str = "'$str";
        }
        if (strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }
    static function toExcell(array $data){
        $filename = "website_data_" . date('YmdHis') . ".xls";

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");

        $flag = false;
        foreach ($data as $row) {
            if (!$flag) {
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            // array_walk($row,__NAMESPACE__."\cleanData");
            echo implode("\t", array_values($row)) . "\r\n";
        }
        exit;
    }
    static function toJson(array $data){
        $filename = "website_data_" . date('YmdHis') . ".json";

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: text/plain");

        echo json_encode($data, JSON_OBJECT_AS_ARRAY);
        exit;
    }
}
