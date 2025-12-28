<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

namespace Wepesi\Renderer;

use Throwable;

/**
 * Development error page renderer
 */
class DevRenderer
{
    /**
     * Render an error page for development
     * @param Throwable $e
     * @param array $event
     * @return string HTML output
     */
    public function render(Throwable $e, array $event): string
    {
        $exceptionClass = get_class($e);
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        $file = htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8');
        $line = $e->getLine();
        $eventId = htmlspecialchars($event['id'] ?? 'N/A', ENT_QUOTES, 'UTF-8');

        // Build stack trace with code snippets
        $stackHtml = $this->renderStackTrace($e);

        // Build request info
        $requestHtml = $this->renderRequestInfo($event);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error: {$exceptionClass}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #1a1a1a;
            color: #e0e0e0;
            line-height: 1.6;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        .header h1 { 
            font-size: 24px; 
            margin-bottom: 10px;
            color: #fff;
        }
        .header .message { 
            font-size: 18px; 
            margin-bottom: 15px;
            color: #f0f0f0;
            word-wrap: break-word;
        }
        .header .location {
            font-size: 14px;
            color: #d0d0d0;
            font-family: 'Courier New', monospace;
        }
        .event-id {
            background: #2a2a2a;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #888;
        }
        .event-id strong { color: #aaa; }
        .section {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .section h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #fff;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .stack-frame {
            background: #1f1f1f;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            border-left: 4px solid #667eea;
        }
        .stack-frame .function {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #a8d0ff;
            margin-bottom: 8px;
        }
        .stack-frame .location {
            font-size: 12px;
            color: #888;
            margin-bottom: 10px;
        }
        .code-snippet {
            background: #161616;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .code-line {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.4;
            white-space: pre;
        }
        .code-line.highlight {
            background: #4a2a2a;
            border-left: 3px solid #ff6b6b;
            padding-left: 7px;
        }
        .line-number {
            display: inline-block;
            width: 50px;
            color: #555;
            text-align: right;
            margin-right: 15px;
            user-select: none;
        }
        .request-info {
            font-size: 14px;
            line-height: 1.8;
        }
        .request-info dt {
            color: #aaa;
            font-weight: 600;
            margin-top: 10px;
        }
        .request-info dd {
            color: #e0e0e0;
            font-family: 'Courier New', monospace;
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$exceptionClass}</h1>
            <div class="message">{$message}</div>
            <div class="location">in {$file} on line {$line}</div>
        </div>
        
        <div class="event-id">
            <strong>Event ID:</strong> {$eventId}
        </div>

        <div class="section">
            <h2>Stack Trace</h2>
            {$stackHtml}
        </div>

        <div class="section">
            <h2>Request Information</h2>
            {$requestHtml}
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Render stack trace with code snippets
     * @param Throwable $e
     * @return string
     */
    private function renderStackTrace(Throwable $e): string
    {
        $html = '';
        $trace = $e->getTrace();

        // Add the exception location as first frame
        array_unshift($trace, [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'function' => '{main}',
            'class' => null,
        ]);

        foreach ($trace as $index => $frame) {
            $file = $frame['file'] ?? '[internal function]';
            $line = $frame['line'] ?? 0;
            $function = $frame['function'] ?? '';
            $class = $frame['class'] ?? '';
            $type = $frame['type'] ?? '';

            $functionDisplay = $class ? htmlspecialchars($class . $type . $function . '()', ENT_QUOTES, 'UTF-8') 
                                      : htmlspecialchars($function . '()', ENT_QUOTES, 'UTF-8');
            $fileDisplay = htmlspecialchars($file, ENT_QUOTES, 'UTF-8');

            $html .= "<div class='stack-frame'>";
            $html .= "<div class='function'>#{$index} {$functionDisplay}</div>";
            $html .= "<div class='location'>{$fileDisplay}";
            if ($line > 0) {
                $html .= ":{$line}";
            }
            $html .= "</div>";

            // Add code snippet if file exists
            if ($file !== '[internal function]' && is_file($file) && $line > 0) {
                $snippet = $this->getCodeSnippet($file, $line);
                if ($snippet) {
                    $html .= "<div class='code-snippet'>{$snippet}</div>";
                }
            }

            $html .= "</div>";
        }

        return $html;
    }

    /**
     * Get code snippet around a specific line
     * @param string $file
     * @param int $line
     * @param int $context Number of lines to show before and after
     * @return string|null
     */
    private function getCodeSnippet(string $file, int $line, int $context = 5): ?string
    {
        try {
            $lines = file($file);
            if ($lines === false) {
                return null;
            }

            $start = max(0, $line - $context - 1);
            $end = min(count($lines), $line + $context);
            
            $snippet = '';
            for ($i = $start; $i < $end; $i++) {
                $lineNum = $i + 1;
                $lineContent = htmlspecialchars(rtrim($lines[$i]), ENT_QUOTES, 'UTF-8');
                $isHighlight = ($lineNum === $line);
                $class = $isHighlight ? 'code-line highlight' : 'code-line';
                
                $snippet .= "<div class='{$class}'>";
                $snippet .= "<span class='line-number'>{$lineNum}</span>";
                $snippet .= $lineContent;
                $snippet .= "</div>";
            }

            return $snippet;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Render request information
     * @param array $event
     * @return string
     */
    private function renderRequestInfo(array $event): string
    {
        $request = $event['request'] ?? [];
        
        $html = '<dl class="request-info">';
        
        if (isset($request['method'])) {
            $method = htmlspecialchars($request['method'], ENT_QUOTES, 'UTF-8');
            $html .= "<dt>Method</dt><dd>{$method}</dd>";
        }
        
        if (isset($request['path'])) {
            $path = htmlspecialchars($request['path'], ENT_QUOTES, 'UTF-8');
            $html .= "<dt>Path</dt><dd>{$path}</dd>";
        }
        
        if (isset($request['query_string']) && !empty($request['query_string'])) {
            $query = htmlspecialchars($request['query_string'], ENT_QUOTES, 'UTF-8');
            $html .= "<dt>Query String</dt><dd>{$query}</dd>";
        }

        if (isset($event['user']) && !empty($event['user'])) {
            $user = htmlspecialchars(json_encode($event['user'], JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8');
            $html .= "<dt>User</dt><dd><pre>{$user}</pre></dd>";
        }

        if (isset($event['environment'])) {
            $env = htmlspecialchars($event['environment'], ENT_QUOTES, 'UTF-8');
            $html .= "<dt>Environment</dt><dd>{$env}</dd>";
        }
        
        $html .= '</dl>';
        
        return $html;
    }
}
