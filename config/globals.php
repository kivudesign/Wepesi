<?php
/**
 * Support for DataBase configuration resource
 * Authentication information store on global variable
 */
    $db_conf= $load_init_config["db_conf"];
    // database configuration setup
    $GLOBALS['config']=[
        'mysql'=>[
            'host'=> $db_conf["host"],
            'username'=> $db_conf["user"],
            'password'=> $db_conf["password"],
            'db'=> $db_conf["database"]
        ],
        'remender'=>[],
        'session'=>[
            "token_name"=>"token"
        ],
        'controller'=>WEB_ROOT,
        'middleware'=>WEB_ROOT,
        'vendor'=>false,
        'lang' => 'en',
        'autoload'=>["src","controller","middleware"]
    ];