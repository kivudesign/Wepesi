<?php

namespace Wepesi\Middleware\Validation;

use Wepesi\Core\Application;
use Wepesi\Core\MiddleWare;

class exampleValidation extends MiddleWare
{
    function changeLang()
    {
        $schema = [
            "token" => $this->rule->string("token")
                ->min(5)
                ->max(30)
                ->required(),
            "lang" => $this->rule->string("lang")
                ->min(1)
                ->max(2)
                ->required()
        ];

        $this->validate->check($_POST, $schema);
        if (!$this->validate->passed()) {
            print_r($this->validate->errors());
        }
    }
}