<?php    
    class Hash{
        static function make($string,$salt=""){
            return hash('sha256',$string.$salt);
        }
        static function salt($length){
        // bin2hex($bytes)
            return random_bytes($length);
        }
        static function unique(){
            return self::make(uniqid());
        }
    }
?>