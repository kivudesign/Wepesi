<?php

use Wepesi\Controller\homeController;
use Wepesi\Core\Routing\Router;
use Wepesi\Core\View;

    $route=new Router();
    // setup get started pages index
    $route->get('/', function () {
        (new View)->display('/home');
    });
    $route->get('/home', "homeController#home");
    //
    $route->post("/change-lang",[homeController::class,"changeLang"])->middleware([\wepesi\Validation\HomeValidation::class, "changeLang"]);
    $route->run();