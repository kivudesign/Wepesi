<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

if (! function_exists('getSubDirectories')) {
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
}

if (! function_exists('extractNamespace')) {
    /**
     * @param $class
     * @return mixed|string
     */
    function extractNamespace($class)
    {
        $class_arr = explode("\\", $class);
        return end($class_arr);
    }
}

if (! function_exists('checkFileExtension')) {
    /**
     * @param $fileName
     * @return string
     */
    function checkFileExtension($fileName): string
    {
        $file_parts = pathinfo($fileName);
        return (isset($file_parts['extension']) && $file_parts['extension'] == 'php') ? $fileName : $fileName . '.php';
    }
}

if (! function_exists('autoIndexFolder')) {
    /**
     * @param array $exclude_folder
     * @return void
     */
    function autoIndexFolder(array $exclude_folder = [])
    {
        $app_root = appDirSeparator(dirname(__DIR__));
        // check if cache directory exists before processing
        $cash_file_dir = appDirSeparator($app_root . '/cache');
        if (!file_exists($cash_file_dir)) {
            mkdir($cash_file_dir, 0777, true);
        }
        // define exclude folder to not be affected by the situation.
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

        if (!checkCacheContent($cash_file_dir, $filter)) {
            foreach ($filter as $subFolder) {
                if (!is_file($subFolder . '/index.php')) {
                    copy(__DIR__ . '/index.php', $subFolder . '/index.php');
                }
            }
        }
    }
}

if (! function_exists('checkCacheContent')){
    /**
     * @param string $cash_file_dir
     * @param array $filter
     * @return bool
     */
    function checkCacheContent(string $cash_file_dir, array $filter): bool
    {
        $status = true;
        $cash_file_path = appDirSeparator($cash_file_dir . '/index_folder.txt');
        sort($filter);
        $file_content = json_encode($filter, true);
        $cache_file = fOpen($cash_file_path, 'a+');
        if (!is_file($cash_file_path) || filesize($cash_file_path) < 1) {
            fwrite($cache_file, $file_content);
        } else {
            $content = fread($cache_file, filesize($cash_file_path));
            if ($content != $file_content) {
                $cache_file = fOpen($cash_file_path, 'w');
                fwrite($cache_file, $file_content);
            } else {
                $status = false;
            }
        }
        fclose($cache_file);
        return $status;
    }
}

if (! function_exists('appDirSeparator')) {
    /**
     * @param string $path
     * @return string
     */
    function appDirSeparator(string $path):string{
        $new_path = $path;
        if ((substr(PHP_OS, 0, 3)) === 'WIN') $new_path = str_replace("\\", '/', $path);
        return $new_path;
    }
}

if (! function_exists('dumper')) {
    /**
     * @param $ex
     * @return void
     */
    function dumper($ex)
    {
        print('<pre>');
        print_r($ex);
        print('</pre>');
        exit();
    }
}

if (! function_exists('url')) {
    /**
     * get a formatted application url route path
     * @param string $path
     * @return string
     */
    function url(string $path): string
    {
        return WEB_ROOT . ltrim($path, '/');
    }
}

if (! function_exists('fileExists')) {
    /**
     * Validate if the file exists, and in some case create it
     * @param string $filename Path to the file or directory. to check files on network shares.
     * @param bool $create if the file does not exist, create it
     * @return bool
     */
    function fileExists(string $filename, bool $create): bool
    {
        if (! is_file($filename) && !file_exists($filename)){
            if ($create) {
                return (bool) file_put_contents($filename, var_export('',true));
            }
            return false;
        }
        return true;
    }
}

if (! function_exists('directoryExists')) {
    /**
     * Validate if the file exists, and in some case create it
     * @param string $filename Path to the file or directory. to check files on network shares.
     * @param bool $create if the file does not exist, create it
     * @return bool
     */
    function directoryExists(string $filename, bool $create): bool
    {
        if (! file_exists($filename)){
            if ($create) {
                return (bool) mkdir($filename,0777,true);
            }
            return false;
        }
        return true;
    }
}