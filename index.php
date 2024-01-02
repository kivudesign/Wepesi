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

(new DotEnv($ROOT_DIR . '/.env'))->load();

$appConfiguration = new AppConfiguration();

$configuration = $appConfiguration
    ->lang(getenv('LANG'))
    ->timezone(getenv('TIME_ZONE'));

(new \Wepesi\Core\Orm\DBConfig())
    ->host($_ENV['DB_HOST'])
    ->port($_ENV['DB_PORT'])
    ->db($_ENV['DB_NAME'])
    ->username($_ENV['DB_USER'])
    ->password($_ENV['DB_PASSWORD']);

$app = new Application($ROOT_DIR, $configuration);

require_once $app::$ROOT_DIR . '/router/route.php';

$app->run();
