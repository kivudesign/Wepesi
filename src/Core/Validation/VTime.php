<?php


namespace Wepesi\Core\Validation;


class VTime implements IValidation
{
    private $time_value;
    private string $string_item;
    private array $source_data;
    private array $_errors;

    function __construct(array $source, string $string_item)
    {
        $this->time_value = $source[$string_item];
        $this->string_item = $string_item;
        $this->source_data = $source;
        $this->checkExist();
    }

    /**
     * @return $this
     */
    function now(): VTime
    {
        $min_date_time = strtotime('now');
        $min_date = date('H:i', $min_date_time);
        $date_value_time = strtotime($this->time_value);
        if ($date_value_time > $min_date_time) {
            $message = [
                'type' => 'date.now',
                'message' => "`{$this->string_item}` {$this->lang->date_now} ",
                'label' => $this->string_item,
                'limit' => $min_date
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
    function min(string $rule_values = 'now'): VTime
    {
        $regex = '#[a-zA-Z]#';
        $time = preg_match($regex, $rule_values);
//        $con=!$time?$time:(int)$time;
        $min_time = date('H:i', strtotime($rule_values));
        $time_value_time = date('H:i', strtotime($this->time_value));
        /**
         * format time to display
         */
//        $minimum_time_value=date("H:i",$min_time);
        if ($time_value_time < $min_time) {
            $message = [
                'type' => 'date.min',
                'message' => "`{$this->string_item}` must be a minimum of `{$min_time}`",
                'label' => $this->string_item,
                'limit' => $min_time
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @param int $rule_values
     * @return $this
     * while try to check maximum date of a defined periode use this module
     */
    function max(string $rule_values = 'now'): VTime
    {
        $regex = '#[a-zA-Z]#';
        $time = preg_match($regex, $rule_values);
//        $con=!$time?$time:(int)$time;
        $max_date_time = strtotime($rule_values);
        $max_date = date('H:i', $max_date_time);
        $date_value_time = strtotime($this->time_value);
        if ($max_date_time < $date_value_time) {
            $message = [
                'type' => 'date.max',
                'message' => "`{$this->string_item}` must be a maximum of `{$max_date}`",
                'label' => $this->string_item,
                'limit' => $max_date
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @return $this
     * call this module is the input is requied and should not be null or empty
     */
    function required(): VTime
    {
        $required_value = trim($this->time_value);
        if (empty($required_value) || strlen($required_value) == 0) {
            $message = [
                'type' => 'any.required',
                'message' => "`{$this->string_item}` is required",
                'label' => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @param string $greater_time_to
     * @return $this
     * check if the time is greater the defined
     */
    function greaterThan(string $greater_time_to = 'now'): VTime
    {
        $time_value_to_check = date('H:i:s', strtotime($this->time_value));
        $time_now = date('H:i:s', strtotime('now'));
        /**
         * format date to be display
         */
//        $formated_date=date("H:i",$time_now);
        if ($time_now >= $time_value_to_check) {
            $message = [
                'type' => 'any.required',
                'message' => "`{$this->string_item}` should be greater than {$time_now}",
                'label' => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @param string $greater_time_to
     * @return $this
     * check if the time is less than defined
     */
    function lessThan(string $greater_time_to = 'now'): VTime
    {
        $time_value_to_check = date('H:i:s', strtotime($this->time_value));
        $time_now = date('H:i:s', strtotime('now'));
        /**
         * format date to be display
         */
//        $formated_date=date("H:i",$time_now);
        if ($time_now <= $time_value_to_check) {
            $message = [
                'type' => 'any.required',
                'message' => "`{$this->string_item}` should be less than {$time_now}",
                'label' => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @param string|null $itemKey
     * @return bool
     * check if the define key exist
     */
    private function checkExist(string $itemKey = null): bool
    {
        $item_to_check = $itemKey ? $itemKey : $this->string_item;
        $regex = '#[a-zA-Z0-9]#';
        $message = [
            'message' => "`{$item_to_check}` is unknown",
            'label' => $item_to_check,
        ];
        if (!isset($this->source_data[$item_to_check])) {
            $message ['type'] = 'any.unknown';
        } else if (!preg_match($regex, $this->source_data[$item_to_check]) || strlen(trim($this->source_data[$item_to_check])) == 0) {
            $message['type'] = 'date.unknown';
        }
        $this->addError($message);
        return true;
    }

    private function addError(array $value):void
    {
         $this->_errors[] = $value;
    }

    function check(): array
    {
        return $this->_errors;
    }
}