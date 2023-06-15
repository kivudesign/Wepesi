<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

/**
 * allow configuration are store in this file
 * for database and the connection
 * they declare as global tho to be accessible from anywhere in the project
 */

//web root configuration
define('WEB_ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));

/**
 * define default domain
 */
$server_name = $_SERVER['SERVER_NAME'] ?? 'wepesi.com';
$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? strtolower(explode('/', $_SERVER['SERVER_PROTOCOL'])[0]) : 'http';
$domain = $_SERVER['REMOTE_ADDR'] == '::1' ? "$protocol://$server_name" : $server_name;
define('DEFAULT_DOMAIN', "$protocol://$server_name");
define('APP_DOMAIN', $domain);

// default timezone
const TIMEZONE = 'Africa/Kigali';

// define in witch cycle are you are working on.
// in case you are in dev, indexing file will not be generated, but in prod fase, it will be generated
define('APP_DEV',true);