<?php
/**
 * Support for DataBase configuration resource
 * Authentication information store on global variable
 */
    $db_conf= (object)$ini_array->db_conf;
    // database configuration setup
    $GLOBALS['config']=array(
        'mysql'=>array(
            'host'=> $db_conf->host,
            'username'=> $db_conf->user,
            'password'=> $db_conf->password,
            'db'=> $db_conf->database
        ),
        'remender'=>array(),
        'session'=>array(
            "token_name"=>"token"
        )
    );