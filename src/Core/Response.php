<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core;

/**
 *
 */
class Response
{
    /**
     * @param $data
     * @param int $status
     * @return void
     */
    public static function send($data, int $status = 200)
    {
        header('Content-Type:application/json;charset=utf-8');
        self::setStatusCode($status);
        $response = $data;
        if (is_array($data)) {
            $response = json_encode($data, true);
        }
        echo $response;
        exit();
    }

    /**
     * @param int $status_code
     * @return void
     */
    public static function setStatusCode(int $status_code)
    {
        http_response_code($status_code);
    }
}