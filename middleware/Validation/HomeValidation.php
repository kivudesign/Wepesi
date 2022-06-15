<?php
namespace wepesi\Validation;

use Wepesi\Core\Validation\Validate;

class HomeValidation
{
    function changeLang(){
        $valid = new Validate();
        $schema = [
            "token" => $valid->string("token")
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

        $valid->check($_POST,$schema);
        if(!$valid->passed()){
            var_dump($valid->errors());
            die();
        }
    }
}