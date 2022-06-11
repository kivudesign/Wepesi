<?php
/**
 * Wepesi Home Controller
 *
 */
namespace Wepesi\Controller;
use Wepesi\Core\Input;
use Wepesi\Core\Redirect;
use Wepesi\Core\Session;

class homeController{
        function __construct()
        {
        }

        function home(){
            Redirect::to(WEB_ROOT);
        }
        function changeLang(){
            Session::put('lang', Input::get("lang"));
            Redirect::to(WEB_ROOT);
        }
    }