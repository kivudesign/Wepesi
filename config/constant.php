<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

/**
 * allow configuration are store in this file
 * for database and the connection
 * they declare as global tho to be accessible from anywhere in the project
 */

/**
 * web app file path
 */
define('WEB_ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
/**
 * os system absolute file path
 */
define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));

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

/**
 * Define default domain
 */
define('DEFAULT_DOMAIN', serverDomain()->protocol . "://" . serverDomain()->server_name);
/**
 * Define Application host domain
 */
define('APP_DOMAIN', serverDomain()->domain);
