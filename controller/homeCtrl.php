<?php

    class homeCtrl{
        function __construct()
        {
            $this->h= new Home();
        }
        function home(){
            $v=new View('index');
            $v->assign("result",$this->h->welcom());
        }        
    }