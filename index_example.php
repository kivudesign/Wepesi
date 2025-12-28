<?php
/*
 * @package Wepesi\framework
 * Copyright (c) 2023-2024. wepesi dev framework
 */

use Wepesi\Core\AppConfiguration;
use Wepesi\Core\Application;
use Wepesi\Core\DotEnv;
use Wepesi\ErrorHandler;

// Define root directory
$ROOT_DIR = __DIR__;

if ((substr(PHP_OS, 0, 3)) === 'WIN') $ROOT_DIR = str_replace("\\", '/', $ROOT_DIR);

require_once $ROOT_DIR . '/config/init.php';

(new DotEnv($ROOT_DIR . '/.env'))->load();

/**
 * Register Error Handler (NEW in v0.1)
 * This must be done early, before any application code runs
 */
$errorConfig = require $ROOT_DIR . '/config/error.php';
ErrorHandler::register($errorConfig);

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

/**
 * Optional: Set user context after authentication
 * This should be done after user login, typically in your authentication logic
 */
// Example:
// if ($user = getCurrentUser()) {
//     ErrorHandler::setUser([
//         'id' => $user->id,
//         'email' => $user->email,
//         'username' => $user->username
//     ]);
// }

$app->run();
