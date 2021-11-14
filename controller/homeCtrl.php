<?php
    class homeCtrl{
        function __construct()
        {
        }
        function home(){
            new View('index');
        }
    }