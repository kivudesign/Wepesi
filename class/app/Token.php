<?php
    
    class Token{
        static function generate(){
            return Session::put(Config::get("session/token_name"),md5(uniqid()));
        }
        static function check($token){
            $tokenName=Config::get("session/token_name");
            if(Session::exists($tokenName) && $token===Session::get($tokenName)){
                Session::delete($tokenName);
                return true;
            }
            return false;
        }
    }
?>