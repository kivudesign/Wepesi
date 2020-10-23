<?php    
    class Input{
        static function exists($type="POST"){
            switch($type){
                case "POST":
                    return (!empty($_POST))?true:false;
                break;
                default: 
                    return false;
                break;
            }
        }
        static function get($item){
            if(isset($_POST[$item])){
                return $_POST[$item];
            }else if(isset($_GET[$item])){
                return $_GET[$item];
            }
            return "";
        }
        static function header($item){            
            $headers = getallheaders();
            if(isset($headers[$item])){
                return $headers[$item];
            }
            return false;
        }
    }
