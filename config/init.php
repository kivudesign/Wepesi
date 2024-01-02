<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

session_start();

require_once $ROOT_DIR . '/config/constant.php';
/**
 * Application Global Configuration
 */
require_once $ROOT_DIR . '/config/globals.php';

/**
 * Project General built-in Functions
 */
require_once $ROOT_DIR . '/config/function.php';

/**
 *  Project autoload register
 */
require_once $ROOT_DIR . '/config/autoload.php';
require_once $ROOT_DIR . '/vendor/autoload.php';
/**
 * Project Utils
 */

require_once $ROOT_DIR . '/config/database.php';

/**
 * Init default lang
 */
if (class_exists(\Wepesi\Core\Session::class)) {
    if (\Wepesi\Core\Session::get('lang') == '') {
        \Wepesi\Core\Session::put('lang', $GLOBALS['config']['lang']);
    }
}
