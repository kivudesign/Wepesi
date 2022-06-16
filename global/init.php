<?php
/**
 *
 */
    session_start();

    include "config/load_init_config.php";
    include "constant.php";
    include "config/globals.php";
    include "helper/functions.php";
/**
 *
 */

$vendor = isset($GLOBALS["config"]) ? (isset($GLOBALS["config"]["vendor"]) ?$GLOBALS["config"]["vendor"]: false) : false;

if( !$vendor ){
    include "autoload.php";
}else{
    include "vendor/autoload.php";
}
