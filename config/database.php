<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

use Wepesi\Core\DotEnv;

(new DotEnv(dirname(__DIR__) . '/.env'))->load();

$GLOBALS['config'] ['mysql'] =
    [
        'host' => getenv('DB_HOST'),
        'db' => getenv('DB_NAME'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
        'port' => getenv('DB_PORT'),
        'usable' => true
    ];