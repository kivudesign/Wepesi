<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

/**
 * Error Handler Configuration
 * 
 * This file contains the configuration for the built-in error handler.
 * The error handler captures PHP exceptions and errors, and provides
 * developer-friendly error pages in development and structured logging.
 */

return [
    /*
     * Enable or disable the error handler
     */
    'enabled' => true,

    /*
     * Application environment (dev or prod)
     * In dev: shows detailed error pages
     * In prod: shows generic error messages
     */
    'environment' => getenv('APP_ENV') ?: 'dev',

    /*
     * Whether to send error reports to transports
     * When false in dev, only file transport will be used
     */
    'send_reports' => getenv('APP_ENV') === 'prod',

    /*
     * Show pretty error page in development
     */
    'show_pretty_page_in_dev' => true,

    /*
     * Transports for error events
     */
    'transports' => [
        'file' => [
            'path' => storage_path('logs/errors'),
        ],
    ],

    /*
     * Fields to sanitize in error reports (case-insensitive substring match)
     * These fields will be replaced with [FILTERED] in event payloads
     */
    'sanitize_fields' => [
        'password',
        'token',
        'authorization',
        'cookie',
        'secret',
        'api_key',
        'access_token',
        'refresh_token',
    ],

    /*
     * Release version (optional)
     * Used to track which version of your app generated the error
     */
    'release' => getenv('APP_VERSION') ?: null,
];
