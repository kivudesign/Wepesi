<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

session_start();

/**
 * Get App domain
 * define default domain
 */
/**
 * Get host domain ip address
 * @return string
 */
function getDomainIP(): string
{
    $ip = $_SERVER['REMOTE_ADDR'];

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif ($ip == '::1') {
        $ip = gethostbyname(getHostName());
    }
    return $ip;
}

/**
 * Get server information's
 * @return object
 */
function serverDomain(): object
{
    $server_name = $_SERVER['SERVER_NAME'];
    $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? strtolower(explode('/', $_SERVER['SERVER_PROTOCOL'])[0]) : 'http';
    $domain = getDomainIp() === '127.0.0.1' ? "$protocol://$server_name" : $server_name;
    return (object)[
        'server_name' => $server_name,
        'protocol' => $protocol,
        'domain' => $domain
    ];
}

/*
 *
 */
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
//require_once $ROOT_DIR . '/vendor/autoload.php';
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
