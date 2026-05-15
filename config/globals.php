<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

$GLOBALS['config'] = [
    'database' => false,
    'reminder' => [],
    'controller' => WEB_ROOT,
    'middleware' => WEB_ROOT,
    'session' => [
        'token_name' => 'token'
    ],
    'lang' => 'fr',
    'vendor' => true,
    'autoload' => ['src', 'app'],
    'helpers' => WEB_ROOT,
    'bundles' => [
       'js' => WEB_ROOT . 'assets/js',
        'css' => WEB_ROOT . 'assets/css'
    ],
];