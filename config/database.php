<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

return [
    'mysql' => [
        'host' => getenv('DB_HOST'),
        'db' => getenv('DB_NAME'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
        'port' => getenv('DB_PORT') ?: 3306,
    ]
];