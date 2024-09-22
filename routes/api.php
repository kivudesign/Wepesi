<?php

global $app;

use Wepesi\Core\Http\Response;

$router = $app->router();
$router->get('/', function () {
    Response::send('Welcome to Wepesi API');
});