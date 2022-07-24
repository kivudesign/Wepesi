<?php

namespace Wepesi\Core\Validation;

use Wepesi\Core\Orm\DB;

/**
 * Description of String
 *
 * @author Ibrahim
 */
class VString implements IValidation
{
    private ?string $string_value;
    private string $string_item;
    private array $source_data;
    private array $_errors;
    private ?DB $db;

    function __construct(array $source, string $string_item = 'null')
    {
        $this->string_value = $source[$string_item] ?? null;
        $this->string_item = $string_item;
        $this->source_data = $source;
        $this->db = DB::getInstance();
        $this->checkExist();
    }

    function min(int $rule_values = 0): VString
    {
        $min = is_integer($rule_values) ? ((int)$rule_values > 0 ? (int)$rule_values : 0) : 0;
        if (strlen($this->string_value) < $min) {
            $message = [
                'type' => 'string.min',
                'message' => "`{$this->string_item}` must be a minimum of `{$min}` characters",
                'label' => $this->string_item,
                'limit' => $min
            ];
            $this->addError($message);
        }
        return $this;
    }

    function max(int $rule_values = 1): VString
    {
        $max = is_integer($rule_values) ? ((int)$rule_values > 0 ? (int)$rule_values : 0) : 0;
        if (strlen($this->string_value) > $max) {
            $message = [
                'type' => 'string.max',
                'message' => "`{$this->string_item}` must be a maximum of `{$max}` characters",
                'label' => $this->string_item,
                'limit' => $max
            ];
            $this->addError($message);
        }
        return $this;
    }

    function email(): VString
    {
        if (!filter_var($this->string_value, FILTER_VALIDATE_EMAIL)) {
            $message = [
                'type' => 'string.email',
                'message' => "`{$this->string_item}` must be an email",
                'label' => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }

    function url(): VString
    {
        if (!preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $this->string_value)) {
            $message = [
                'type' => 'string.url',
                'message' => "`{$this->string_item}` should be an url",
                'label' => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }

    function match(string $key_to_match): VString
    {
        $this->checkExist($key_to_match);
        if (isset($this->source_data[$key_to_match]) && (strlen($this->string_value) != strlen($this->source_data[$key_to_match])) && ($this->string_value != $this->source_data[$key_to_match])) {
            $message = [
                'type' => 'string.match',
                'message' => "`{$this->string_item}` must match {$key_to_match}",
                'label' => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }

    /**
     * @return $this
     * call this module is the input is requied and should not be null or empty
     */
    function required(): VString
    {
        $required_value = trim($this->string_value);
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

    function unique(string $table_name): VString
    {
        $check_uniq = $this->db->get($table_name)->where([$this->string_item, '=', $this->string_value])->result();
        if (count($check_uniq)) {
            $message = [
                'type' => 'string.unique',
                'message' => "`{$this->string_item}` = `{$this->string_value}` already exist,it should be unique",
                'label' => $this->string_item,
            ];
            $this->addError($message);
        }
        return $this;
    }

//    private methode
    private function checkExist(string $itemKey = null): bool
    {
        $item_to_check = $itemKey ?? $this->string_item;
        $regex = '#[a-zA-Z0-9]#';
        $message = [
            'message' => "`{$item_to_check}` is unknown",
            'label' => $item_to_check
        ];
        if (!isset($this->source_data[$item_to_check])) {
            $message ['type'] = 'any.unknown';
        } else if (!preg_match($regex, $this->source_data[$item_to_check]) || strlen(trim($this->source_data[$item_to_check])) == 0) {
            $message ['type'] = 'string.unknown';
        }
        $this->addError($message);
        return true;
    }

    private function addError(array $value):void
    {
         $this->_errors[] = $value;
    }

    function check():array
    {
        return $this->_errors;
    }
}