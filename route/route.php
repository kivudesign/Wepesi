<?php

use Wepesi\Controller\homeController;
use Wepesi\Core\View;
use Wepesi\Middleware\Validation\HomeValidation;

// setup get started pages index
$router->get('/', function () {
    (new View)->display('/home');
});
$router->get('/home', 'homeController#home');
//
$router->post('/change-lang', [homeController::class, 'changeLang'])
    ->middleware([HomeValidation::class, 'changeLang']);