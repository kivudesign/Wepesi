<?php

/**
 * Scan a folder an get all directory inside
 * @param $dir
 * @return array
 */
function getSubDirectories($dir): array
{
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
    return isset($file_parts['extension']) ? $fileName : $fileName . ".php";
}