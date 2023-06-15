<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

use Wepesi\Core\AppConfiguration;
use Wepesi\Core\Application;
use Wepesi\Core\DotEnv;

// Define root directory
$ROOT_DIR = __DIR__;

if ((substr(PHP_OS, 0, 3)) === 'WIN') $ROOT_DIR = str_replace("\\", '/', $ROOT_DIR);

require_once $ROOT_DIR . '/config/init.php';

(new DotEnv($ROOT_DIR.'/.env'))->load();

$appConfiguration = new AppConfiguration();

$configuration = $appConfiguration
    ->lang(getenv('LANG'))
    ->timezone(getenv('TIME_ZONE'));

$app = new Application($ROOT_DIR, $configuration);

$router = $app->router();

require_once Application::$ROOT_DIR . '/route/route.php';

$router->run();
