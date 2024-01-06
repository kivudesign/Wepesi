<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

/**
 * Wepesi Home Controller
 *
 */

namespace Wepesi\Controller;


use Wepesi\Core\Controller;
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
     * @return void
     */
    function home()
    {
        $this->view->display('home');
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