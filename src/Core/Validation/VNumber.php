<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wepesi\Core\Validation;

use Wepesi\Core\Orm\DB;

/**
 * Description of VNumber
 *
 * @author Lenovo
 */
class VNumber {
    //put your code here
    private int $string_value;
    private string $string_item;
    private array $source_data;
    private array $_errors;
    private ?DB $db;

    function __construct(array $source,string $string_item) {
        $this->source_data=$source;
        $this->string_value=(int)$source[$string_item];
        $this->string_item=$string_item;
        $this->db=DB::getInstance();
        $this->checkExist();
    }

    /**
     * @param int $min_values
     * @return $this
     */
    function min(int $min_values): VNumber
    {
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

    /**
     * @param int $maximum_value
     * @return $this
     */
    function max(int $maximum_value): VNumber
    {
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
    function positive(): VNumber
    {
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

    /**
     * @param string $table_name
     * @throws \Exception
     */
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
    function required(): VNumber
    {
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
    private function checkExist(string $itemKey=null): bool
    {
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
    private function addError(array $value):void{
       $this->_errors[]=$value;
    }
    function check(): array
    {
        return  $this->_errors;
    }
}
