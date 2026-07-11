# Contributing to Wepesi

Thank you for your interest in contributing to the Wepesi framework! This guide describes the conventions and structure you must follow when contributing to the core source code located in the `src/` folder.

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Code Structure](#code-structure)
3. [Naming Conventions](#naming-conventions)
4. [Abstract Classes (Providers)](#abstract-classes-providers)
5. [Interfaces (Contracts)](#interfaces-contracts)
6. [Directory Layout Example](#directory-layout-example)
7. [Running Tests](#running-tests)

---

## Getting Started

1. Fork the repository and clone your fork locally.
2. Install dependencies:
   ```shell
   composer install
   ```
3. Copy the environment file and configure it:
   ```shell
   cp .env.example .env
   ```
4. Run the test suite to confirm everything is green before making any changes:
   ```shell
   ./vendor/bin/phpunit
   ```

---

## Code Structure

All framework source code lives under `src/Core/`. Follow these rules when adding or modifying source files:

### Single, self-contained class

If the new functionality can be expressed in a single class that has no closely related companion classes, the file may live directly inside the relevant module directory.

```
src/Core/
└── MyFeature.php          # standalone class — no sub-folder required
```

### Feature with multiple related classes

When two or more closely related classes form a cohesive module, group them in their own sub-folder named after the feature. The folder and all class names **must** comply with [PSR-4](https://www.php-fig.org/psr/psr-4/) so that Composer autoloading works without any extra configuration.

```
src/Core/
└── MyFeature/
    ├── MyFeature.php
    ├── MyFeatureHelper.php
    └── ...
```

---

## Naming Conventions

### Classes

- Use **PascalCase** (also known as UpperCamelCase) for all class names.
- File names must exactly match the class name (PSR-4).
- Namespace segments must mirror the directory path under `src/`.

```php
// src/Core/MyFeature/MyFeatureHelper.php
namespace Wepesi\Core\MyFeature;

class MyFeatureHelper
{
    // ...
}
```

### Variables and Properties

- Use **snake_case** for all variable names and class properties.

```php
class UserService
{
    private string $user_name;
    private int    $max_retry_count;

    public function findByEmail(string $email_address): ?array
    {
        $query_result = [];
        // ...
        return $query_result;
    }
}
```

---

## Abstract Classes (Providers)

Abstract classes act as the base implementation for a feature module. They must:

- Have a name suffixed with **`Provider`** (e.g., `DatabaseProvider`, `ViewBuilderProvider`).
- Be placed inside a `Providers/` sub-folder within the relevant module directory.

```
src/Core/MyFeature/
└── Providers/
    └── MyFeatureProvider.php   # abstract class
```

```php
// src/Core/MyFeature/Providers/MyFeatureProvider.php
namespace Wepesi\Core\MyFeature\Providers;

abstract class MyFeatureProvider
{
    // shared implementation ...
}
```

---

## Interfaces (Contracts)

Interfaces define the public API of a feature module. They must:

- Have a name suffixed with **`Contract`** (e.g., `DatabaseContract`, `ViewEngineContract`).
- Be placed inside a `Contracts/` folder that is itself a sub-folder of `Providers/`.

```
src/Core/MyFeature/
└── Providers/
    ├── MyFeatureProvider.php       # abstract class
    └── Contracts/
        └── MyFeatureContract.php   # interface
```

```php
// src/Core/MyFeature/Providers/Contracts/MyFeatureContract.php
namespace Wepesi\Core\MyFeature\Providers\Contracts;

interface MyFeatureContract
{
    public function execute(): void;
}
```

The abstract provider class then typically implements its own contract:

```php
// src/Core/MyFeature/Providers/MyFeatureProvider.php
namespace Wepesi\Core\MyFeature\Providers;

use Wepesi\Core\MyFeature\Providers\Contracts\MyFeatureContract;

abstract class MyFeatureProvider implements MyFeatureContract
{
    // shared implementation ...
}
```

---

## Directory Layout Example

The following shows how a fully fleshed-out `Payment` module would be structured:

```
src/Core/
└── Payment/
    ├── Payment.php                          # main concrete class
    ├── PaymentProcessor.php                 # related concrete class
    ├── Providers/
    │   ├── BasePaymentProvider.php          # abstract base class (Provider)
    │   └── Contracts/
    │       └── PaymentContract.php          # interface (Contract)
    └── Traits/
        └── PaymentHelperTrait.php           # optional shared trait
```

---

## Running Tests

```shell
# Run the full test suite
./vendor/bin/phpunit

# Run a specific test file
./vendor/bin/phpunit src/Test/ConfigTest.php
```

All new code must be covered by unit tests. Test files live under `src/Test/` and should mirror the module structure under `src/Core/`. For example, a class at `src/Core/Payment/Payment.php` should have its tests at `src/Test/Payment/PaymentTest.php`.

---

*Happy coding! 🚀*
