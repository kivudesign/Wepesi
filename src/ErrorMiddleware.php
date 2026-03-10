<?php
/*
 * Copyright (c) 2023-2024. Wepesi Dev Framework
 */

namespace Wepesi;

use Throwable;
use Wepesi\Core\Http\MiddleWare;

/**
 * Middleware to catch exceptions in HTTP request pipeline
 */
class ErrorMiddleware extends MiddleWare
{
    /**
     * Handle the request and catch any exceptions
     * @param callable $next
     * @return mixed
     */
    public function handle(callable $next)
    {
        try {
            return $next();
        } catch (Throwable $e) {
            // Capture the exception
            ErrorHandler::captureException($e);
            
            // Render and return error response
            $response = ErrorHandler::renderResponse($e);
            echo $response;
            exit(1);
        }
    }
}
