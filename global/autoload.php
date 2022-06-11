<?php
/**
 * This is a build in autoload witch does not required to dump composer each time
 * new class is added on the src class folder.
 * it speed and support event namespaces.
 * but it not support for external module install via composer.
 */
    $config = $GLOBALS["config"];
    $autoload =["src"];
    if(isset($config["autoload"])){
        $autoload=is_string($config['autoload'])?[$config['autoload']]:$config['autoload'];
    }

    spl_autoload_register(function($class) use ($autoload) {
        foreach ($autoload as $src){
            $dirs = getSubDirectories($src);
            $class_arr = explode("\\",$class);
            $len = count($class_arr);
            $classFile = $class_arr[($len-1)];
            foreach($dirs as $dir){
                $file = $dir."/". checkFileExtension($classFile);
                if ( is_file($file) ) {
                    require_once($file);
                }
            }
        }
    });