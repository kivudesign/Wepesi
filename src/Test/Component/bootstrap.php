<?php
/*
 * Copyright (c) 2026. Wepesi Dev Framework
 */

// Autoload Composer dependencies
require_once __DIR__ . '/../../../vendor/autoload.php';

// Set timezone for consistent tests
date_default_timezone_set('Africa/Lubumbashi');

// Define test constants
define('TEST_ROOT', __DIR__);
define('FIXTURE_DIR', TEST_ROOT . '/Fixtures');
define('CONFIG_DIR', FIXTURE_DIR . '/config');

// Create test config directory if not exists
if (!is_dir(CONFIG_DIR)) {
    mkdir(CONFIG_DIR, 0777, true);
}

// Register error handler for tests
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Helper functions for tests
function createTestConfig(string $name, array $components): string
{
    $file = CONFIG_DIR . '/' . $name . '.php';
    file_put_contents($file, '<?php return ' . var_export($components, true) . ';');
    return $file;
}

function cleanupTestConfigs(): void
{
    foreach (glob(CONFIG_DIR . '/*.php') as $file) {
        unlink($file);
    }
}

// Cleanup on shutdown
register_shutdown_function('cleanupTestConfigs');