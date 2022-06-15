<?php


namespace Wepesi\Core\Validation;

class VDate extends ABIValidation
{
    private $date_value;
    private ?string $string_item;
    private array $source_data;
    private int $_min, $_max;
    private object $lang;

    function __construct(array $source,string $string_item=null) {
        $this->lang= (object)LANG_VALIDATE;
        if(!isset($source[$string_item])){
            return $this->checkExist($string_item);
        }
        $this->date_value=$source[$string_item];
        $this->string_item=$string_item;
        $this->source_data=$source;
        $this->_max= $this->_min=0;
    }

    /**
     * @return $this
     */
    function now(){
        try{
            $min_date_time=strtotime("now");
            $min_date=date("d/F/Y",$min_date_time);
            $date_value_time= strtotime($this->date_value);
            if ($date_value_time > $min_date_time) {
                $message=[
                    "type"=>"date.now",
                    "message"=> "`{$this->string_item}` {$this->lang->date_now} ",
                    "label"=>$this->string_item,
                    "limit"=>$min_date
                ];
                $this->addError($message);
            }
        }catch (\Exception $ex){
            $message=[
                "type"=>"date.now",
                "message"=> $ex,
                "label"=>$this->string_item,
                "limit"=>"now"
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @param string|null $times
     * @return $this
     * while trying to get day validation use this module
     */
    function today(string $times=null){
        try{
            $regeg = "#+[0-9]h:[0-9]min:[0-9]sec#";
            $min_date_time = strtotime("now {$times}");
            $min_date = date("d/F/Y", $min_date_time);
            $date_value_time = strtotime($this->date_value);
            if ($date_value_time > $min_date_time) {
                $message = [
                    "type" => "date.now",
                    "message" => "`{$this->string_item}` {$this->lang->date_today} ",
                    "label" => $this->string_item,
                    "limit" => $min_date
                ];
                $this->addError($message);
            }
        }catch (\Exception $ex){
            $message = [
                "type" => "date.now",
                "message" => $ex,
                "label" => $this->string_item,
                "limit" => $min_date
            ];
            $this->addError($message);
        }
        return $this;
    }
    /**
     * @param string $rule_values
     * @return $this
     * get the min date control from the given date
     */
    function min(string $rule_values="now"){
        try{
            $regex = "#[a-zA-Z]#";
            $time = preg_match($regex, $rule_values);
            $minimum_date = date("Y-m-d", strtotime($rule_values));
            $date_value_time = date("Y-m-d", strtotime($this->date_value));
            /**
             * format date time to be display (fr):format
             */
            $min_date_value = date_format(date_create($rule_values), "d/F/Y");
            if ($minimum_date > $date_value_time) {
                $message = [
                    "type" => "date.min",
                    "message" => "`{$this->string_item}` {$this->lang->date_min} `{$min_date_value}`",
                    "label" => $this->string_item,
                    "limit" => $min_date_value
                ];
                $this->addError($message);
            }
        }catch (\Exception $ex){
            $message = [
                "type" => "date.min",
                "message" => $ex,
                "label" => $this->string_item,
                "limit" => 0
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @param int $rule_values
     * @return $this
     * while try to check maximum date of a defined period use this module
     */
    function max(int $rule_values=1){
        try{
            $regex = "#[a-zA-Z]#";
            $time = preg_match($regex, $rule_values);
//        $con=!$time?$time:(int)$time;
            $maximum_value = strtotime($rule_values);
            $date_max = date("d/F/Y", $maximum_value);
            $date_value_time = strtotime($this->date_value);
            if ($maximum_value < $date_value_time) {
                $message = [
                    "type" => "date.max",
                    "message" => "`{$this->string_item}` {$this->lang->date_max} `{$date_max}`",
                    "label" => $this->string_item,
                    "limit" => $date_max
                ];
                $this->addError($message);
            }
        }catch (\Exception $ex){
            $message = [
                "type" => "date.max",
                "message" => $ex,
                "label" => $this->string_item,
                "limit" => $date_max
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
        try{
            $required_value = trim($this->date_value);
            if (empty($required_value) || strlen($required_value) == 0) {
                $message = [
                    "type" => "any.required",
                    "message" => "`{$this->string_item}` {$this->lang->required}",
                    "label" => $this->string_item,
                ];
                $this->addError($message);
            }
        }catch (\Exception $ex){
            $message = [
                "type" => "any.required",
                "message" => $ex,
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
        try{
            $item_to_check = $itemKey ? $itemKey : $this->string_item;
            $regex = "#[a-zA-Z0-9]#";
            if (!isset($this->source_data[$item_to_check])) {
                $message = [
                    "type" => "any.unknown",
                    "message" => "`{$item_to_check}` {$this->lang->unknown}",
                    "label" => $item_to_check,
                ];
                $this->addError($message);
            } else if (!preg_match($regex, $this->source_data[$item_to_check]) || strlen(trim($this->source_data[$item_to_check])) == 0) {
                $message = [
                    "type" => "date.unknown",
                    "message" => "`{$item_to_check}` {$this->lang->string_unknown}",
                    "label" => $item_to_check,
                ];
                $this->addError($message);
            }
            return true;
        }catch (\Exception $ex){
            $message = [
                "type" => "date.unknown",
                "message" => $ex,
                "label" => $item_to_check,
            ];
            $this->addError($message);
            return false;
        }
    }
}