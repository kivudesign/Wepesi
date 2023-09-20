<?php

use Wepesi\Controller\exampleController;
use Wepesi\Core\View;
use Wepesi\Middleware\Validation\exampleValidation;

$router = $app->router();
// setup get started pages index
$router->get('/', function () {
    (new View)->display('/home');
});
$router->get('/home', '\Wepesi\Controller\exampleController#home');
//
$router->post('/changelang', [exampleController::class, 'changeLang'])
    ->middleware([exampleValidation::class, 'changeLang']);

include \Wepesi\Core\Application::$ROOT_DIR . './router/api.php';