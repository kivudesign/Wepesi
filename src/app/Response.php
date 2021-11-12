<?php

namespace Wepesi\App\Core;

    class Response{     

        static function send($data,$status=200){
            http_response_code($status);
            header('Content-Type:application/json;chartset=utf-8');
            $response = $data;
            if (is_array($data)) {
                $response = json_encode($data, true);
            }
            echo $response;
            exit();
        }
    }