<?php
// Define root directory

$ROOT_DIR = __DIR__;

if ((substr(PHP_OS, 0, 3)) === 'WIN') $ROOT_DIR = str_replace("\\", '/', $ROOT_DIR);

require_once __DIR__.'/config/init.php';
    require_once __DIR__.'/route/router.php';