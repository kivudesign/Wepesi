<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Http;

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
        echo json_encode($data, true);
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