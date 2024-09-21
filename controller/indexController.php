<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

/**
 * Wepesi Home Controller
 *
 */

namespace Wepesi\Controller;


use Wepesi\Core\Http\Controller;
use Wepesi\Core\Http\Input;
use Wepesi\Core\Http\Redirect;
use Wepesi\Core\Session;

class indexController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    function home(): void
    {
        $this->view->display('home');
    }

    /**
     *
     */
    function changeLang(): void
    {
        Session::put('lang', Input::get("lang"));
        Redirect::to("/");
    }
}