<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core\Http\Providers\Contracts;

/**
 *
 */
interface ResponseContract
{
    /**
     * @param array|string $data
     * @param int $status
     * @return mixed
     */
    public static function send($data, int $status);

    /**
     * @param int $status_code
     * @return mixed
     */
    public static function setStatusCode(int $status_code);
}
