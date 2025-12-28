# Wepesi

* Wepesi library
  `wepesi` is the quick ans simple framework the help you devellop simple web application with php and design OOP
  concept,
  it has been design by following most off principle for big framework but make it simple for develloper.

## Installation

The installation of the application does not require somuch thing;

- In you are familliar with `composer` to install :
  You can find project directly on packagest on : https://packagist.org/packages/wepesi/wepesi

```shell
 composer create-project wepesi/wepesi
```

and it will create a project for You.

- In case you are not familliar with `composer` you can donwload it directly the source code on github
  on :https://github.com/kivudesign/Wepesi, no nee of extra module to start workin on the project.

# Intoduction

Wepesi is a simple Web framework that help you devellop simple web application, and benefit advantage of large php
framework like

- routing
- controller
- simple ORM *without migration
- MVC design patern
- OOP
- Middleware
- Validation
- View
- **Built-in Error Handler** (v0.1) - Production-safe error capture and developer-friendly error pages

All module are built-id, its has been design to give to make the framework flexible, you can restructure everything as
you want and be able to add more module.

# Error Handler

Wepesi includes a built-in error handler that captures PHP exceptions and errors, providing developer-friendly error pages in development and structured error logging for production.

## Features

- **Global Error Capture**: Automatically captures PHP exceptions, errors, and fatal errors
- **Development Mode**: Beautiful HTML error pages with stack traces and code snippets
- **Production Mode**: Generic error responses with detailed logging
- **File Transport**: Structured JSON logs with daily rotation in `storage/logs/errors/`
- **Security**: Automatic sanitization of sensitive fields (passwords, tokens, etc.)
- **User Context**: Track which user experienced the error
- **Middleware Integration**: Catches exceptions in HTTP request pipeline

## Setup

### 1. Enable the Error Handler

In your `index.php` or bootstrap file, register the error handler:

```php
<?php
// Load error handler configuration
$errorConfig = require __DIR__ . '/config/error.php';

// Register the error handler
\Wepesi\ErrorHandler::register($errorConfig);

// Continue with your application bootstrap...
$app = new Application($ROOT_DIR, $configuration);
$app->run();
```

### 2. Configuration

The error handler is configured via `config/error.php`:

```php
return [
    'enabled' => true,                              // Enable/disable error handler
    'environment' => getenv('APP_ENV') ?: 'dev',    // dev or prod
    'send_reports' => getenv('APP_ENV') === 'prod', // Send to transports
    'show_pretty_page_in_dev' => true,              // Show detailed error page in dev
    'transports' => [
        'file' => [
            'path' => storage_path('logs/errors'),  // Log file location
        ],
    ],
    'sanitize_fields' => [                          // Fields to filter from logs
        'password', 'token', 'authorization', 
        'cookie', 'secret', 'api_key'
    ],
];
```

### 3. Environment Variables

Add to your `.env` file:

```env
APP_ENV=dev              # or 'prod' for production
APP_VERSION=1.0.0        # Optional: track which version generated errors
```

## Usage

### Automatic Exception Capture

Once registered, the error handler automatically captures all uncaught exceptions:

```php
// This will be automatically captured and logged
throw new Exception('Something went wrong!');
```

### Manual Exception Capture

Capture exceptions manually for logging without stopping execution:

```php
try {
    // Some risky operation
    $result = processPayment($data);
} catch (Exception $e) {
    // Log the error and continue
    \Wepesi\ErrorHandler::captureException($e, [
        'payment_id' => $paymentId,
        'amount' => $amount
    ]);
    
    // Handle gracefully
    return response()->json(['error' => 'Payment failed'], 500);
}
```

### Capture Messages

Log informational messages or warnings:

```php
\Wepesi\ErrorHandler::captureMessage('Payment gateway timeout', [
    'gateway' => 'stripe',
    'attempt' => 3
]);
```

### Set User Context

Associate errors with the current user:

```php
// After user login
\Wepesi\ErrorHandler::setUser([
    'id' => $user->id,
    'email' => $user->email,
    'username' => $user->username
]);

// All subsequent errors will include this user context
```

### Use Error Middleware

Add the middleware to your routes to catch exceptions in the HTTP pipeline:

```php
use Wepesi\ErrorMiddleware;

// In your routing configuration
$router->middleware(ErrorMiddleware::class);
```

## Development vs Production

### Development Mode (`APP_ENV=dev`)

- Shows beautiful HTML error pages with:
  - Exception class and message
  - File and line number
  - Full stack trace with code snippets
  - Request information
  - User context
  - Event ID for tracking
- Writes structured logs to `storage/logs/errors/errors-YYYY-MM-DD.jsonl`

### Production Mode (`APP_ENV=prod`)

- Returns generic error responses:
  - JSON: `{"error": "Internal Server Error", "message": "An unexpected error occurred."}`
  - HTML: Plain text "500 Internal Server Error"
- Writes detailed error information to log files
- Sanitizes sensitive data before logging

## Log Format

Error logs are stored as JSON Lines (one JSON object per line):

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000",
  "timestamp": "2023-12-28T13:05:24+00:00",
  "level": "error",
  "environment": "prod",
  "exception": {
    "class": "Exception",
    "message": "Database connection failed",
    "code": 0,
    "file": "/path/to/file.php",
    "line": 42,
    "stack": [...]
  },
  "request": {
    "method": "POST",
    "path": "/api/users",
    "headers": {...}
  },
  "user": {
    "id": 123,
    "email": "user@example.com"
  }
}
```

## Important Notes

### Disable Xdebug in Production

The error handler replaces the need for Xdebug in production. Disable Xdebug for better performance:

```ini
; In your php.ini
xdebug.mode=off
```

### Security Considerations

- Sensitive fields are automatically filtered from logs
- Never enable `show_pretty_page_in_dev` in production
- Protect your `storage/logs/errors/` directory from public access
- Review logs regularly for security incidents

### Performance

- File writes use atomic operations (temp file + rename)
- Minimal overhead when no errors occur
- Daily log rotation prevents large files

# Integration

no need to know about composer, the simple way is to download the all the project and place on the server side
devellopenent.
decompress the file, and folder the instruction.

- if you are using `wamp`, place the decopress folder on the `www` folder of `wamp` folder with is on the root of the
  dick c: on windows
- if you are using `xamp`, you place the folder on the `htdocs`
  you can rename the folder as you want according to you need

# Structure

the library is subdivised in multiple part with folder, we have:

- `class` : where all model and where we can find the core logic to run the libray.
  there is a folder call `app`, in with you are not allowed to modified any class if you dont know what you are doing.
  in some case, it will have an impact on the way your application is working.
- `controller`: where you can creat all the controller. the system has been designed like that to make a difference
  between controller and model.
- `config`: where you can config the database configuration, and the autoloading.
- `layout`: the layout help manage all `css style` or `javascript script`, or not. the idea, is to have a better
  logical. in case you use the layout,
  there is a way yu can accee those file by using wepesi class `Bundle`.
- `route`: is where you can define all your route
- `views`: here is where you will create all the pages that will display by the user.
- `index.php`: this is the main file. to start the app,
- `.htaccess`: this help to manage the routing.

# *Hope you enjoy.
