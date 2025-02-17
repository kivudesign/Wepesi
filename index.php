<?php
/*
 * @package Wepesi\framework
 * Copyright (c) 2023. wepesi dev framework
 */

use Wepesi\Core\AppConfiguration;
use Wepesi\Core\Application;
use Wepesi\Core\DotEnv;

// Define root directory
$ROOT_DIR = __DIR__;

if ((substr(PHP_OS, 0, 3)) === 'WIN') $ROOT_DIR = str_replace("\\", '/', $ROOT_DIR);

require_once $ROOT_DIR . '/config/init.php';

(new DotEnv($ROOT_DIR . '/.env'))->load();

/**
 *  Generate and index a file for redirection (protection) while APP_DEV in production
 */
if (getenv('APP_ENV') === 'prod') {
    autoIndexFolder(['assets']);
}

$appConfiguration = new AppConfiguration();

$configuration = $appConfiguration
    ->lang(getenv('LANG'))
    ->timezone(getenv('TIME_ZONE'));

$app = new Application($ROOT_DIR, $configuration);

$app->run();
