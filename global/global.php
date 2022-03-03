<?php
    /**
     * allow configuration are store in this file
     * for database and the connection
     * they declare as global tho to be accessible from anywhere in the project
     */
    const LANG = "fr";
    include("./lang/".LANG."/language.php");
    // load configguration
    $ini_array =(object) parse_ini_file("./config/config.ini", true);

    //include database Globale configuration
    include ("db.php");
    // inlude language file according to your configuraiton
    define("LANG_VALIDATE", $validation);

    //web root configaration

    define('WEB_ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
    define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));

    // default timezone
    define('TIMEZONE','Africa/Kigali');

    //define default domain
    $server_name=$_SERVER['SERVER_NAME'];
    $protocol=$_SERVER['REQUEST_SCHEME'];

    define("DEFAULT_DOMAIN","$protocol://$server_name");
    define("APP_DOMAIN",$server_name);