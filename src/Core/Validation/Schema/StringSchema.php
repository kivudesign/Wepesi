<?php
/*
 * Copyright (c) 2022.  Wepesi validation.
 *  @author Boss Ibrahim Mussa
 */

namespace Wepesi\Core\Validation\Schema;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Wepesi\Core\Validation\Providers\SChemaProvider;


/**
 * String valiation schema
 * validate string value
 */
final class StringSchema extends SChemaProvider
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }

    /**
     * @return $this
     */
    public function email(): StringSchema
    {
        $this->schema[$this->class_name]["email"] = true;
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function url(): StringSchema
    {
        $this->schema[$this->class_name]["url"] = true;
        return $this;
    }

    /**
     *
     * @param string $key_to_match
     * @return $this
     */
    public function match(string $key_to_match): StringSchema
    {
        $this->schema[$this->class_name]["match"] = $key_to_match;
        return $this;
    }

    /**
     * @param string $ip_address
     * @return $this
     */
    public function addressIp(bool $ipv6 = false): StringSchema
    {
        if ($ipv6) {
            $this->schema[$this->class_name]['addressIpv6'] = true;
        } else {
            $this->schema[$this->class_name]['addressIp'] = true;
        }
        return $this;
    }

    /**
     * @param string $table_name
     * @return VString
     */
    public function unique(string $table_name): StringSchema
    {
        $this->schema[$this->class_name]['unique'] = $table_name;
        return $this;
    }
}