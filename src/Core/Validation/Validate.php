<?php
namespace Wepesi\Core\Validation;
use Wepesi\Core\Orm\DB;

/**
 * Description of validation
 * this will allow to validate input value
 *
 * @author Lenovo
 */
class Validate {
    private $_passed;
    private $_errors;
    private $stringValue;
    private $source;
    //put your code here
    function __construct(array $_source=[]) {
        $this->_errors=[];
        $this->_passed = false;
        $this->source=(isset($_POST) && !empty($_POST))?$_POST:((isset($_GET) && !empty($_GET))?$_GET:$_source);
        $this->stringValue=null;
        $this->db=DB::getInstance();
        $this->lang= (object)LANG_VALIDATE;
    }
    /**
     * @param array $source: from where to check
     * @param array $items: model for validation
     *
     * this module help to check if there are define module but does not be defined to be checked     *
     */
    function check(array $source,array $items=[]):void{
        $this->_errors=[];
        $this->check_undefined_Object_key($source,$items);
        foreach($items as $item=>$response){
            if(isset($source[$item])) {
                if($response){
                    foreach ($response as $key=>$value){
                        $this->addError($value);                    
                    }                    
                }
            }else{
                $message=[
                    "type" => "object.unknown",
                    "message" => "`{$item}` does not exist",
                    "label" => $item,
                ];
                $this->addError($message);
            }            
        }
        if (count($this->_errors) == 0) {
            $this->_passed = true;
        }
    }
    private function check_undefined_Object_key(array $source,array $items){
        $diff_array_key= array_diff_key($source,$items);
        $source_key= array_keys($diff_array_key);
        if(count($source_key)>0){
            foreach($source_key as $key){
                $message=[
                    "type" => "object.undefined",
                    "message" => "`{$key}` is not defined",
                    "label" => $key,
                ];
                $this->addError($message);
            }
        }
    }

    /**
     * @param string $tring_key
     * @return VString
     * when whant to validate string value use this module
     */
    function string(string $tring_key){
        return new VString($this->source,$tring_key);
    }

    /**
     * @param string $tring_key
     * @return VNumber
     * when want to validate numbers use this module;
     *
     */
    function number(string $tring_key){
        return new VNumber($this->source,$tring_key,$this->source[$tring_key]);
    }
    /**
     *
     * @param string $tring_key
     * @return type
     */
    function any(string $tring_key=null){
        return $this->check_undefined_Object_key($this->source,[$tring_key]);
    }

    /**
     * @param string $tring_key
     * @return VDate
     * while want to validate a date, this module will do the things
     */
    function date(string $tring_key){
        return new VDate($this->source,$tring_key,$this->source[$tring_key]);
    }

    /**
     * @param string $tring_key
     * @return VTime
     * while want to validate a time, this module will be helpfull
     */
    function time(string $tring_key){
        return new VTime($this->source,$tring_key,$this->source[$tring_key]);
    }
//
//  /**
//     *
//     * @param array $source
//     * @param array $items
//     * @return boolean
//     */
//    private function check_undefined_Object_key(array $source,array $items){
//        $diff_array_key= array_diff_key($source,$items);
//        $source_key= array_keys($diff_array_key);
//        $status_key=false;
//        if(count($source_key)>0){
//            foreach($source_key as $key){
//                $message=[
//                    "type" => "object.undefined",
//                    "message" => "`{$key}` is not defined",
//                    "label" => $key,
//                ];
//                $this->addError($message);
//                $status_key=true;
//            }
//        }
//        return $status_key;
//    }
    private function addError(array $value){
       return $this->_errors[]=$value;
    }
    
    function errors(){
        return ["error"=>$this->_errors];
    }
    /**
     * 
     * @returns boolean [true,false]
     */
    function passed(){
        return $this->_passed;
    }
}
