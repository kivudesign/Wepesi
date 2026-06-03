<?php

namespace Wepesi\Core\Component;

use Wepesi\Core\Application;
use Wepesi\Core\Component\Providers\Contracts\ComponentContracts;

/**
 * 
 * @package Wepesi\Core\Component
 * @template T
 * 
 */
final class useComponent
{
    protected static array $components = [];
    protected static bool $loaded = false;

    /**
     * Load component registration config file
     * 
     * @param string $configFile 
     * @return void
     */
    public static function loadConfig(string $configFile): void
    {
        if (file_exists($configFile)) {
            // TO DO  review the loading application configuration 
            // better rewriter the Config application class to load from multiple array files
            $new = require $configFile;
            if (is_array($new)) {
                self::$components = array_merge(self::$components, $new);
            }
        }
        self::$loaded = true;
    }

    /**
     * Maps component method name to component class
     * 
     * @param string $name component reference name
     * @param array $arguments 
     * @return string
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (!self::$loaded) {
            // default : look for config/components/php to this file
            self::loadConfig(Application::getRootDir() . '/config/components.php');
        }
        if (!isset(self::$components[$name])) {
            throw new \Exception("Component '$name' not registered or not implemented.");
        }
        $class = self::$components[$name];

        if (!empty($arguments)) {
            $data = $arguments[0] ?? [];
            return (new $class())->render($data);
        }

        return new $class();
    }

    /**
     * register component From Directory
     *
     * @param  mixed $directory
     * @param  mixed $namespace
     *
     * @link https://github.com/kivudesign/Wepesi/tree/master/app/Components
     * @return void
     */
    public static function registerFromDirectory(string $directory, string $namespace): void
    {
        $files = glob(rtrim($directory, "/") . "/*.php");
        foreach ($files as $file) {
            $class = $namespace . "\\" . basename($file, '.php');
            if (class_exists($class) && is_a($class, ComponentContracts::class, true)) {
                $name = lcfirst(basename($file, '.php'));
                self::$components[$name] = $class;
            }
        }
        self::$loaded = count($files) > 0;
    }

    /**
     * Manually register a one-off component
     *
     * @param  mixed $name
     * @param  mixed $className
     * @return void
     */
    public static function register(string $name, string $className): void
    {
        self::$components[$name] = $className;
        self::$loaded = true;
    }
}
