<?php
$router->api('/',function() use($router){
    $router->get('/home',function(){
        \Wepesi\Core\Response::send(['message' => 'Welcom to api routing']);
    });
});