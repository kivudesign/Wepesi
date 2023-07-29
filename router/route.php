<?php

$router = $app->router();
// setup get started pages index
$router->get('/', function () {
    (new \Wepesi\Core\View)->display('/home');
});
$router->get('/home', '\Wepesi\Controller\homeController#home');
//
$router->post('/changelang', [\Wepesi\Controller\homeController::class, 'changeLang'])
    ->middleware([\Wepesi\Middleware\Validation\HomeValidation::class, 'changeLang']);

include \Wepesi\Core\Application::$ROOT_DIR . './router/api.php';