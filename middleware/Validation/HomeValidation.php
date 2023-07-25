<?php
namespace Wepesi\Middleware\Validation;

use Wepesi\Core\MiddleWare;
use Wepesi\Core\Validation\Validate;

class HomeValidation extends MiddleWare
{
    function changeLang(){
        $rules = [
            "token" => $this->schema->string("token")
                ->min(1)
                ->max(2)
                ->required()
                ->check(),
            "lang" => $valid->string("lang")
                ->min(1)
                ->max(2)
                ->required()
                ->check()
        ];

        $this->validate->check($_POST,$rules);
        if(!$this->validate->passed()){
            dumper($this->validate->errors());
            die();
        }
    }
}