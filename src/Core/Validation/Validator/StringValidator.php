<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Validator;

use Wepesi\Core\Validation\Providers\ValidatorProvider;

/**
 * Description of String
 *
 * @author Boss Ibrahim Mussa
 */
final class StringValidator extends ValidatorProvider
{
    /**
     *
     * @param string $item the item to be validated.
     * @param array $data_source the source data from where is going to check it the match key exist and have value.
     */
    public function __construct(string $item, array $data_source)
    {
        $this->errors = [];
        $this->data_source = $data_source;
        $this->field_name = $item;
        $this->field_value = $data_source[$item];
        parent::__construct();
    }

    /**
     *
     * @param int $rule
     *
     */
    public function min(int $rule): void
    {
        if ($this->positiveParamMethod($rule)) return;
        if (strlen($this->field_value) < $rule) {
            $this->messageItem
                ->type('string.min')
                ->message("`$this->field_name` should have minimum of `$rule` characters")
                ->label($this->field_name)
                ->limit($rule);
            $this->addError($this->messageItem);
        }
    }

    /**
     *
     * @param int $rule
     *
     */
    public function max(int $rule): void
    {
        if ($this->positiveParamMethod($rule, true)) return;
        if (strlen($this->field_value) > $rule) {
            $this->messageItem
                ->type('string.max')
                ->message("`$this->field_name` should have maximum of `$rule` characters")
                ->label($this->field_name)
                ->limit($rule);
            $this->addError($this->messageItem);
        }
    }

    /**
     *
     */
    public function email()
    {
        if (!filter_var($this->field_value, FILTER_VALIDATE_EMAIL)) {
            $this->messageItem
                ->type('string.email')
                ->message("`$this->field_name` should be an email.")
                ->label($this->field_name);
            $this->addError($this->messageItem);
        }
    }

    /**
     *supported link :
     * http(s)://[domain].[extension] ,
     * http(s)://www.[domain].[extension],
     * www.[domain].[extension]
     *
     */
    public function url()
    {
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->field_value)) {
            $this->messageItem
                ->type('string.url')
                ->message("'$this->field_name' this should be a link(url)")
                ->label($this->field_name);
            $this->addError($this->messageItem);
        }
    }

    /**
     *
     * @param string $key_to_match
     *
     */
    public function match(string $key_to_match)
    {
        $this->isStringAndValid($key_to_match);
        if (isset($this->data_source[$key_to_match]) && (strlen($this->field_value) != strlen($this->data_source[$key_to_match])) && ($this->field_value != $this->data_source[$key_to_match])) {
            $this->messageItem
                ->type('string.match')
                ->message("`$this->field_name` should match `$key_to_match`")
                ->label($this->field_name);
            $this->addError($this->messageItem);
        }
    }

    /**
     * @param string $ip_address
     * @return void
     */
    public function addressIp()
    {
        if (!filter_var($this->field_value, FILTER_VALIDATE_IP)) {
            $this->messageItem
                ->type('string.ip_address')
                ->message("`$this->field_name` is not a valid Ip address")
                ->label($this->field_name);
            $this->addError($this->messageItem);
        }
    }

    /**
     * @param string $ip_address
     * @return void
     */
    public function addressIpv6(string $ip_address)
    {
        if (!filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $this->messageItem
                ->type('string.ip_address_v6')
                ->message("`$this->field_name` is not a valid ip address (ipv6)")
                ->label($this->field_name);
            $this->addError($this->messageItem);
        }
    }

    /**
     *
     * @param string $item_key
     * @return void
     */
    protected function isStringAndValid(string $item_key): void
    {
        $field_to_check = !$item_key ? $this->field_name : $item_key;
        $regex = '#[a-zA-Z0-9]#';
        if (!isset($this->data_source[$field_to_check])) {
            $this->messageItem
                ->type('string.unknown')
                ->message("`$field_to_check` is not valid")
                ->label($field_to_check);
            $this->addError($this->messageItem);
        } else if (!preg_match($regex, $this->data_source[$field_to_check]) || strlen(trim($this->field_value)) == 0) {
            $this->messageItem
                ->type('string.unknown')
                ->message("`$field_to_check` should be a string")
                ->label($field_to_check);
            $this->addError($this->messageItem);
        }
    }

    protected function classProvider(): string
    {
        return 'string';
    }
}
