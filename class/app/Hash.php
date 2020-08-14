<?php    
    class Hash{
        static function make($string,$salt=""){
            // could use password_hash instead of hash only so that you may use the BCRYPT as the hashing algorythm
            // check https://www.php.net/manual/en/function.password-hash.php
            return hash('sha256',$string.$salt);
        }
        static function salt($bytes){
            return bin2hex($bytes);
        }
        static function unique(){
            return self::make(uniqid());
        }
    }
?>
