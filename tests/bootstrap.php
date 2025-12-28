<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

/**
 * PHPUnit Test Bootstrap
 * This file provides a minimal bootstrap for running tests
 */

// Define ROOT_DIR for tests
$GLOBALS['ROOT_DIR'] = dirname(__DIR__);

// Load helper functions
require_once __DIR__ . '/../config/function.php';

// Simple PSR-4 autoloader for Wepesi namespace
spl_autoload_register(function ($class) {
    // Only handle Wepesi namespace
    if (strpos($class, 'Wepesi\\') !== 0) {
        return;
    }

    // Remove Wepesi\ prefix
    $relativeClass = substr($class, 7);
    
    // Convert namespace to file path
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});
