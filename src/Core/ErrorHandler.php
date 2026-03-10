<?php
/*
 * Copyright (c) 2024. Wepesi Dev Framework
 */

namespace Wepesi\Core;

use Throwable;

/**
 * ErrorHandler class provides comprehensive error and exception handling
 * similar to Sentry or xdebug, with environment-aware display modes.
 * 
 * @package Wepesi\Core
 */
class ErrorHandler
{
    /**
     * @var bool Whether to display detailed errors (dev mode)
     */
    private static bool $detailedErrors = true;

    /**
     * @var bool Whether error handler is registered
     */
    private static bool $registered = false;

    /**
     * @var array Collected errors during request
     */
    private static array $errors = [];

    /**
     * Register the error handler
     * 
     * @param bool $detailedErrors Whether to show detailed errors (true for dev, false for production)
     * @return void
     */
    public static function register(bool $detailedErrors = true): void
    {
        if (self::$registered) {
            return;
        }

        self::$detailedErrors = $detailedErrors;
        self::$registered = true;

        // Set error reporting based on environment
        if (self::$detailedErrors) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
            ini_set('display_errors', '0');
        }

        // Register handlers
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Handle PHP errors
     * 
     * @param int $errno Error number
     * @param string $errstr Error message
     * @param string $errfile File where error occurred
     * @param int $errline Line number where error occurred
     * @return bool
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        // Don't handle errors suppressed with @
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $error = [
            'type' => self::getErrorType($errno),
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
            'time' => date('Y-m-d H:i:s')
        ];

        self::$errors[] = $error;

        // Convert error to exception for better handling
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Handle uncaught exceptions
     * 
     * @param Throwable $exception The exception to handle
     * @return void
     */
    public static function handleException(Throwable $exception): void
    {
        self::$errors[] = [
            'type' => 'Exception',
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
            'time' => date('Y-m-d H:i:s')
        ];

        self::displayError($exception);
        exit(1);
    }

    /**
     * Handle fatal errors on shutdown
     * 
     * @return void
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $exception = new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            
            self::handleException($exception);
        }
    }

    /**
     * Display error information
     * 
     * @param Throwable $exception The exception to display
     * @return void
     */
    private static function displayError(Throwable $exception): void
    {
        // Clear any previous output - properly handle all buffer levels
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Only set HTTP response code if headers haven't been sent yet
        if (!headers_sent()) {
            http_response_code(500);
        }

        if (self::$detailedErrors) {
            self::displayDetailedError($exception);
        } else {
            self::displayProductionError();
        }
    }

    /**
     * Display detailed error for development environment
     * 
     * @param Throwable $exception The exception to display
     * @return void
     */
    private static function displayDetailedError(Throwable $exception): void
    {
        $errorClass = get_class($exception);
        $errorMessage = htmlspecialchars($exception->getMessage());
        $errorFile = htmlspecialchars($exception->getFile());
        $errorLine = $exception->getLine();
        $errorCode = $exception->getCode();
        $trace = $exception->getTrace();

        // Get file context
        $fileLines = self::getFileContext($exception->getFile(), $exception->getLine());

        // Check if it's an AJAX request
        if (self::isAjaxRequest()) {
            self::sendJsonError($exception);
            return;
        }

        echo self::renderDetailedErrorHtml([
            'class' => $errorClass,
            'message' => $errorMessage,
            'file' => $errorFile,
            'line' => $errorLine,
            'code' => $errorCode,
            'trace' => $trace,
            'fileLines' => $fileLines,
            'environment' => 'development'
        ]);
    }

    /**
     * Display generic error for production environment
     * 
     * @return void
     */
    private static function displayProductionError(): void
    {
        if (self::isAjaxRequest()) {
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            echo json_encode([
                'status' => 'error',
                'message' => 'An internal server error occurred'
            ]);
            return;
        }

        echo self::renderProductionErrorHtml();
    }

    /**
     * Get file context around error line
     * 
     * @param string $file File path
     * @param int $line Line number
     * @param int $context Number of lines before and after
     * @return array
     */
    private static function getFileContext(string $file, int $line, int $context = 10): array
    {
        if (!file_exists($file)) {
            return [];
        }

        $lines = file($file);
        $start = max(0, $line - $context - 1);
        $end = min(count($lines), $line + $context);

        $context = [];
        for ($i = $start; $i < $end; $i++) {
            $context[$i + 1] = rtrim($lines[$i]);
        }

        return $context;
    }

    /**
     * Get error type name from error number
     * 
     * @param int $errno Error number
     * @return string
     */
    private static function getErrorType(int $errno): string
    {
        $types = [
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];

        return $types[$errno] ?? 'Unknown Error';
    }

    /**
     * Check if request is AJAX
     * 
     * @return bool
     */
    private static function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Send JSON error response
     * 
     * @param Throwable $exception The exception
     * @return void
     */
    private static function sendJsonError(Throwable $exception): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        
        $response = [
            'status' => 'error',
            'message' => $exception->getMessage(),
        ];

        if (self::$detailedErrors) {
            $response['exception'] = get_class($exception);
            $response['file'] = $exception->getFile();
            $response['line'] = $exception->getLine();
            $response['trace'] = array_map(function($trace) {
                return [
                    'file' => $trace['file'] ?? 'unknown',
                    'line' => $trace['line'] ?? 0,
                    'function' => $trace['function'] ?? 'unknown',
                    'class' => $trace['class'] ?? null
                ];
            }, $exception->getTrace());
        }

        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * Render detailed error HTML
     * 
     * @param array $data Error data
     * @return string
     */
    private static function renderDetailedErrorHtml(array $data): string
    {
        $trace = $data['trace'];
        $fileLines = $data['fileLines'];
        
        $traceHtml = '';
        foreach ($trace as $index => $item) {
            $file = htmlspecialchars($item['file'] ?? 'unknown');
            $line = $item['line'] ?? 0;
            $function = htmlspecialchars($item['function'] ?? 'unknown');
            $class = isset($item['class']) ? htmlspecialchars($item['class']) . '::' : '';
            
            $traceHtml .= "<div style='margin: 10px 0; padding: 10px; background: #f8f9fa; border-left: 3px solid #6c757d;'>";
            $traceHtml .= "<div style='font-weight: bold;'>#{$index} {$class}{$function}()</div>";
            $traceHtml .= "<div style='color: #6c757d; font-size: 0.9em;'>{$file}:{$line}</div>";
            $traceHtml .= "</div>";
        }

        $contextHtml = '';
        foreach ($fileLines as $lineNum => $lineContent) {
            $isErrorLine = $lineNum === $data['line'];
            $bgColor = $isErrorLine ? '#ffebee' : '#f5f5f5';
            $lineColor = $isErrorLine ? '#c62828' : '#424242';
            $weight = $isErrorLine ? 'bold' : 'normal';
            
            $contextHtml .= "<div style='background: {$bgColor}; padding: 2px 10px; font-family: monospace; font-size: 0.9em;'>";
            $contextHtml .= "<span style='color: {$lineColor}; font-weight: {$weight}; display: inline-block; width: 50px;'>{$lineNum}</span>";
            $contextHtml .= "<span style='color: {$lineColor}; font-weight: {$weight};'>" . htmlspecialchars($lineContent) . "</span>";
            $contextHtml .= "</div>";
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - {$data['class']}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: #f5f5f5; }
        .error-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .error-header { background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%); color: white; padding: 30px; border-radius: 8px 8px 0 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .error-type { font-size: 1.2em; opacity: 0.9; margin-bottom: 10px; }
        .error-message { font-size: 1.5em; font-weight: 600; margin-bottom: 15px; }
        .error-location { opacity: 0.9; font-family: monospace; }
        .error-body { background: white; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .section { margin-bottom: 30px; }
        .section-title { font-size: 1.2em; font-weight: 600; margin-bottom: 15px; color: #333; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; }
        .code-context { background: #f5f5f5; border-radius: 4px; overflow-x: auto; border: 1px solid #e0e0e0; }
        .environment-badge { display: inline-block; background: #ff9800; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.9em; font-weight: 600; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <div class="environment-badge">DEVELOPMENT MODE</div>
            <div class="error-type">{$data['class']}</div>
            <div class="error-message">{$data['message']}</div>
            <div class="error-location">at {$data['file']}:{$data['line']}</div>
        </div>
        <div class="error-body">
            <div class="section">
                <div class="section-title">Code Context</div>
                <div class="code-context">
                    {$contextHtml}
                </div>
            </div>
            <div class="section">
                <div class="section-title">Stack Trace</div>
                {$traceHtml}
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Render production error HTML
     * 
     * @return string
     */
    private static function renderProductionErrorHtml(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .error-container { text-align: center; padding: 40px; }
        .error-icon { font-size: 80px; color: #d32f2f; margin-bottom: 20px; }
        h1 { font-size: 2em; color: #333; margin-bottom: 10px; }
        p { color: #666; font-size: 1.1em; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1>Something went wrong</h1>
        <p>We're sorry, but something went wrong. Please try again later.</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get all collected errors
     * 
     * @return array
     */
    public static function getErrors(): array
    {
        return self::$errors;
    }

    /**
     * Clear collected errors
     * 
     * @return void
     */
    public static function clearErrors(): void
    {
        self::$errors = [];
    }
}
