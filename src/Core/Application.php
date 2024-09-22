<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core;

use Wepesi\Core\Routing\Router;

/**
 *
 */
class Application
{
    /**
     * @var array
     */
    private static array $config_params = [];
    /**
     * define application global configuration
     * @var string
     */
    private static string $root_dir;
    /**
     * @var string
     */
    public static string $APP_DOMAIN;
    /**
     * @var string
     */
    public static string $APP_LANG;
    /**
     * @var string
     */
    public static string $APP_TEMPLATE;
    /**
     * @var string
     */
    public static string $layout_content_param;
    /**
     * @var string
     */
    private static string $layout;

    /**
     * @var string
     */
    private static string $VIEW_FOLDER;
    /**
     * @var Router
     */
    private Router $router;

    /**
     * Application constructor.
     * @param string $path root path directory of the application
     * @param AppConfiguration $config
     */
    public function __construct(string $path, AppConfiguration $config)
    {
        self::$config_params = $config->generate();
        self::$root_dir = str_replace("\\", '/', $path);
        self::$APP_DOMAIN = serverDomain()->domain;
        self::$APP_LANG = self::$config_params['lang'] ?? 'fr';
        self::$APP_TEMPLATE = self::$config_params['app_template'] ?? '';
        self::$layout_content_param = 'layout_content';
        self::$layout = '';
        self::$VIEW_FOLDER = '';
        $this->router = new Router();
    }

    /**
     * @return string
     */
    public static function getRootDir(): string
    {
        return self::$root_dir;
    }

    /**
     * simple builtin dumper for dump data
     * @param $ex
     *
     */
    public static function dumper($ex): void
    {
        print('<pre>');
        var_dump($ex);
        print('</pre>');
        exit();
    }

    /**
     * Define a layout to be used by all pages in the application.
     * can be set at the top of your application to be available everywhere.
     * @param string $layout
     * @return void
     */
    public static function setLayout(string $layout)
    {
        self::$layout = self::getRootDir() . '/views/' . trim($layout, '/');
    }

    /**
     * @param string $layout_name
     * @return void
     */
    public static function setLayoutcontentparam(string $layout_name)
    {
        self::$layout_content_param = $layout_name;
    }

    /**
     * @param string $folder_name
     * @return void
     */
    public static function setViewFolder(string $folder_name)
    {
        self::$VIEW_FOLDER = $folder_name;
    }

    /**
     * @return string|null
     */
    public static function getLayout(): ?string
    {
        return strlen(trim(self::$layout )) > 0 ? self::$layout : null;
    }

    /**
     * @return string
     */
    public static function getLayoutContentParam(): string
    {
        return self::$layout_content_param ;
    }

    /**
     * @return string
     */
    public static function getViewFolder()
    {
        return self::$VIEW_FOLDER ;
    }

    /**
     * @return Router
     */
    public function router(): Router
    {
        return $this->router;
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->router->run();
    }
}