<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

namespace Wepesi\Core;

class Config
{
    /**
     * @var array<string, mixed>
     */
    private static array $items = [];

    /**
     * Create a config instance and optionally preload values from legacy globals.
     */
    public function __construct()
    {
        if (!empty($GLOBALS['config']) && is_array($GLOBALS['config'])) {
            self::$items = array_replace_recursive(self::$items, $GLOBALS['config']);
        }
    }

    /**
     * Load a config file that returns an array.
     *
     * @param string $file
     * @return void
     */
    public function load(string $file): void
    {
        if (!is_file($file)) {
            return;
        }

        $config = require $file;

        if (!is_array($config)) {
            return;
        }

        self::merge($config);
    }

    /**
     * Load all config files from a directory.
     *
     * @param string $directory
     * @param array<int, string> $except
     * @return void
     */
    public function loadDirectory(string $directory, array $except = []): void
    {
        if (!is_dir($directory)) {
            return;
        }

        foreach (glob(rtrim($directory, '/') . '/*.php') ?: [] as $file) {
            $name = basename($file);

            if (in_array($name, $except, true)) {
                continue;
            }

            $this->load($file);
        }
    }

    /**
     * Merge config values.
     *
     * @param array<string, mixed> $items
     * @return void
     */
    public static function merge(array $items): void
    {
        self::$items = array_replace_recursive(self::$items, $items);
        $GLOBALS['config'] = self::$items;
    }

    /**
     * Set a config value using slash notation.
     *
     * @param string $path
     * @param mixed $value
     * @return void
     */
    public static function set(string $path, mixed $value): void
    {
        $segments = explode('/', trim($path, '/'));
        $config = &self::$items;

        foreach ($segments as $segment) {
            if (!isset($config[$segment]) || !is_array($config[$segment])) {
                $config[$segment] = [];
            }

            $config = &$config[$segment];
        }

        $config = $value;
        $GLOBALS['config'] = self::$items;
    }

    /**
     * Get a config value using slash notation.
     *
     * @param string|null $path
     * @param mixed $default
     * @return mixed
     */
    public static function get(?string $path = null, mixed $default = false): mixed
    {
        if ($path === null || trim($path) === '') {
            return self::$items ?: ($GLOBALS['config'] ?? $default);
        }

        $config = self::$items ?: ($GLOBALS['config'] ?? []);

        foreach (explode('/', trim($path, '/')) as $bit) {
            if (!is_array($config) || !array_key_exists($bit, $config)) {
                return $default;
            }

            $config = $config[$bit];
        }

        return $config;
    }

    /**
     * Determine whether a config path exists.
     *
     * @param string $path
     * @return bool
     */
    public static function has(string $path): bool
    {
        return self::get($path, null) !== null;
    }

    /**
     * Return all config values.
     *
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return self::$items ?: ($GLOBALS['config'] ?? []);
    }
}
