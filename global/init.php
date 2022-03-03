<?php
    session_start();
    
    require_once 'global.php';
    //

/**
 * @param $dir
 * @return array|false
 */
    function getSubDirectories($dir)    {
        $subDir = array();
        $directories = array_filter(glob($dir), 'is_dir');
        $subDir = array_merge($subDir, $directories);
        foreach ($directories as $directory) $subDir = array_merge($subDir, getSubDirectories($directory . '/*'));
        return $subDir;
    }
/**
 * @param $fileName
 * @return mixed|string
 */
    function checkFileExtension($fileName){
        $file_parts = pathinfo($fileName);
        $file = isset($file_parts['extension']) ? $fileName : $fileName . ".php";
        return $file;
    }
    require_once 'cors.php';
    //activet this module for only for api app
    $autoload= (object)$ini_array->autoload;
    if(isset($autoload->vendor) && !$autoload->vendor){
        var_dump($GLOBALS['config']);
        include 'autoload.php';
    }else{
        die("There hould be a vendor key on autoload patern to manage with autoload to be used. by default if false");
    }
