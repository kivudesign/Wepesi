<?php    

namespace Wepesi\App\Core;
    class Hash{
        static function make($string,$salt=""){
            return hash('sha256',$string.$salt);
        }
        static function salt($length){
            return bin2hex(random_bytes($length));
        }
        static function unique(){
            return self::make(uniqid());
        }
    }
?>