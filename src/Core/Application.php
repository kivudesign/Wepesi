<?php
/*
 * Copyright (c) 2023. wepesi dev framework
 */

namespace Wepesi\Core;

use Wepesi\Core\Database\Database;
use Wepesi\Core\Database\DatabaseConfig;
use Wepesi\Core\Database\Providers\Contracts\DatabaseContracts;
use Wepesi\Core\DI\Container;
use Wepesi\Core\DI\Contracts\ContainerContracts;
use Wepesi\Core\Http\Input;
use Wepesi\Core\Http\Redirect;
use Wepesi\Core\Http\Request;
use Wepesi\Core\Http\Response;
use Wepesi\Core\Resolver\Option;
use Wepesi\Core\Resolver\OptionsResolver;
use Wepesi\Core\Routing\Route;
use Wepesi\Core\Routing\RouteFileRegistrar;
use Wepesi\Core\Routing\Router;
use Wepesi\Core\Validation\MessageErrorBuilder;
use Wepesi\Core\Validation\Providers\Contracts\MessageBuilderContracts;
use Wepesi\Core\Validation\Validate;
use Wepesi\Core\View\Provider\Contract\ViewEngineContracts;
use Wepesi\Core\View\View;

/**
 *
 */
class Application
{
    /**
     * @var string
     */
    private static string $APP_VIEW_PATH;

    /***
     * @var string
     */
    private static string $APP_ROUTE_PATH;

    /**
     * @var ContainerContracts
     */
    private static ContainerContracts $container;

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
     * @var Router
     */
    private Router $router;

    /**
     * Application constructor.
     * @param string $path root path directory of the application
     * @param AppConfiguration $config
     * @param ContainerContracts $Container
     */
    public function __construct(string $path, AppConfiguration $config, ContainerContracts $Container)
    {
        $config_params = $config->getCongigurations();
        self::$root_dir = str_replace("\\", '/', $path);
        self::$APP_DOMAIN = serverDomain()->domain;
        self::$APP_LANG = $config_params['lang'] ?? 'fr';
        self::$APP_TEMPLATE = $config_params['app_template'] ?? '';
        self::$layout_content_param = 'layout_content';
        self::$layout = '';
        self::$APP_VIEW_PATH = $config_params['view'];
        self::$APP_ROUTE_PATH = $config_params['route'];
        //
        self::$container = $Container;

        self::$container->instance(self::class, $this);
        self::$container->instance(Container::class, self::$container);

        $this->registerCoreServices();
        $this->loadServiceProviders();

        $this->router = self::$container->get(Router::class);
    }

    /**
     * Get the application dependency injection container.
     *
     * @return ContainerContracts
     */
    public static function container(): ContainerContracts
    {
        return self::$container;
    }

    /**
     * Resolve a service from the application container.
     *
     * @param string $abstract
     * @param array<int|string, mixed> $parameters
     * @return mixed
     */
    public static function make(string $abstract, array $parameters = []): mixed
    {
        return self::$container->get($abstract, $parameters);
    }

    /**
     * Register a service in the application container.
     *
     * @param string $abstract
     * @param callable|string|null $concrete
     * @return void
     */
    public static function bind(string $abstract, callable|string|null $concrete = null): void
    {
        self::$container->bind($abstract, $concrete);
    }

    /**
     * Register a singleton service in the application container.
     *
     * @param string $abstract
     * @param callable|string|null $concrete
     * @return void
     */
    public static function singleton(string $abstract, callable|string|null $concrete = null): void
    {
        self::$container->singleton($abstract, $concrete);
    }

    /**
     * Load application service bindings.
     *
     * @return void
     */
    private function loadServiceProviders(): void
    {
        $serviceConfigPath = self::$root_dir . '/config/services.php';

        if (!file_exists($serviceConfigPath)) {
            return;
        }

        $provider = require $serviceConfigPath;

        if (is_callable($provider)) {
            $provider();
        }
    }
    /**
     * @return string
     */
    public static function getViewPath(): string
    {
        return self::$root_dir . self::$APP_VIEW_PATH;
    }



    /**
     * @return string
     */
    public static function getRootDir(): string
    {
        return self::$root_dir;
    }

    /**
     * Define a layout to be used by all pages in the application.
     * can be set at the top of your application to be available everywhere.
     * @param string $layout
     * @return void
     */
    public static function setLayout(string $layout): void
    {
        self::$layout = self::getRootDir() . self::$APP_VIEW_PATH . trim($layout, '/');
    }

    /**
     * @param string $layout_name
     * @return void
     */
    public static function setLayoutcontentparam(string $layout_name): void
    {
        self::$layout_content_param = $layout_name;
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
        $base_route_path = self::getRootDir() . self::$APP_ROUTE_PATH;
        $api_route_path = $base_route_path . '/api.php';
        // register Api route
        if (file_exists($api_route_path)) {
            $this->router->api($this->registerRoute('/api.php'));
        }
        // register web route
        $web_route_path = $base_route_path . '/web.php';
        if (file_exists($web_route_path)) {
            $this->router->group([], $this->registerRoute('/web.php'));
        }
        // missing route source
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
        return $this->basePath(self::$APP_ROUTE_PATH . '/' . trim($path,'/'));
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
     * Initialize the database configuration
     * @return void
     */
    private function initDB(): void
    {
        self::$container->get(DatabaseConfig::class)
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

    /**
     * Register framework core services in the dependency injection container.
     *
     * @return void
     */
    private function registerCoreServices(): void
    {
        self::$container->instance(self::class, $this);
        self::$container->instance(ContainerContracts::class, self::$container);
        self::$container->instance(Container::class, self::$container);

        /*
         * Core application services.
         */
        self::$container->singleton(Router::class);
        self::$container->singleton(DatabaseConfig::class);

        /*
         * Database uses a private constructor, so it must be registered through getInstance().
         */
        self::$container->singleton(Database::class, function () {
            return Database::getInstance();
        });

        self::$container->singleton(DatabaseContracts::class, function () {
            return Database::getInstance();
        });

        /*
         * Route-related classes receive runtime constructor parameters.
         * They must be bind(), not singleton().
         */
        self::$container->bind(Route::class);
        self::$container->bind(RouteFileRegistrar::class);

        /*
        * Resolver classes receive runtime parameters.
        */
        self::$container->bind(Option::class);
        self::$container->bind(OptionsResolver::class);


        self::$container->bind(MessageBuilderContracts::class, MessageErrorBuilder::class);
        /*
         * HTTP services.
         */
        self::$container->singleton(Request::class, function () {
            return Request::createFromGlobals();
        });
        self::$container->singleton(Response::class);
        self::$container->singleton(Input::class);
        self::$container->singleton(Redirect::class);

        /*
         * View and validation services.
         */
        self::$container->singleton(View::class);
        self::$container->bind(ViewEngineContracts::class, View::class);
        self::$container->singleton(Validate::class);
        self::$container->singleton(MessageErrorBuilder::class);

        /*
         * Utility services.
         */
        self::$container->singleton(Config::class);
        self::$container->singleton(DotEnv::class);
        self::$container->singleton(Hash::class);
        self::$container->singleton(I18n::class);
        self::$container->singleton(Session::class);
        self::$container->singleton(Token::class);
        self::$container->singleton(JWT::class);
        self::$container->singleton(Email::class);
        self::$container->singleton(Media::class);
        self::$container->singleton(MetaData::class);
        self::$container->singleton(Bundles::class);
    }
}