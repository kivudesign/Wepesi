<?php

use Wepesi\Core\View;

class homeCtrl{
        function __construct()
        {
        }
        function home(){
            new View('index');
        }
        function contact(){
            new View('contact');
        }
    }