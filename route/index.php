<?php
    $route=new Router();
    $route->get('/',function(){
        new view('exemple');
    });

    $route->get('/list',function(){
        Controller::useController('Examples');
        $ex = new Examples();
        $result=$ex->echo();
        $view = new View('exemple');
        $view->assign("data",$result);
    });
    $route->get("/list/:id", "Examples#echo");

    $route->post('/list',function(){
        Controller::useController('Examples');
        $ex = new Examples();
        $ex->addList($_REQUEST);
    });
    $route->get('/register',function(){
        new View('register');
    });
    $route->post('/register',"Example#register");

    $route->run();
?>