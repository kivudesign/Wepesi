# Wepesi Error Handler

The Wepesi framework includes a built-in error handler that provides comprehensive error and exception handling similar to popular tools like Sentry or Xdebug, with environment-aware display modes.

## Features

- **Comprehensive Error Handling**: Catches all PHP errors, warnings, notices, and exceptions
- **Beautiful Error Pages**: Displays detailed, formatted error information in development mode
- **Environment-Aware**: Automatically adjusts display based on `APP_ENV` setting
- **Stack Trace Visualization**: Shows detailed stack traces with file paths and line numbers
- **Code Context**: Highlights the error line with surrounding code for better debugging
- **AJAX-Friendly**: Returns JSON responses for AJAX requests
- **Production-Safe**: Shows generic error messages in production to protect sensitive information
- **Automatic Registration**: Integrates seamlessly with the application bootstrap

## How It Works

The ErrorHandler is automatically registered when the Application is instantiated. It sets up handlers for:
- Regular PHP errors (warnings, notices, etc.)
- Uncaught exceptions
- Fatal errors during shutdown

## Configuration

The error handler behavior is controlled by the `APP_ENV` environment variable in your `.env` file:

```env
# Development mode - shows detailed errors
APP_ENV=dev

# Production mode - shows generic error messages
APP_ENV=prod
```

## Development Mode

In development mode (`APP_ENV=dev`), the error handler displays:
- Full exception class name
- Detailed error message
- File path and line number where the error occurred
- Code context showing lines around the error
- Complete stack trace with function calls and locations
- Visual highlighting of the error line

Example of development error display:
- Red header with "DEVELOPMENT MODE" badge
- Error type and message prominently displayed
- Syntax-highlighted code context
- Expandable stack trace with all function calls

## Production Mode

In production mode (`APP_ENV=prod`), the error handler:
- Hides all sensitive information
- Shows a generic "Something went wrong" message
- Returns appropriate HTTP 500 status code
- Protects your application internals from exposure

## JSON Responses

When an AJAX request fails, the error handler automatically returns JSON:

**Development mode:**
```json
{
  "status": "error",
  "message": "Detailed error message",
  "exception": "ExceptionClassName",
  "file": "/path/to/file.php",
  "line": 42,
  "trace": [...]
}
```

**Production mode:**
```json
{
  "status": "error",
  "message": "An internal server error occurred"
}
```

## Manual Usage

While the error handler is registered automatically, you can also register it manually:

```php
use Wepesi\Core\ErrorHandler;

// Register in development mode
ErrorHandler::register(true);

// Register in production mode
ErrorHandler::register(false);
```

## Error Collection

The error handler collects errors during request processing. You can access them:

```php
use Wepesi\Core\ErrorHandler;

// Get all collected errors
$errors = ErrorHandler::getErrors();

// Clear collected errors
ErrorHandler::clearErrors();
```

## Integration with Existing Code

The error handler is integrated into the application bootstrap process in `src/Core/Application.php`. When the Application is instantiated, it automatically:

1. Checks the `APP_ENV` setting
2. Registers the error handler with appropriate mode
3. Sets up all error and exception handlers

No additional configuration or code changes are needed in your controllers or routes.

## Benefits

1. **Better Debugging**: Detailed error information helps developers quickly identify and fix issues
2. **Professional Appearance**: Beautiful error pages instead of raw PHP errors
3. **Security**: Production mode protects sensitive information
4. **Consistency**: Unified error handling across the entire application
5. **Developer-Friendly**: Similar experience to popular frameworks and tools
6. **No External Dependencies**: Built-in, no need for Xdebug or external services

## Replacing Xdebug

This error handler provides many of the error display features of Xdebug:
- Stack traces
- File and line information
- Variable context (through exception messages)
- Formatted HTML output

You can use this handler instead of Xdebug for error display purposes, though Xdebug still provides additional debugging features like step debugging and profiling.

## Examples

### Example 1: Catching an Undefined Variable

```php
// This will trigger the error handler
echo $undefinedVariable;
```

**Development Output**: Beautiful HTML page showing:
- Error type: "Notice"
- Message: "Undefined variable: undefinedVariable"
- Exact file and line number
- Code context with the error line highlighted

**Production Output**: Generic error page

### Example 2: Catching an Exception

```php
throw new Exception("Something went wrong in my application");
```

**Development Output**: Detailed exception information with full stack trace

**Production Output**: "Something went wrong" message

### Example 3: AJAX Error

```javascript
// JavaScript AJAX request
fetch('/api/endpoint')
  .then(response => response.json())
  .catch(error => console.error(error));
```

If the endpoint throws an error, the error handler returns a JSON response that can be easily handled by your JavaScript code.

## Maintenance

The error handler is located at `src/Core/ErrorHandler.php`. It's a single, self-contained class with no external dependencies, making it easy to maintain and customize if needed.
