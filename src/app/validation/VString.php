<?php
namespace Wepesi\App\Core;
/**
 * Description of String
 *
 * @author Ibrahim
 */
class VString extends ABIValidation {
    private $string_value;
    private ?string $string_item;
    private array $source_data;
    private int $_min,$_max;
    //put your code here
    private object $lang;
    private ?DB $db;

    function __construct(array $source,string $string_item=null) {
        $this->db=DB::getInstance();
        $this->lang= (object)LANG_VALIDATE;
        if(!isset($source[$string_item])){
            return $this->checkExist($string_item);
        }
        $this->string_value=$source[$string_item];
        $this->string_item=$string_item;
        $this->source_data=$source;
        $this->_max= $this->_min=0;
    }

    /**
     * @param int $rule_values
     * @return $this
     */
    function min(int $rule_values=0){
        if (strlen($this->string_value) < $rule_values) {
            $message=[
                "type"=>"string.min",
                "message"=> "`{$this->string_item}` {$this->lang->string_min} `{$rule_values}` characters",
                "label"=>$this->string_item,
                "limit"=>$rule_values
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @param int $rule_values
     * @return $this
     */
    function max(int $rule_values=1){
        if (strlen($this->string_value) > $rule_values) {
            $message = [
                "type" => "string.max",
                "message" => "`{$this->string_item}` {$this->lang->string_max} `{$rule_values}` characters",
                "label" => $this->string_item,
                "limit" => $rule_values
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @return $this
     */
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

    /**
     * @return $this
     */
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

    /**
     * @param string $key_tomatch
     * @return $this
     */
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

    /**
     * @param string $table_name
     * @return $this
     */
    function unique(string $table_name){
        $check_uniq=$this->db->get($table_name)->where([$this->string_item,'=',$this->string_value])->result();
        if(count($check_uniq)){
            $message = [
                "type"=> "string.unique",
                "message" => "`{$this->string_item}` {$this->lang->unique}",
                "label" => $this->string_item,
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
}
