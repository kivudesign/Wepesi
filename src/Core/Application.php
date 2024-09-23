<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core;

use Wepesi\Core\Database\DBConfig;
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
     * @return string
     */
    public static function getLayout(): string
    {
        return trim(self::$layout );
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
     * @throws \Exception
     */
    protected function routeProvider(): void
    {
        $base_route_path = self::getRootDir() . '/routes';
        $api_route_path = $base_route_path . '/api.php';
        if (file_exists($api_route_path)) {
            $this->router->group([
                'pattern' => '/api'
            ], function (Router $router) {
                if (isset($_SERVER['HTTP_ORIGIN'])) {
                    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
                    // you want to allow, and if so:
                    header('Access-Control-Allow-Origin: *');
                    header('Access-Control-Allow-Credentials: true');
                    header('Access-Control-Max-Age: 86400');    // cache for 1 day
                }
                header('Access-Control-Allow-Methods: GET, POST,PUT, PATCH, HEAD, OPTIONS');
                // Access-Control headers are received during OPTIONS requests
                if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                    // may also be using PUT, PATCH, HEAD etc.
                    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

                    exit(0);
                }
                $router->group([], $this->registerRoute('/api.php'));
            });
        }
        $web_route_path = $base_route_path . '/web.php';
        if (file_exists($web_route_path)) {
            $this->router->group([], $this->registerRoute('/web.php'));
        }
        if (!file_exists($web_route_path) && !file_exists($api_route_path)) {
            throw new \Exception('No Route file not found.');
        }
    }

    /**
     * route path
     * @param string $path
     * @return string
     */
    public function registerRoute(string $path): string
    {
        return $this->basePath('/routes' . '/' . trim($path,'/'));
    }
    /**
     * @param string $path
     * @return string
     */
    public function basePath(string $path): string
    {
        return self::$root_dir . '/' . trim($path,'/');
    }
    /**
     * Initialise the database configuration
     * @return void
     */
    private function initDB(): void
    {
        (new DBConfig())
            ->host($_ENV['DB_HOST'])
            ->port($_ENV['DB_PORT'])
            ->db($_ENV['DB_NAME'])
            ->username($_ENV['DB_USER'])
            ->password($_ENV['DB_PASSWORD']);
    }
    /**
     * @return void
     * @throws \Exception
     */
    public function run(): void
    {
        $this->initDB();
        $this->routeProvider();
        $this->router->run();
    }
}