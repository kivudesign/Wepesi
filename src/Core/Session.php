<?php

namespace Wepesi\Core;

/**
 *
 */
class Session
{
    /**
     * @param string $name
     * @param string $string
     * @return false|mixed
     */
    public static function flash(string $name, string $string = '')
    {
        if (self::exists($name)) {
            $session = self::get($name);
            self::delete($name);
            return $session;
        } else {
            self::put($name, $string);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function exists(string $name): bool
    {
        return isset($_SESSION[$name]);
    }

    /**
     * @param string $name
     * @return false|mixed
     */
    public static function get(string $name)
    {
        return $_SESSION[$name] ?? false;
    }

    /**
     * @param string $name
     */
    public static function delete(string $name)
    {
        if (self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * @param string $name
     * @param $value
     * @return mixed
     */
    public static function put(string $name, $value)
    {
        return $_SESSION[$name] = $value;
    }
}
