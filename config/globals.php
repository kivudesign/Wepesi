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
    'autoload' => ['src', 'controller', 'middleware', 'models', 'helpers'],
    'helper' => WEB_ROOT,
    'preferences' => WEB_ROOT. 'helper'
];