<?php
/*
 * Copyright (c) 2023-2026. Wepesi Dev Framework
 */

/**
 * Wepesi Home Controller
 *
 */

namespace App\controller;

use Wepesi\Core\Http\Controller;
use Wepesi\Core\Http\Input;
use Wepesi\Core\Http\Redirect;
use Wepesi\Core\Session;

class exampleController extends Controller
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
        Session::put('lang', Input::post("lang"));
        Redirect::to("/");
    }
}