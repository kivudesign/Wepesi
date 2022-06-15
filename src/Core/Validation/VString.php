<?php
namespace Wepesi\Core\Validation;
use Wepesi\Core\Orm\DB;

/**
 * Description of String
 *
 * @author Ibrahim
 */
class VString implements IValidation{
    private $string_value;
    private $string_item;
    private $source_data;
    private $_errors;
    private $_min;
    private $_max;
    //put your code here
    function __construct(array $source,string $string_item=null) {
        $this->string_value=$source[$string_item];
        $this->string_item=$string_item;
        $this->source_data=$source;
        $this->_max= $this->_min=0;
        $this->db=DB::getInstance();
        $this->lang= (object)LANG_VALIDATE;
        $this->checkExist();
    }
    function min(int $rule_values=0){
        $min=is_integer($rule_values)? ((int)$rule_values>0?(int)$rule_values:0):0;
        if (strlen($this->string_value) < $min) {
            $message=[
                "type"=>"string.min",
                "message"=> "`{$this->string_item}` {$this->lang->string_min} `{$min}` characters",
                "label"=>$this->string_item,
                "limit"=>$min
            ];
            $this->addError($message);
        }
        return $this;
    }
    
    function max(int $rule_values=1){
        $max = is_integer($rule_values) ? ((int)$rule_values > 0 ? (int)$rule_values : 0):0;
        $this->_max=$max; 
        if (strlen($this->string_value) > $max) {
            $message = [
                "type" => "string.max",
                "message" => "`{$this->string_item}` {$this->lang->string_max} `{$max}` characters",
                "label" => $this->string_item,
                "limit" => $max
            ];
            $this->addError($message);
        }
        return $this;
    }

    function email(){
        if (!filter_var($this->string_value, FILTER_VALIDATE_EMAIL)) {
            $message = [
                "type" => "string.email",
                "message" => "`{$this->string_item}` {$this->lang->email}",
                "label" => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }
    function url(){
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->string_value)) {
            $message = [
                "type" => "string.url",
                "message" => "`{$this->string_item}` {$this->lang->url}",
                "label" => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }
    function match(string $key_tomatch){
        $this->checkExist($key_tomatch);
        if (isset($this->source_data[$key_tomatch]) && (strlen($this->string_value)!= strlen($this->source_data[$key_tomatch])) && ($this->string_value!=$this->source_data[$key_tomatch])) {
            $message = [
                "type" => "string.match",
                "message" => "`{$this->string_item}` {$this->lang->matches} {$key_tomatch}",
                "label" => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @return $this
     * call this module is the input is requied and should not be null or empty
     */
    function required(){
        $required_value= trim($this->string_value);
        if (empty($required_value) || strlen($required_value)==0) {
            $message = [
                "type"=> "any.required",
                "message" => "`{$this->string_item}` {$this->lang->required}",
                "label" => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }
    function unique(string $table_name){
        $check_uniq=$this->db->get($table_name)->where([$this->string_item,'=',$this->string_value])->result();
        if(count($check_uniq)){
            $message = [
                "type"=> "string.unique",
                "message" => "`{$this->string_item}`= `{$this->string_value}` {$this->lang->unique}",
                "label" => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }
//    private methode
    private function checkExist(string $itemKey=null){
        $item_to_check=$itemKey?$itemKey:$this->string_item;
        $regex="#[a-zA-Z0-9]#";
        if (!isset($this->source_data[$item_to_check])) {
            $message = [
                "type"=> "any.unknown",
                "message" => "`{$item_to_check}` {$this->lang->unknown}",
                "label" => $item_to_check,
            ];
            $this->addError($message);
        }else if(!preg_match($regex,$this->source_data[$item_to_check]) || strlen(trim($this->source_data[$item_to_check]))==0){
            $message=[
                    "type" => "string.unknown",
                    "message" => "`{$item_to_check}` {$this->lang->string_unknown}",
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
