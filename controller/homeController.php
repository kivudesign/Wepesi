<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

/**
 * Wepesi Home Controller
 *
 */
namespace Wepesi\Controller;
use http\Env\Response;
use Wepesi\Core\Input;
use Wepesi\Core\Redirect;
use Wepesi\Core\Session;
use Wepesi\Models\Message;
use Wepesi\Models\Roles;
use Wepesi\Models\Users;


class homeController{

    /**
     * @return void
     */
        function home(){
            Redirect::to("/");
        }

    /**
     * @return void
     */
        function changeLang(){
            Session::put('lang', Input::get("lang"));
            Redirect::to("/");
        }
    }