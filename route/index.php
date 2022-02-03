<?php

use Wepesi\Core\Routing\Router;
use Wepesi\Core\View;

$route=new Router();
    // setup get started pages index
    $route->get('/', function () {
        new View('index');
    });
    $route->get('/home', "homeCtrl#home");
    $route->get('/contact', [homeCtrl::class,"contact"]);

    $route->run();
?>