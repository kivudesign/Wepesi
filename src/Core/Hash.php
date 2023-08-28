<?php

namespace Wepesi\Core;

/**
 *
 */
class Hash
{
    /**
     * @param $length
     * @return string
     * @throws \Exception
     */
    static function salt($length): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * @return string
     */
    static function unique()
    {
        return self::make(uniqid());
    }

    /**
     * @param $string
     * @param $salt
     * @return string
     */
    static function make($string, $salt = "")
    {
        return hash('sha256', $string . $salt);
    }
}
