<?php
    /**
     * allow configuration are store in this file
     * for database and the connection
     * they declare as global tho to be accessible from anywhere in the project
     */
    const LANG = "fr";
    include("./lang/" . checkFileExtension(LANG));
    // load configguration
    $ini_array =(object) parse_ini_file("./core/config.ini", true);
    $db_conf= (object)$ini_array->db_conf;

    // database configuration setup
    define("HOST", $db_conf->host);
    define("DATABASE", $db_conf->database);
    define("USER", $db_conf->user);
    define("PASSWORD", $db_conf->password);

    // inlude language file according to your configuraiton
    define("LANG_VALIDATE", $validation);
    define("LANG_BOX_MESSAGE", $boxMessage);

    //web root configaration

    define('WEB_ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
    define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));

    // default timezone
    define('TIMEZONE','Africa/Kigali');

    //define default domain
    define("DEFAULT_DOMAIN","http://doctawetu");
//    define("DEFAULT_DOMAIN","https://doctawetu.com");
    define("APP_DOMAIN","doctawetu.com");