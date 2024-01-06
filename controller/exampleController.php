<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

/**
 * Wepesi Home Controller
 *
 */

namespace Wepesi\Controller;


use Wepesi\Core\Application;
use Wepesi\Core\Http\Input;
use Wepesi\Core\Http\Redirect;
use Wepesi\Core\Session;
use Wepesi\Models\Message;
use Wepesi\Models\Roles;
use Wepesi\Models\Users;

class exampleController
{

    /**
     * @return void
     */
    function home()
    {
        $users = (new Users())
            ->include((new Message()))
            ->include((new Roles()))->findAll();
        if (isset($users['exception'])){
            Application::dumper($users['exception']);
        }else{
            Application::dumper($users[0]);

        }
//        Redirect::to("/");
    }

    /**
     * @return void
     */
    function changeLang()
    {
        Session::put('lang', Input::get("lang"));
        Redirect::to("/");
    }
}