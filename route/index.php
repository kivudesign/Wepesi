<?php
    $route=new Router();
    $route->get('/',function(){
        new view('exemple');
    });

    $route->get('/list',function($id){
        Controller::useController('Examples');
        $ex = new Examples();
        $result=$ex->echo();
        $view = new View('exemple');
        $view->assign("data",$result);
    });
    $route->get('/list/:id',function($id){
        Controller::useController('Examples');
        $ex = new Examples();
        $result=$ex->echo($id);
        $view = new View('exemple');
        $view->assign("data",$result);
    });
?>