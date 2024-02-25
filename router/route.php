<?php

use Wepesi\Controller\indexController;
use Wepesi\Core\View;
use Wepesi\Middleware\Validation\exampleValidation;

$router = $app->router();
// setup get started pages index
$router->get('/', function () {
    (new View)->display('/home');
});
$router->get('/home', [\Wepesi\Controller\indexController::class,'home']);
//
$router->post('/changelang', [indexController::class, 'changeLang'])
    ->middleware([exampleValidation::class, 'changeLang']);

include \Wepesi\Core\Application::$ROOT_DIR . './router/api.php';