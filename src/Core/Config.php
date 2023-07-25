<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

namespace Wepesi\Core;
/**
 *
 */
class Config
{
    /**
     * @param $path
     * @return false|mixed|string
     */
    static function get($path = null)
    {
        if ($path) {
            $config = $GLOBALS['config'] ?? '';
            $path = explode('/', $path);
            foreach ($path as $bit) {
                if (isset($config[$bit])) {
                    $config = $config[$bit];
                }
            }
            return $config;
        }
        return false;
    }
}
