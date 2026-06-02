<?php
/*
 * Copyright (c) 2023. Wepesi.
 */

/**
 * Wepesi built-in autoloader.
 *
 * This autoloader exists to avoid requiring composer dump-autoload
 * every time a new app controller, middleware, model, or component is added.
 *
 * It supports:
 * - PSR-4 framework classes: Wepesi\... => src/...
 * - PSR-4 app classes: App\... => app/...
 * - Legacy app classes without namespace:
 *   UsersController => app/Controller/UsersController.php
 *   Users => app/Models/Users.php
 *   exampleValidation => app/Middleware/exampleValidation.php
 */

$rootDir = appDirSeparator(dirname(__DIR__));

$prefixes = [
    'Wepesi\\' => $rootDir . '/src/',
    'wepesi\\' => $rootDir . '/src/',
    'App\\' => $rootDir . '/app/',
    'app\\' => $rootDir . '/app/',
];

spl_autoload_register(static function (string $class) use ($prefixes, $rootDir): void {
    /*
     * First: PSR-4 style loading.
     *
     * Example:
     * Wepesi\Core\Routing\Router
     * => src/Core/Routing/Router.php
     *
     * App\Controller\UsersController
     * => app/Controller/UsersController.php
     */
    foreach ($prefixes as $prefix => $baseDir) {
        $prefixLength = strlen($prefix);

        if (strncmp($prefix, $class, $prefixLength) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $prefixLength);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }

        return;
    }

    /*
     * Second: legacy non-namespaced app loading.
     *
     * This is useful for existing Wepesi projects where classes are declared like:
     *
     * class UsersController extends Controller
     * class exampleValidation extends MiddleWare
     * class Users extends Entity
     */
    $legacyDirectories = [
        $rootDir . '/app/Controller',
        $rootDir . '/app/Middleware',
        $rootDir . '/app/Models',
        $rootDir . '/app/Components',
    ];

    foreach ($legacyDirectories as $directory) {
        $file = $directory . '/' . $class . '.php';

        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
}, true, true);