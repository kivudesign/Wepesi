<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */


namespace Wepesi\Core\Validation\Validator;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Wepesi\Core\Validation\Providers\ValidatorProvider;

/**
 * Validate boolean schema
 *
 * @author Domeshow
 */
final class BooleanValidator extends ValidatorProvider
{

    /**
     * @param string $item
     * @param array $source
     */
    function __construct(string $item, array $source)
    {
        parent::__construct();
        $this->field_name = $item;
        $this->data_source = $source;
        $this->field_value = $source[$this->field_name];

        if ($this->isBoolean()) {
            $this->field_value = $source[$item];
        }
    }

    /**
     * @param $rule
     * @return void
     */
    public function min($rule)
    {
    }

    /**
     * @param $rule
     * @return void
     */
    public function max($rule)
    {
    }

    /**
     *
     * @param string|null $itemKey
     * @return boolean
     */
    private function isBoolean(string $itemKey = null): bool
    {
        $item_to_check = !$itemKey ? $this->field_name : $itemKey;
        $val = $this->data_source[$item_to_check];

        $regex = "/^(true|false)$/";
        if (!preg_match($regex, is_bool($val) ? ($val ? 'true' : 'false') : $val)) {
            $this->messageItem
                ->type('boolean.unknown')
                ->message("`$item_to_check` should be a boolean")
                ->label($item_to_check);
            $this->addError($this->messageItem);
            return false;
        }
        return true;
    }

    /**
     * @param string $value
     * @return void
     */
    public function isValid(string $value)
    {
        $passed_value = is_bool($value) ? ($value ? 'true' : 'false') : $value;
        $required_value = strtolower($passed_value);
        $check = $required_value == 'true' || $required_value == 'false';
        if (!($check)) {
            $this->messageItem
                ->type('boolean.required')
                ->message("isValid param must be boolean but you put `$required_value`")
                ->label($this->field_name);
            $this->addError($this->messageItem);
        } else {
            // $sting_value= is_bool($this->string_value);
            $incoming_value = $this->field_value ? 'true' : 'false';

            if ($incoming_value != $required_value) {
                $this->messageItem
                    ->type('boolean.valid')
                    ->message("`$incoming_value` is not validValue required. You must put `$required_value`")
                    ->label($this->field_name);
                $this->addError($this->messageItem);
            }
        }
    }

    /**
     * @return string
     */
    protected function classProvider(): string
    {
        return 'boolean';
    }
}
