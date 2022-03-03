<?php
/**
 * This is a build in autoload witch does not required to dump composer each time
 * new class is added on the src class folder.
 * it speed and support event namespaces.
 * but it not support for external module install via composer.
 */
    spl_autoload_register(function($class){
        $dirs = getSubDirectories("src");
        $class_arr=explode("\\",$class);
        $len=count($class_arr);
        $classFile=$class_arr[($len-1)];
        foreach($dirs as $dir){
            $file=$dir."/". checkFileExtension($classFile);
            if (is_file($file)) { // check if the file exist
                require_once($file); // incluse the file request if it exist
            }
        }
    });