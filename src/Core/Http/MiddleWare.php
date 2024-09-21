<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core\Http;

use Wepesi\Core\Http\Providers\BaseControllerMiddleware;
use Wepesi\Core\Validation\Rules;
use Wepesi\Core\Validation\Validate;

abstract class MiddleWare extends BaseControllerMiddleware
{
    protected Validate $validate;
    protected Rules $rule;

    public function __construct()
    {
        parent::__construct();
        $this->rule = new Rules();
        $this->validate = new Validate();
    }
}