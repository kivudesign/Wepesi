<?php
namespace Wepesi\Middleware\Validation;

use Wepesi\Core\MiddleWare;

class exampleValidation extends MiddleWare
{
    function changeLang(){
        $rules = [
            "token" => $this->schema->string("token")
                ->min(1)
                ->max(2)
                ->required(),
            "lang" => $this->schema->string("lang")
                ->min(1)
                ->max(2)
                ->required()
        ];

        $this->validate->check($_POST,$rules);
        if(!$this->validate->passed()){
            dumper($this->validate->errors());
            exit();
        }
    }
}