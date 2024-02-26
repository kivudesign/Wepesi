<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

use Wepesi\Core\I18n;
use Wepesi\Core\Session;

/**
 * @param string $dir
 * @return array
 */
function getSubDirectories(string $dir): array
{
    $subDir = [];
    $directories = array_filter(glob($dir), 'is_dir');
    $subDir = array_merge($subDir, $directories);
    foreach ($directories as $directory) $subDir = array_merge($subDir, getSubDirectories($directory . '/*'));
    return $subDir;
}

/**
 * @param $class
 * @return false|mixed|string
 */
function extractNamespace($class)
{
    $class_arr = explode("\\", $class);
    return end($class_arr);
}

/**
 * @param $fileName
 * @return mixed|string
 */
function checkFileExtension($fileName)
{
    $file_parts = pathinfo($fileName);
    return (isset($file_parts['extension']) && $file_parts['extension'] == 'php') ? $fileName : $fileName . '.php';
}

/**
 * @param array $exclude_folder
 * @return void
 */
function autoIndexFolder(array $exclude_folder = [])
{
    $app_root = appDirSeparator(dirname(__DIR__));

    // define folder to be excluded to not be affected by the process.
    $exclude = ['vendor', 'test'];
    if (count($exclude_folder)) $exclude = array_merge($exclude, $exclude_folder);
    $implode = implode('|', $exclude);
    $folder_struct = getSubDirectories($app_root);

    $filter = array_filter($folder_struct, function ($folder_name) use ($implode) {
        $pattern = "/$implode/i";
        if (!preg_match($pattern, strtolower(trim($folder_name)))) {
            return $folder_name;
        }
    });
    if (!checkCacheContent($filter,$app_root)) {
        foreach ($filter as $subFolder) {
            if (!is_file($subFolder . '/index.php')) {
                copy(__DIR__ . '/index.php', $subFolder . '/index.php');
            }
        }
    }
}

/**
 * check content from cache file
 * @param array $filter
 * @param string $app_root
 * @return bool
 */
function checkCacheContent(array $filter,string $app_root): bool
{
    $status = true;
    // check if cache directory exists before processing
    $cash_file_dir = appDirSeparator($app_root . '/cache');
    if (!file_exists($cash_file_dir)) {
        mkdir($cash_file_dir, 0777, true);
    }
    $cash_file_path = appDirSeparator($cash_file_dir . '/index_folder');

    if (!file_exists($cash_file_path)){
        file_put_contents($cash_file_path, var_export($filter, true));
    } else {
        $old_content = file_get_contents($cash_file_path);
        if (json_encode($old_content, true) != json_encode($filter, true)) {
            file_put_contents($cash_file_path, var_export($filter, true));
        } else {
            $status = false;
        }
    }
    return $status;
}

/**
 * @param string $path
 * @return string
 */
function appDirSeparator(string $path): string
{
    $new_path = $path;
    if ((substr(PHP_OS, 0, 3)) === 'WIN') $new_path = str_replace("\\", '/', $path);
    return $new_path;
}

/**
 * translate your text
 * @param string $message
 * @param string|array    $value
 * @return string
 */
function tra(string $message, $value = null): string
{
    $i18n = new i18n(Session::get('lang'));
    $translate_value = !is_array($value) ? [$value] : $value;
    return $i18n->translate($message, $translate_value);
}

/**
 * get a formatted application route path
 * @param string $path
 * @return string
 */
function route_path(string $path): string{
    return WEB_ROOT . ltrim($path,'/');
}