<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core\Http;

use Wepesi\Core\Application;
use Wepesi\Core\Http\Providers\BaseControllerMiddleware;
use Wepesi\Core\Validation\Rules;
use Wepesi\Core\Validation\Validate;

abstract class MiddleWare extends BaseControllerMiddleware
{
    protected Validate $validate;
    protected Rules $rule;

    /**
     * Framework service initializer.
     *
     * This method is called automatically by the DI container after the middleware
     * is created, so child middleware classes do not need to call parent::__construct().
     *
     * @return void
     */
    final public function __wepesiInit(): void
    {
        // Initialize base services
        $this->initializeBaseServices();
        //

        $this->validate = Application::make(Validate::class);
        $this->rule = Application::make(Rules::class);
    }
}