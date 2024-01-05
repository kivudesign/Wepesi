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
 * Define default domain
 */
define('DEFAULT_DOMAIN', serverDomain()->protocol . "://" . serverDomain()->server_name);
/**
 * Define Application host domain
 */
define('APP_DOMAIN', serverDomain()->domain);
