<?php    
    class Input{
        static function exists($type="post"){
            switch($type){
                case "post":
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
    }
?>