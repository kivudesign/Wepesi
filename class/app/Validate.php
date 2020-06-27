<?php
 
    class Validate{
        private $_passed=false;
        private $_errors=false;
        private $_db=null;
        function __construct()
        {
            $this->_db==DB::getInstance();
        }

        function check($source,$items=array()){
            foreach($items as $item=>$rules){
                foreach($rules as $rule=>$rvalue){

                    $value=trim($source[$item]);
                    $item=escape($item);
                    
                    if($rule=='required' && empty($value)){
                        $this->addError("{$item} is required");
                    }else if(!empty($value)){
                        switch($rule){
                            case "min":
                                if(strlen($value)<$rvalue){
                                    $this->addError("{$item} must be a minimum of {$rvalue} caracters");
                                }
                            break;
                            case "max":
                                if(strlen($value)>$rvalue){
                                    $this->addError("{$item} must be a maximum of {$rvalue} caracters");
                                }
                            break;
                            case "matches":
                                if($value!=$source[$rvalue]){
                                    $this->addError("{$rule} must match {$item}");
                                }
                            break;
                            case "unique":
                                $check=$this->_db->get($rvalue,array($item,'=',$value));
                                if($check->count()){
                                    $this->addError("{$item} already exist.");
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
?>