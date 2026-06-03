<?php
/*
 * @package Wepesi\framework
 * Copyright (c) 2023. wepesi dev framework
 */

use Wepesi\Core\AppConfiguration;
use Wepesi\Core\Application;
use Wepesi\Core\Config;
use Wepesi\Core\DI\Container;
use Wepesi\Core\DotEnv;

// Define root directory
$ROOT_DIR = str_replace('\\', '/', __DIR__);

require_once $ROOT_DIR . '/config/bootstrap.php';

$envFile = $ROOT_DIR . '/.env';

if (is_file($envFile)) {
    (new DotEnv($envFile))->load();
}

$appConfig = new Config();
$appConfig->load($ROOT_DIR . '/config/globals.php');
$appConfig->load($ROOT_DIR . '/config/database.php');

/**
 * Generate index files for directory protection in production.
 */
if (getenv('APP_ENV') === 'prod') {
    autoIndexFolder(['assets']);
}

$container = new Container();
$container->instance(Config::class, $appConfig);

$configuration = (new AppConfiguration())
    ->view()
    ->route()
    ->lang(getenv('LANG') ?: Config::get('lang', 'fr'))
    ->timezone(getenv('TIME_ZONE') ?: 'Africa/Kigali');

$app = new Application($ROOT_DIR, $configuration, $container);

$app->run();
