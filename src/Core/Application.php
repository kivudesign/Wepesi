<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core;

use Wepesi\Core\Routing\Router;

class Application
{
    /**
     * define application global configuration
     * @var string
     */
    public static string $ROOT_DIR;
    public static string $APP_DOMAIN;
    public static string $APP_LANG;
    public static ?string $APP_TEMPLATE;
    public static ?string $LAYOUT = null;
    private static array $params = [];
    private Router $router;

    /**
     * Application constructor.
     * @param string $path path root directory of the application
     * @param AppConfiguration $config
     */
    public function __construct(string $path, AppConfiguration $config)
    {

        self::$ROOT_DIR = str_replace("\\", '/', $path);
        self::$APP_DOMAIN = $this->domainSetup()->app_domain;
        self::$params = $config->generate();
        self::$APP_TEMPLATE = self::$params['app_template'] ?? null;
        self::$APP_LANG = self::$params['lang'] ?? 'fr';
        $this->router = new Router();
    }

    /**
     * @return object
     */
    private function domainSetup(): object
    {
        $server_name = $_SERVER['SERVER_NAME'];
        $protocol = strtolower(explode('/', $_SERVER['SERVER_PROTOCOL'])[0]);
        $domain = self::getDomainIp() === '127.0.0.1' ? "$protocol://$server_name" : $server_name;
        return (object)[
            'server_name' => $server_name,
            'protocol' => $protocol,
            'app_domain' => $domain,
        ];
    }

    /**
     * use method to get domain ip
     * @return string
     */
    public static function getDomainIp() : string
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ($ip == '::1') {
            $ip = gethostbyname(getHostName());
        }
        return $ip;
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
        self::$LAYOUT = $layout;
    }

    /**
     * @return Router
     */
    public function router(): Router
    {
        return $this->router;
    }

    public function run()
    {
        $this->router->run();
    }
}