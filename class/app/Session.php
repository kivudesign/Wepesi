<?php
    class Session{
        static function exists($name){
            return (isset($_SESSION[$name]))?true:false;
        } 
        
        static function put($name,$value){
            return $_SESSION[$name]=$value;
        } 
        static function get($name){
            return isset($_SESSION[$name])? $_SESSION[$name]:false;
        } 
        static function delete($name){
            if(self::exists($name)){
                unset($_SESSION[$name]);
                
            }
        } 
        static function flash($name,$string=''){
            if(self::exists($name)){
                $session=self::get($name);
                self::delete($name);
                return $session;                
            }else{
                self::put($name,$string);
            }
        } 
    }
?>