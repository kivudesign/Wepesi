<?php

namespace Wepesi\Core;

/**
 *
 */
class Token
{
    /**
     * @return mixed
     */
    public static function generate()
    {
        return Session::put(Config::get("session/token_name"), md5(uniqid()));
    }

    /**
     * @param $token
     * @return bool
     */
    public static function check($token): bool
    {
        $tokenName = Config::get("session/token_name");
        if (Session::exists($tokenName) && $token === Session::get($tokenName)) {
            Session::delete($tokenName);
            return true;
        }
        return false;
    }
}
