<?php
    /**
     * allow configuration are store in this file
     * for database and the connection
     * they declare as global tho to be accessible from anywhere in the project
     */
    const LANG = "fr";
    include("./lang/".LANG."/language.php");



    // include language file according to your configuration
    define("LANG_VALIDATE", $validation);

    //web root configaration

    define('WEB_ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
    define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));

    // default timezone
    const TIMEZONE = 'Africa/Kigali';

    //define default domain
    $server_name=$_SERVER['SERVER_NAME'];
    $protocol=$_SERVER['REQUEST_SCHEME'];

    define("DEFAULT_DOMAIN","$protocol://$server_name");
    define("APP_DOMAIN",$server_name);