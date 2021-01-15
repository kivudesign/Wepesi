<?php
    $route=new Router();
    // setup get started pages index
    $route->get('/', function () {
        new View('index');
    });
    $route->get('/home', "homeCtrl#home");

    $route->run();
?>