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
    public static string $LAYOUT_CONTENT;
    /**
     * @var string|null
     */
    public static string $LAYOUT;

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
        self::$LAYOUT_CONTENT = 'layout_content';
        self::$LAYOUT = '';
        self::$VIEW_FOLDER = '';
        $this->router = new Router();
    }

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
     * Set the layout at the top of your application to be available everywhere.
     * @param string $layout
     * @return void
     */
    public static function setLayout(string $layout)
    {
        self::$LAYOUT = self::getRootDir().'/views/'.$layout;
    }
    public static function setLayoutContent(string $layout_name)
    {
        self::$LAYOUT_CONTENT = $layout_name;
    }

    public static function setViewFolder(string $folder_name)
    {
        self::$VIEW_FOLDER = $folder_name;
    }
    public static function getLayout()
    {
        return self::$LAYOUT ;
    }
    public static function getLayoutContent()
    {
        return self::$LAYOUT_CONTENT ;
    }

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