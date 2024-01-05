<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core;

use Wepesi\Core\Routing\Router;

/**
 * Application root
 */
class Application
{
    /**
     * define application global configuration
     * @var string
     */
    public static string $ROOT_DIR;
    /**
     * @var string
     */
    public static string $APP_DOMAIN;
    /**
     * @var string|mixed
     */
    public static string $APP_LANG;
    /**
     * @var string|mixed|null
     */
    public static ?string $APP_TEMPLATE;
    /**
     * @var string
     */
    public static string $LAYOUT_CONTENT;
    /**
     * @var string|null
     */
    public static ?string $LAYOUT = null;
    /**
     * @var array
     */
    private static array $params = [];
    /**
     * @var Router
     */
    private Router $router;

    /**
     * Application constructor.
     * @param string $path path root directory of the application
     * @param AppConfiguration $config
     */
    public function __construct(string $path, AppConfiguration $config)
    {

        self::$ROOT_DIR = str_replace("\\", '/', $path);
        self::$APP_DOMAIN = serverDomain()->domain;
        self::$params = $config->generate();
        self::$APP_TEMPLATE = self::$params['app_template'] ?? null;
        self::$APP_LANG = self::$params['lang'] ?? 'fr';
        $this->router = new Router();
        self::$LAYOUT_CONTENT = 'layout_content';
    }

    /**
     * simple builtin dumper for dump data
     * @param $ex
     * @return void
     */
    public static function dumper($ex)
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
        self::$LAYOUT = self::$ROOT_DIR . '/views/' . $layout;
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
