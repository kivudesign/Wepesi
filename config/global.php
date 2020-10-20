<?php
// database configuration setup
    define("HOST", "localhost");
    define("DATABASE", "root");
    define("USER", "root");
    define("PASSWORD", "");

//web root configaration
    define('WEB_ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
    define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));