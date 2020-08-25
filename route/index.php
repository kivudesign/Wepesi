<?php
    $route=new Router();
    // setup get started pages index
    $route->get('/', function () {
        new View('index');
    });

    $route->run();
?>