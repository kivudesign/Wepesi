# Wepesi

Wepesi is a lightweight PHP framework for building web applications using MVC principles. It is designed to be simple, flexible, and easy to get started with — no heavy configuration required.

**Features:**

- Routing (GET, POST, PUT, PATCH, DELETE)
- Controllers
- Simple ORM (no migrations)
- Middleware
- Validation
- View rendering
- Sessions, JWT, and more — all built in

## Table of Contents

1. [Installation](#installation)
2. [Project Structure](#project-structure)
3. [Routing](#routing)
4. [Controllers](#controllers)
5. [Database](#database)
6. [Validation](#validation)

---

## Installation

### Using Composer (recommended)

Requires PHP >= 8.0. Create a new project with:

```shell
composer create-project wepesi/wepesi my-project
```

Then open `my-project/` in your web server root and configure your `.env` file (copy `.env.example` to `.env`).

### Manual Setup

1. Download or clone the repository:

   ```shell
   git clone https://github.com/kivudesign/Wepesi.git my-project
   ```

2. Copy the environment file and fill in your settings:

   ```shell
   cp .env.example .env
   ```

3. Place the project folder in your web server's document root:
   - **WAMP**: `C:/wamp/www/my-project`
   - **XAMPP**: `C:/xampp/htdocs/my-project`
   - **Linux/macOS Apache/Nginx**: configure a virtual host pointing to the project root

4. Navigate to `http://localhost/my-project` in your browser.

> No extra modules or build steps are needed. The `.htaccess` file handles URL rewriting automatically.

---

## Project Structure

```
my-project/
├── app/
│   ├── Controller/       # Your controllers
│   ├── Models/           # Your entity/model classes
│   ├── Middleware/       # Request middleware
│   ├── Routes/
│   │   ├── web.php       # Web routes
│   │   └── api.php       # API routes
│   └── Views/            # HTML view templates
├── config/               # Database and autoload configuration
├── src/                  # Framework core (do not modify)
├── .env                  # Environment variables
├── .htaccess             # URL rewriting
└── index.php             # Application entry point
```

---

## Routing

Routes are defined in `app/Routes/web.php` (web) or `app/Routes/api.php` (API). The `$router` object is available via `$app->router()`.

### Basic routes

```php
// Closure handler
$router->get('/', function () {
    echo 'Hello World!';
});

$router->post('/submit', function () {
    // handle POST request
});
```

### Route with a dynamic parameter

```php
$router->get('/users/:id', function ($id) {
    echo 'User ID: ' . $id;
});
```

Constrain the parameter to digits only using `->with()`:

```php
$router->get('/users/:id', function ($id) {
    echo 'User ID: ' . $id;
})->with('id', '[0-9]+');
```

### Route to a controller method

```php
use App\Controller\UserController;

$router->get('/users', [UserController::class, 'index']);
$router->post('/users', [UserController::class, 'store']);
```

### Route groups

```php
$router->group('/admin', function () use ($router) {
    $router->get('/', [AdminController::class, 'dashboard']);
    $router->get('/users', [AdminController::class, 'users']);
});
```

### Middleware on a route

```php
use App\Middleware\AuthMiddleware;

$router->get('/dashboard', [DashboardController::class, 'index'])
    ->middleware([AuthMiddleware::class, 'handle']);
```

### API routes

Routes defined inside `$router->api()` are automatically prefixed with `/api`:

```php
use Wepesi\Core\Routing\Router;

$router->api(function (Router $router) {
    $router->group('/v1', function (Router $router) {
        $router->get('/users', [UserController::class, 'index']);
    });
});
// Resolves to: /api/v1/users
```

### Custom 404 handler

```php
use Wepesi\Core\Http\Response;

$router->set404(function () {
    Response::send('Route not found', 404);
});
```

---

## Controllers

Controllers live in `app/Controller/` and extend `Wepesi\Core\Http\Controller`.

```php
<?php

namespace App\Controller;

use Wepesi\Core\Http\Controller;
use Wepesi\Core\Http\Input;
use Wepesi\Core\Http\Redirect;
use Wepesi\Core\Session;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // Render a view
    public function index(): void
    {
        $this->view->display('users/index');
    }

    // Read POST input and redirect
    public function store(): void
    {
        $name  = Input::post('name');
        $email = Input::post('email');

        // ... save to database ...

        Redirect::to('/users');
    }

    // Store a value in session
    public function setLocale(): void
    {
        Session::put('lang', Input::post('lang'));
        Redirect::to('/');
    }
}
```

Pass data to a view:

```php
public function show(int $id): void
{
    $user = (new UserEntity())->where(['id' => $id])->findOne();
    $this->view->assign('user', $user);
    $this->view->display('users/show');
}
```

---

## Database

Wepesi includes a simple ORM built around **Entity** classes. Create an entity in `app/Models/` by extending `Wepesi\Core\Database\Entity`.

### Defining an entity

```php
<?php

namespace App\Models;

use Wepesi\Core\Database\Entity;

class UserEntity extends Entity
{
    // The framework infers the table name from the class name ("user")
    // or you can override it:
    protected function getName(): string
    {
        return 'users';
    }
}
```

### Querying records

```php
$entity = new UserEntity();

// Fetch all users
$users = $entity->findAll();

// Fetch with conditions
$admins = $entity->where(['role' => 'admin'])->findAll();

// Fetch a single record
$user = $entity->where(['id' => 1])->findOne();

// Limit and order
$recent = $entity->orderby('created_at')->desc()->limit(10)->findAll();

// Count rows
$total = $entity->count();
```

### Inserting a record

```php
$entity = new UserEntity();
$result = $entity->save([
    'name'  => 'Jane Doe',
    'email' => 'jane@example.com',
    'role'  => 'member',
]);
```

### Updating a record

```php
$entity = new UserEntity();
$result = $entity
    ->where(['id' => 42])
    ->fields(['name' => 'Jane Smith'])
    ->update();
```

### Deleting a record

```php
$entity = new UserEntity();
$result = $entity->where(['id' => 42])->delete();
```

### Relationships

```php
class PostEntity extends Entity {}

class UserEntity extends Entity
{
    public function posts(): object
    {
        return $this->hasMany(new PostEntity());
    }
}
```

---

## Validation

Use `Wepesi\Core\Validation\Validate` together with the rule classes to validate incoming data.

### Basic usage

```php
use Wepesi\Core\Validation\Validate;
use Wepesi\Core\Validation\Rules\StringRules;
use Wepesi\Core\Validation\Rules\NumberRules;

$data = [
    'name'  => 'Jane Doe',
    'email' => 'jane@example.com',
    'age'   => 25,
];

$validate = new Validate();
$validate->check($data, [
    'name'  => (new StringRules())->required()->min(2)->max(100)->generate(),
    'email' => (new StringRules())->required()->email()->generate(),
    'age'   => (new NumberRules())->required()->positive()->generate(),
]);

if ($validate->passed()) {
    // Validation succeeded — proceed with business logic
} else {
    // Validation failed — inspect errors
    $errors = $validate->errors();
}
```

### Available rule classes

| Class          | Common methods                                      |
|----------------|-----------------------------------------------------|
| `StringRules`  | `required()`, `min(n)`, `max(n)`, `email()`, `url()`, `match('field')`, `unique('table')` |
| `NumberRules`  | `required()`, `min(n)`, `max(n)`, `positive()`      |
| `ArrayRules`   | `required()`, `string()`, `number()`, `structure([])` |
| `BooleanRules` | `required()`                                        |
| `DateRules`    | `required()`, `min('date')`, `max('date')`          |

### Validation inside a controller

```php
use Wepesi\Core\Http\Input;
use Wepesi\Core\Http\Response;
use Wepesi\Core\Validation\Validate;
use Wepesi\Core\Validation\Rules\StringRules;

public function store(): void
{
    $data = [
        'name'  => Input::post('name'),
        'email' => Input::post('email'),
    ];

    $validate = new Validate();
    $validate->check($data, [
        'name'  => (new StringRules())->required()->min(2)->generate(),
        'email' => (new StringRules())->required()->email()->generate(),
    ]);

    if (!$validate->passed()) {
        Response::send(['errors' => $validate->errors()], 422);
        return;
    }

    // ... save data ...
}
```

---

*Happy building! 🚀*

