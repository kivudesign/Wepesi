<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */
global $app;

use Wepesi\Controller\exampleController;
use Wepesi\Core\Http\Response;
use Wepesi\Core\Views\View;
use Wepesi\Middleware\Validation\exampleValidation;

$router = $app->router();

// setup get started pages index
$router->get('/', function () {
    (new View)->display('/home');
});

$router->get('/helloworld', function () {
    (new View)->renderHTML('<h1>Hello World!</h1>');
});

$router->get('/home', [exampleController::class,'home']);
//
$router->post('/changelang', [exampleController::class, 'changeLang'])
    ->middleware([exampleValidation::class, 'changeLang']);

$router->set404(function(){
    Response::send('route not defined', 404);
});
