<?php
namespace wepesi\Validation;

use Wepesi\Core\Input;
use Wepesi\Core\Redirect;
use Wepesi\Core\Session;
use Wepesi\Core\Validation\Validate;

class HomeValidation
{
    function changeLang(){
        if(Input::get("token")=="fr"){
            Session::put("errors","Middleware validation error");
        }
    }
}