<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wepesi\Core\Validation\Validator;

use Wepesi\Core\Validation\Providers\ValidatorProvider;

/**
 * Description of VNumber
 *
 * @author Boss Ibrahim Mussa
 */
final class NumberValidator extends ValidatorProvider
{

    /**
     * @param string $item
     * @param array $data_source
     */
    public function __construct(string $item, array $data_source)
    {
        parent::__construct();
        $this->data_source = $data_source;
        $this->field_name = $item;
        $this->field_value = (int)$data_source[$item];
        $this->isNumber();
    }

    /**
     * @return bool
     */
    protected function isNumber(): bool
    {
        $regex_string = '#[a-zA-Z]#';
        if (preg_match($regex_string, trim($this->data_source[$this->field_name])) || ((int)$this->data_source[$this->field_name] !== $this->field_value)) {
            $this->messageItem
                ->type('number.unknown')
                ->message("`$this->field_name` should be a number")
                ->label($this->field_name);
            $this->addError($this->messageItem);
            return false;
        }
        return true;
    }

    /**
     * @param int $rule
     * @return void
     */
    public function min(int $rule)
    {
        if ((int)$this->field_value < $rule) {
            $this->messageItem
                ->type('number.min')
                ->message("`$this->field_name` should be greater than `$rule`")
                ->label($this->field_name)
                ->limit($rule);
            $this->addError($this->messageItem);
        }
    }

    /**
     * @param int $rule
     * @return void
     */
    public function max(int $rule)
    {
        if ((int)$this->field_value > $rule) {
            $this->messageItem
                ->type('number.max')
                ->message("`$this->field_name` should be less than `$rule`")
                ->label($this->field_name)
                ->limit($rule);
            $this->addError($this->messageItem);
        }
    }

    /**
     *
     */
    public function positive()
    {
        if ((int)$this->field_value < 0) {
            $this->messageItem
                ->type('number.positive')
                ->message("`$this->field_name` should be a positive number")
                ->label($this->field_name)
                ->limit(1);
            $this->addError($this->messageItem);
        }
    }

    /**
     * @return string
     */
    protected function classProvider(): string
    {
        return 'number';
    }
}
