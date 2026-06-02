<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

session_start();


/**
 * Project General built-in Functions
 */
require_once $ROOT_DIR . '/config/function.php';

/**
 * Project autoload register.
 *
 * Composer autoload is preferred when available.
 * Built-in autoload is used as a fallback for non-composer installations.
 */
$composerAutoload = $ROOT_DIR . '/vendor/autoload.php';

if (is_file($composerAutoload)) {
    require_once $composerAutoload;
}

require_once $ROOT_DIR . '/config/autoload.php';

/*
 *
 */
require_once $ROOT_DIR . '/config/constant.php';

/**
 * Application Global Configuration
 */
require_once $ROOT_DIR . '/config/globals.php';

/**
 * Init default lang
 */
if (class_exists(\Wepesi\Core\Session::class)) {
    if (\Wepesi\Core\Session::get('lang') == '') {
        \Wepesi\Core\Session::put('lang', $GLOBALS['config']['lang']);
    }
}
