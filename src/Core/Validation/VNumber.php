<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wepesi\Core\Validation;

/**
 * Description of VNumber
 *
 * @author Lenovo
 */
class VNumber {
    //put your code here
    private $string_value;
    private $string_item;
    private $source_data;
    private $_errors;
    private $_min;
    private $_max;
    
    function __construct(array $source,string $string_item) {
        $this->source_data=$source;
        $this->string_value=(int)$source[$string_item];
        $this->string_item=$string_item;
        $this->_max= $this->_min=0;
        $this->db=DB::getInstance();
        $this->lang= (object)LANG_VALIDATE;
        $this->checkExist();
    }
    function min(int $min_values){
        if ((int) $this->string_value < $min_values) {
            $message=[
                "type"=>"number.min",
                "message"=> "`{$this->string_item}` must be a minimum of `{$min_values}`",
                "label"=>$this->string_item,
                "limit"=>$min_values
            ];
            $this->addError($message);
        }
        return $this;
    }
    function max(int $maximum_value){
        if ($this->string_value > $maximum_value) {
            $message=[
                "type"=>"number.max",
                "message"=> "`{$this->string_item}` must be a maximum of  `{$maximum_value}`",
                "label"=>$this->string_item,
                "limit"=>$maximum_value
            ];
            $this->addError($message);
        }
        return $this;
    }
    function positive(){
        if ($this->string_value < 1) {
            $message=[
                "type"=>"number.positive",
                "message"=> "`{$this->string_item}` should be positive",
                "label"=>$this->string_item,
                "minimum"=>1
            ];
            $this->addError($message);
        }
        return $this;
    }
    function unique(string $table_name){
        $check_uniq=$this->db->get($table_name)->where([$this->string_item,'=',$this->string_value])->result();
        if(count($check_uniq)){
            $message = [
                "type"=> "numer.unique",
                "message" => "`{$this->string_item}` already exist,it should be unique",
                "label" => $this->string_item,
            ];
            $this->addError($message);
        }
    }
    function required(){
        $required_value= $this->string_value;
        if (empty($required_value) && $required_value!=0) {
            $message = [
                "type"=> "any.required",
                "message" => "`{$this->string_item}` is required",
                "label" => $this->string_item
            ];
            $this->addError($message);
        }
        return $this;
    }
//    
    private function checkExist(string $itemKey=null){
        $item_to_check=$itemKey?$itemKey:$this->string_item;
        $regex_string="#[a-zA-Z]#";
        if (!isset($this->source_data[$item_to_check])) {
            $message = [
                "type"=> "any.unknown",
                "message" => "`{$item_to_check}` is unknown",
                "label" => $item_to_check,
            ];
            $this->addError($message);
        }
        return true;
    }
    private function addError(array $value){
       return $this->_errors[]=$value;
    }
    function check(){
        return  $this->_errors;
    }
}
