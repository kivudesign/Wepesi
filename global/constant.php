<?php
    /**
     * allow configuration are store in this file
     * for database and the connection
     * they declare as global tho to be accessible from anywhere in the project
     */

    // include language file according to your configuration

    //web root configaration

    define('WEB_ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
    define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));

    // default timezone
    const TIMEZONE = 'Africa/Kigali';

    //define default domain
    $server_name=$_SERVER['SERVER_NAME']??"wepesi.com";
    $protocol=$_SERVER['REQUEST_SCHEME']??"http";

    define("DEFAULT_DOMAIN","$protocol://$server_name");
    define("APP_DOMAIN",$server_name);