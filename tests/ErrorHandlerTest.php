<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

namespace Wepesi\Test;

use PHPUnit\Framework\TestCase;
use Wepesi\ErrorHandler;
use Wepesi\Reporter\FileTransport;

class ErrorHandlerTest extends TestCase
{
    private string $testLogPath;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Reset error handler state
        ErrorHandler::reset();
        
        // Create temporary log directory for tests
        $this->testLogPath = sys_get_temp_dir() . '/wepesi_error_logs_' . uniqid();
        mkdir($this->testLogPath, 0755, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Reset error handler state
        ErrorHandler::reset();
        
        // Clean up test log files
        if (is_dir($this->testLogPath)) {
            $this->removeDirectory($this->testLogPath);
        }
    }
    
    /**
     * Recursively remove directory
     * @param string $dir
     * @return void
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    public function testCaptureExceptionWritesToFileTransport()
    {
        // Register error handler with test configuration
        $config = [
            'enabled' => true,
            'environment' => 'prod',
            'send_reports' => true,
            'transports' => [
                'file' => [
                    'path' => $this->testLogPath
                ]
            ],
            'show_pretty_page_in_dev' => false,
        ];

        ErrorHandler::register($config);

        // Create a test exception
        $exception = new \Exception('Test exception message');

        // Capture the exception
        $eventId = ErrorHandler::captureException($exception);

        // Verify event ID was returned
        $this->assertNotEmpty($eventId);
        $this->assertIsString($eventId);

        // Verify log file was created
        $date = date('Y-m-d');
        $logFile = $this->testLogPath . '/errors-' . $date . '.jsonl';
        
        $this->assertFileExists($logFile, 'Error log file should exist');

        // Verify file contains valid JSON
        $content = file_get_contents($logFile);
        $this->assertNotEmpty($content, 'Log file should not be empty');

        // Parse JSON line
        $lines = explode(PHP_EOL, trim($content));
        $this->assertGreaterThan(0, count($lines), 'Should have at least one log line');

        $eventData = json_decode($lines[0], true);
        $this->assertNotNull($eventData, 'Log line should be valid JSON');

        // Verify event structure
        $this->assertArrayHasKey('id', $eventData);
        $this->assertArrayHasKey('timestamp', $eventData);
        $this->assertArrayHasKey('level', $eventData);
        $this->assertArrayHasKey('exception', $eventData);
        $this->assertArrayHasKey('environment', $eventData);

        // Verify exception details
        $this->assertEquals('Exception', $eventData['exception']['class']);
        $this->assertEquals('Test exception message', $eventData['exception']['message']);
        $this->assertEquals('prod', $eventData['environment']);
    }

    public function testCaptureMessageWritesToFileTransport()
    {
        // Register error handler with test configuration
        $config = [
            'enabled' => true,
            'environment' => 'dev',
            'send_reports' => true,
            'transports' => [
                'file' => [
                    'path' => $this->testLogPath
                ]
            ],
        ];

        ErrorHandler::register($config);

        // Capture a message
        $eventId = ErrorHandler::captureMessage('Test message', ['key' => 'value']);

        // Verify event ID was returned
        $this->assertNotEmpty($eventId);

        // Verify log file was created
        $date = date('Y-m-d');
        $logFile = $this->testLogPath . '/errors-' . $date . '.jsonl';
        
        $this->assertFileExists($logFile);

        // Parse JSON line
        $content = file_get_contents($logFile);
        $lines = explode(PHP_EOL, trim($content));
        $eventData = json_decode($lines[0], true);

        // Verify message details
        $this->assertArrayHasKey('message', $eventData);
        $this->assertEquals('Test message', $eventData['message']);
        $this->assertArrayHasKey('context', $eventData);
        $this->assertEquals('value', $eventData['context']['key']);
    }

    public function testSetUser()
    {
        $config = [
            'enabled' => true,
            'environment' => 'dev',
            'send_reports' => true,
            'transports' => [
                'file' => [
                    'path' => $this->testLogPath
                ]
            ],
        ];

        ErrorHandler::register($config);

        // Set user context
        ErrorHandler::setUser([
            'id' => 123,
            'email' => 'test@example.com'
        ]);

        // Capture an exception
        $exception = new \Exception('Test with user');
        ErrorHandler::captureException($exception);

        // Verify user context in log
        $date = date('Y-m-d');
        $logFile = $this->testLogPath . '/errors-' . $date . '.jsonl';
        $content = file_get_contents($logFile);
        $lines = explode(PHP_EOL, trim($content));
        $eventData = json_decode($lines[0], true);

        $this->assertArrayHasKey('user', $eventData);
        $this->assertEquals(123, $eventData['user']['id']);
        $this->assertEquals('test@example.com', $eventData['user']['email']);
    }

    public function testSanitizeSensitiveFields()
    {
        $config = [
            'enabled' => true,
            'environment' => 'dev',
            'send_reports' => true,
            'transports' => [
                'file' => [
                    'path' => $this->testLogPath
                ]
            ],
            'sanitize_fields' => ['password', 'token'],
        ];

        ErrorHandler::register($config);

        // Capture message with sensitive data
        ErrorHandler::captureMessage('Test', [
            'username' => 'john',
            'password' => 'secret123',
            'api_token' => 'abc123'
        ]);

        // Verify sanitization
        $date = date('Y-m-d');
        $logFile = $this->testLogPath . '/errors-' . $date . '.jsonl';
        $content = file_get_contents($logFile);
        $lines = explode(PHP_EOL, trim($content));
        $eventData = json_decode($lines[0], true);

        $this->assertEquals('john', $eventData['context']['username']);
        $this->assertEquals('[FILTERED]', $eventData['context']['password']);
        $this->assertEquals('[FILTERED]', $eventData['context']['api_token']);
    }

    public function testFileTransportCreatesDirectory()
    {
        $nonExistentPath = $this->testLogPath . '/nested/path';
        
        $transport = new FileTransport($nonExistentPath);
        
        $event = [
            'id' => 'test-id',
            'message' => 'test',
            'timestamp' => date('c'),
        ];

        $result = $transport->send($event);

        $this->assertTrue($result);
        $this->assertDirectoryExists($nonExistentPath);
    }
}
