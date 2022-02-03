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
class VNumber extends ABIValidation {
    //put your code here
    private $string_value;
    private string $string_item;
    private array $source_data;
    private int $_min,$_max;
    private object $lang;
    private ?DB $db;

    function __construct(array $source,string $string_item) {
        $this->db=DB::getInstance();
        $this->lang= (object)LANG_VALIDATE;
        if(!isset($source[$string_item])){
            return $this->checkExist($string_item);
        }
        $this->source_data=$source;
        $this->string_value=isset($source[$string_item])?(int)$source[$string_item]:"";
        $this->string_item=$string_item;
        $this->_max= $this->_min=0;
    }

    /**
     * @param int $min_values
     * @return $this
     */
    function min(int $min_values=0){
        if ((int) $this->string_value < $min_values) {
            $message=[
                "type"=>"number.min",
                "message"=> "`{$this->string_item}` {$this->lang->number_min}  `{$min_values}`",
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
    function max(int $maximum_value=1){
        if ((int)$this->string_value > $maximum_value) {
            $message=[
                "type"=>"number.max",
                "message"=> "`{$this->string_item}` {$this->lang->number_max}  `{$maximum_value}`",
                "label"=>$this->string_item,
                "limit"=>$maximum_value
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @return $this
     */
    function positive(){
        if ($this->string_value < 1) {
            $message=[
                "type"=>"number.positive",
                "message"=> "`{$this->string_item}` {$this->lang->number_positive}",
                "label"=>$this->string_item,
                "minimum"=>1
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @param string $table_name
     * @return void
     */
    function unique(string $table_name){
        $check_uniq=$this->db->get($table_name)->where([$this->string_item,'=',$this->string_value])->result();
        if(count($check_uniq)){
            $message = [
                "type"=> "numer.unique",
                "message" => "`{$this->string_item}` {$this->lang->unique}",
                "label" => $this->string_item,
            ];
            $this->addError($message);
        }
    }

    /**
     * @return $this
     */
    function required(){
        $required_value= $this->string_value;
        if (empty($required_value) && $required_value!=0) {
            $message = [
                "type"=> "number.required",
                "message" => "`{$this->string_item}` {$this->lang->required}",
                "label" => $this->string_item
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @param string|null $itemKey
     * @return bool
     */
    private function checkExist(string $itemKey=null){
        $item_to_check=$itemKey??$this->string_item;
        $regex_string="#[a-zA-Z]#";
        if (!isset($this->source_data[$item_to_check])) {
            $message = [
                "type"=> "any.unknown",
                "message" => "`{$item_to_check}` {$this->lang->unknown}",
                "label" => $item_to_check,
            ];
            $this->addError($message);
        }
        return true;
    }
}
