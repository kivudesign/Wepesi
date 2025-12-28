<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

namespace Wepesi\Reporter;

/**
 * File-based transport for error events
 */
class FileTransport implements TransportInterface
{
    private string $logPath;

    /**
     * @param string $logPath Path to logs directory
     */
    public function __construct(string $logPath)
    {
        $this->logPath = rtrim($logPath, '/');
    }

    /**
     * Send an error event by writing to a daily log file
     * @param array $event
     * @return bool
     */
    public function send(array $event): bool
    {
        try {
            // Ensure directory exists
            if (!is_dir($this->logPath)) {
                mkdir($this->logPath, 0755, true);
            }

            // Create filename with date pattern
            $date = date('Y-m-d');
            $filename = $this->logPath . '/errors-' . $date . '.jsonl';

            // Encode event as JSON line
            $jsonLine = json_encode($event, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

            // Write atomically using temp file + rename
            $tempFile = $filename . '.tmp.' . uniqid();
            
            // If file exists, append to temp first
            if (file_exists($filename)) {
                copy($filename, $tempFile);
                file_put_contents($tempFile, $jsonLine, FILE_APPEND | LOCK_EX);
                rename($tempFile, $filename);
            } else {
                // New file, write directly
                file_put_contents($tempFile, $jsonLine, LOCK_EX);
                rename($tempFile, $filename);
            }

            return true;
        } catch (\Throwable $e) {
            // Fail silently to avoid infinite loops
            error_log('FileTransport failed: ' . $e->getMessage());
            return false;
        }
    }
}
