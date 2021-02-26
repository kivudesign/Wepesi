<?php
 
    class Validate{
        private $_passed=false;
        private $_errors=false;
        private $_db=null;
        function __construct()
        {
            $this->_db==DB::getInstance();
            $this->lang = (object)LANG_VALIDATION;
        }

        function check($source,$items=array()){
            foreach($items as $item=>$rules){
                foreach($rules as $rule=>$rvalue){

                    $value= is_array($source[$item]) ? $source[$item]['name'] : trim($source[$item]);
                    $item=escape($item);
                    
                    if($rule=='required' && empty($value)){
                        $this->addError("{$item} ". $this->lang->required);
                    }else if(!empty($value)){
                        switch($rule){
                            case "min":
                                    if(strlen($value)<$rvalue){
                                        $this->addError("{$item} ". $this->lang->min." {$rvalue} caracters");
                                    }
                                break;
                                case "max":
                                    if(strlen($value)>$rvalue){
                                        $this->addError("{$item} ".$this->lang->max." {$rvalue} caracters");
                                    }
                                break;
                                case "matches":
                                    if($value!=$source[$rvalue]){
                                        $this->addError("{$rvalue} " . $this->lang->matches . " {$item}");
                                    }
                                break;
                                case "number":
                                    if(preg_match("#.\W#",$value) || preg_match("#[a-zA-Z]#",$value)){
                                        $this->addError("{$item} " . $this->lang->number );
                                    }
                                break;
                                case "email":
                                    if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                                        $this->addError("{$item} " . $this->lang->email );
                                    }
                                break;
                                case "url":
                                    if(!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $source[$rvalue])){
                                        $this->addError("{$item} " . $this->lang->url);
                                    }
                            case "unique":
                                $check=$this->_db->get($rvalue,array($item,'=',$value));
                                if($check->count()){
                                    $this->addError("{$item} ". $this->lang->unique);
                                }
                            break;
                        }
                    }   
                }
            }
            if(empty($this->_errors)){
                $this->_passed=true;
            }
        }

        function addError($error){
            $this->_errors[]=$error; 
        }

        function errors(){
            return $this->_errors;
        }

        function passed(){
            return $this->_passed;
        }
    }
