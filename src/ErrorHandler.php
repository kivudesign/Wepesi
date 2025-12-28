<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

namespace Wepesi;

use ErrorException;
use Throwable;
use Wepesi\Core\Http\Response;
use Wepesi\Renderer\DevRenderer;
use Wepesi\Reporter\TransportInterface;
use Wepesi\Reporter\FileTransport;

/**
 * Global error and exception handler
 */
class ErrorHandler
{
    private static ?self $instance = null;
    private static array $config = [];
    private static array $user = [];
    private static array $transports = [];
    private static bool $registered = false;

    /**
     * Register the error handler
     * @param array $config
     * @return void
     */
    public static function register(array $config = []): void
    {
        if (self::$registered) {
            return;
        }

        self::$config = array_merge([
            'enabled' => true,
            'environment' => 'dev',
            'send_reports' => false,
            'transports' => [
                'file' => [
                    'path' => storage_path('logs/errors')
                ]
            ],
            'show_pretty_page_in_dev' => true,
            'sanitize_fields' => ['password', 'token', 'authorization', 'cookie', 'secret', 'api_key'],
            'release' => null,
        ], $config);

        if (!self::$config['enabled']) {
            return;
        }

        // Initialize transports
        self::initializeTransports();

        // Register PHP handlers
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);

        self::$registered = true;
    }

    /**
     * Initialize configured transports
     * @return void
     */
    private static function initializeTransports(): void
    {
        if (isset(self::$config['transports']['file'])) {
            $fileConfig = self::$config['transports']['file'];
            $path = $fileConfig['path'] ?? storage_path('logs/errors');
            self::$transports[] = new FileTransport($path);
        }
    }

    /**
     * Handle uncaught exceptions
     * @param Throwable $e
     * @return void
     */
    public static function handleException(Throwable $e): void
    {
        self::captureException($e);
        
        // In CLI mode, just output the error
        if (php_sapi_name() === 'cli') {
            echo "Fatal error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . PHP_EOL;
            exit(1);
        }
        
        // For HTTP requests, render response
        $response = self::renderResponse($e);
        echo $response;
        exit(1);
    }

    /**
     * Handle PHP errors by converting to ErrorException
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @return bool
     * @throws ErrorException
     */
    public static function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (!(error_reporting() & $level)) {
            return false;
        }

        throw new ErrorException($message, 0, $level, $file, $line);
    }

    /**
     * Handle fatal errors at shutdown
     * @return void
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $exception = new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            
            self::captureException($exception);
        }
    }

    /**
     * Capture an exception and send to transports
     * @param Throwable $e
     * @param array $context
     * @return string Event ID
     */
    public static function captureException(Throwable $e, array $context = []): string
    {
        $event = self::buildEvent($e, 'error', $context);
        self::sendEvent($event);
        return $event['id'];
    }

    /**
     * Capture a message
     * @param string $message
     * @param array $context
     * @return string Event ID
     */
    public static function captureMessage(string $message, array $context = []): string
    {
        $event = self::buildEvent(null, 'info', $context, $message);
        self::sendEvent($event);
        return $event['id'];
    }

    /**
     * Set user context
     * @param array $user
     * @return void
     */
    public static function setUser(array $user): void
    {
        self::$user = $user;
    }

    /**
     * Render HTTP response for an exception
     * @param Throwable $e
     * @return string HTML or JSON response
     */
    public static function renderResponse(Throwable $e): string
    {
        $isDev = self::$config['environment'] === 'dev';
        $showPrettyPage = self::$config['show_pretty_page_in_dev'] ?? true;

        // In development with pretty page enabled
        if ($isDev && $showPrettyPage) {
            $event = self::buildEvent($e, 'error', []);
            $renderer = new DevRenderer();
            
            http_response_code(500);
            header('Content-Type: text/html; charset=UTF-8');
            return $renderer->render($e, $event);
        }

        // In production or dev without pretty page, return generic error
        http_response_code(500);
        
        // Check if request accepts JSON
        $acceptsJson = isset($_SERVER['HTTP_ACCEPT']) && 
                       (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if ($acceptsJson) {
            header('Content-Type: application/json; charset=UTF-8');
            return json_encode([
                'error' => 'Internal Server Error',
                'message' => 'An unexpected error occurred.'
            ]);
        }

        // Plain text response
        header('Content-Type: text/plain; charset=UTF-8');
        return "500 Internal Server Error\n\nAn unexpected error occurred.";
    }

    /**
     * Build event payload
     * @param Throwable|null $e
     * @param string $level
     * @param array $context
     * @param string|null $message
     * @return array
     */
    private static function buildEvent(?Throwable $e, string $level, array $context = [], ?string $message = null): array
    {
        $event = [
            'id' => self::generateEventId(),
            'timestamp' => date('c'),
            'level' => $level,
            'environment' => self::$config['environment'] ?? 'production',
            'release' => self::$config['release'] ?? null,
        ];

        if ($e !== null) {
            $event['exception'] = [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack' => self::buildStackTrace($e),
            ];
        }

        if ($message !== null) {
            $event['message'] = $message;
        }

        // Add request context
        $event['request'] = self::buildRequestContext();

        // Add user context
        if (!empty(self::$user)) {
            $event['user'] = self::sanitizeData(self::$user);
        }

        // Add custom context
        if (!empty($context)) {
            $event['context'] = self::sanitizeData($context);
        }

        return $event;
    }

    /**
     * Build stack trace array
     * @param Throwable $e
     * @return array
     */
    private static function buildStackTrace(Throwable $e): array
    {
        $stack = [];
        $trace = $e->getTrace();

        foreach ($trace as $frame) {
            $stackFrame = [
                'file' => $frame['file'] ?? '[internal]',
                'line' => $frame['line'] ?? 0,
                'function' => $frame['function'] ?? '',
            ];

            if (isset($frame['class'])) {
                $stackFrame['class'] = $frame['class'];
                $stackFrame['type'] = $frame['type'] ?? '';
            }

            // Add code snippet for debugging
            if (isset($frame['file']) && isset($frame['line']) && is_file($frame['file'])) {
                $stackFrame['code_snippet'] = self::getCodeSnippet($frame['file'], $frame['line']);
            }

            $stack[] = $stackFrame;
        }

        return $stack;
    }

    /**
     * Get code snippet around a line
     * @param string $file
     * @param int $line
     * @param int $context
     * @return array
     */
    private static function getCodeSnippet(string $file, int $line, int $context = 3): array
    {
        try {
            $lines = file($file);
            if ($lines === false) {
                return [];
            }

            $start = max(0, $line - $context - 1);
            $end = min(count($lines), $line + $context);
            
            $snippet = [];
            for ($i = $start; $i < $end; $i++) {
                $snippet[$i + 1] = rtrim($lines[$i]);
            }

            return $snippet;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Build request context
     * @return array
     */
    private static function buildRequestContext(): array
    {
        $request = [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'path' => $_SERVER['REQUEST_URI'] ?? '',
        ];

        if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
            $request['query_string'] = $_SERVER['QUERY_STRING'];
        }

        // Sanitize headers
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace('HTTP_', '', $key);
                $headerName = str_replace('_', '-', strtolower($headerName));
                $headers[$headerName] = $value;
            }
        }
        
        $request['headers'] = self::sanitizeData($headers);

        return $request;
    }

    /**
     * Sanitize sensitive data
     * @param array $data
     * @return array
     */
    private static function sanitizeData(array $data): array
    {
        $sanitizeFields = self::$config['sanitize_fields'] ?? [];
        
        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key);
            
            // Check if field should be sanitized
            foreach ($sanitizeFields as $sensitiveField) {
                if (strpos($lowerKey, strtolower($sensitiveField)) !== false) {
                    $data[$key] = '[FILTERED]';
                    break;
                }
            }
            
            // Recursively sanitize nested arrays
            if (is_array($value)) {
                $data[$key] = self::sanitizeData($value);
            }
        }

        return $data;
    }

    /**
     * Send event to all configured transports
     * @param array $event
     * @return void
     */
    private static function sendEvent(array $event): void
    {
        if (!self::$config['send_reports'] && self::$config['environment'] === 'dev') {
            // In dev mode with send_reports disabled, still write to file for debugging
            if (!empty(self::$transports)) {
                foreach (self::$transports as $transport) {
                    if ($transport instanceof FileTransport) {
                        $transport->send($event);
                    }
                }
            }
            return;
        }

        foreach (self::$transports as $transport) {
            try {
                $transport->send($event);
            } catch (\Throwable $e) {
                // Fail silently to avoid infinite loops
                error_log('Transport failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Generate unique event ID
     * @return string
     */
    private static function generateEventId(): string
    {
        // Simple UUID v4 implementation
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variant 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
