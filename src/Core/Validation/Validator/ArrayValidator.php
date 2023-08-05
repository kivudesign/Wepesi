<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Validator;

use Wepesi\Core\Validation\Providers\ValidatorProvider;
use Wepesi\Core\Validation\Validate;

/**
 * Validate array schema
 */
final class ArrayValidator extends ValidatorProvider
{
    /**
     * @param string $item field name
     * @param array $data_source resource where data will come from
     */
    public function __construct(string $item, array $data_source = [])
    {
        parent::__construct();
        $this->errors = [];
        $this->data_source = $data_source;
        $this->field_name = $item;
        $this->field_value = $data_source[$item];
    }

    /**
     * check the minimum length of an array should be about
     * @param int $rule
     * @return void
     */
    public function min(int $rule): void
    {
        // TODO: Implement min() method.
        if ($this->checkNotPositiveParamMethod($rule)) return;
        if (count($this->field_value) < $rule) {
            $this->messageItem
                ->type('array.min')
                ->message("`$this->field_name` should have a minimum of `$rule` elements")
                ->label($this->field_name)
                ->limit($rule);
            $this->addError($this->messageItem);
        }
    }

    /**
     * @param int $rule
     * @return void
     */
    public function max(int $rule): void
    {
        // TODO: Implement max() method.
        if ($this->checkNotPositiveParamMethod($rule, true)) return;
        if (count($this->field_value) > $rule) {
            $this->messageItem
                ->type('array.max')
                ->message("`$this->field_name` should have a maximum of `$rule` elements")
                ->label($this->field_name)
                ->limit($rule);
            $this->addError($this->messageItem);
        }
    }

    /**
     * @param array $elements validate an array if elements, it should be and array with key value to be well set
     * @return void
     */
    public function structure(array $elements): void
    {
        $validate = new Validate();
        $element_source = $this->data_source[$this->field_name];
        $validate->check($element_source, $elements);
        if (!$validate->passed()) {
            foreach ($validate->errors() as $error) $this->addError($error);
        }
    }

    /**
     * check content are string, in other case handler an error
     * @return void
     */
    public function string(): void
    {
        $len = count($this->field_value);
        if ($len < 1) {
            $this->min(1);
        } else {
            $filter = array_filter($this->field_value, function ($element) {
                if (!is_string($element)) return $element;
            });
            $keys = array_keys($filter);
            for ($i = 0; $i < count($keys); $i++) {
                $position = $keys[$i];
                $this->messageItem
                    ->type('array.string')
                    ->message("`$this->field_name[$position]` should be a string")
                    ->label($this->field_name)
                    ->limit(1);
                $this->addError($this->messageItem);
            }
        }
    }

    /**
     * @return void
     */
    public function number(): void
    {
        $len = count($this->field_value);
        if ($len < 1) {
            $this->min(1);
        } else {
            $filter = array_filter($this->field_value, function ($element) {
                if (!is_numeric($element)) return $element;
            });

            $keys = array_keys($filter);
            for ($i = 0; $i < count($keys); $i++) {
                $position = $keys[$i];
                $this->messageItem
                    ->type('array.number')
                    ->message("`$this->field_name[$position]` should be a number")
                    ->label($this->field_name)
                    ->limit(count($keys));
                $this->addError($this->messageItem);
            }
        }
    }

    /**
     * @return string
     */
    protected function classProvider(): string
    {
        return 'array';
    }
}