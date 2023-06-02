<?php
/*
 * Copyright (c) 2023. Wepesi.
 */
$db_conf = $load_init_config['db_conf'];

$GLOBALS['config'] ['mysql'] =
    [
        'host' => $db_conf['host'],
        'db' => $db_conf['database'],
        'username' => $db_conf['user'],
        'password' => $db_conf['password'],
        'port' => $db_conf['port']
    ];