<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

/**
 * This is a builtin autoload witch does not require dumping composer each time
 * a new class is added on the src class folder.
 * it's lightweight and supports namespaces.
 * and can be adapted for external module installed via composer.
 */
$config = $GLOBALS['config'];
$autoload = ['src'];
if (isset($config['autoload'])) {
    $autoload = is_string($config['autoload']) ? [$config['autoload']] : $config['autoload'];
}

// builtin autoload
spl_autoload_register(/**
 * @param $class
 * @return void
 */ function ($class) use ($autoload) {
    $app_root = appDirSeparator(dirname(__DIR__));
    foreach ($autoload as $src) {
        $folder = $app_root . '/' . $src;
        $directories = getSubDirectories($folder);
        $classFile = extractNamespace($class);
        foreach ($directories as $dir) {
            $file = $dir . '/' . checkFileExtension($classFile);
            if (is_file($file)) {
                require_once "$file";
            }
        }
    }
}, true);