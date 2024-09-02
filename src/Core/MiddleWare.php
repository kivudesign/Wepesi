<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core;

use Wepesi\Core\Validation\Rules;
use Wepesi\Core\Validation\Validate;

abstract class MiddleWare
{
    protected Validate $validate;
    protected Rules $rule;

    public function __construct()
    {
        $this->rule = new Rules();
        $this->validate = new Validate();
    }
}