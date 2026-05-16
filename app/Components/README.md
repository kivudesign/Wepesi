Wepesi/Component

## 📋 Table of Contents

1. [Introduction](#introduction)
2. [Quick Start](#quick-start)
3. [Usage Patterns](#usage-patterns)
    - [Array Style (Immediate)](#array-style-immediate)
    - [Chainable Style (Fluent)](#chainable-style-fluent)
    - [Auto-Rendering](#auto-rendering-with-__tostring)
4. [Common Properties](#common-properties)
5. [Component Reference](#component-reference)
    - [References](#Example.md)
6. [Extending the Library](#extending-the-library)
    - [Creating Custom Components](#creating-custom-components)
    - [Auto-Discovery](#auto-discovery)
    - [Config File Registration](#config-file-registration)
7. [Best Practices](#best-practices)

---

## Introduction

This library provides a **framework-agnostic** component system for building PHP user interfaces. It offers two syntax styles (array-based and chainable), automatic HTML escaping for security, and works with **any PHP project** (plain PHP, Laravel, Symfony, WordPress, etc.).

**Key Features:**
- No external dependencies
- Automatic XSS protection via `htmlspecialchars`
- Two usage styles (array or fluent chainable)
- Common attributes (`id`, `class`, `data-*`, custom attributes)
- Test coverage included

---
It doesn't include CSS you add your own classes via the `class()` method.

## Quick Start

```php
<?php

use App\useComponent;

// Register components (once in your bootstrap)
useComponent::loadConfig(__DIR__ . '/config/components.php');

// Build a simple card
echo useComponent::card([
    'title' => 'Welcome',
    'content' => 'Hello World!'
]);

// Or using chainable syntax
echo useComponent::card()
    ->title('Welcome')
    ->content('Hello World!')
    ->class('shadow-lg');

// Mix components together
echo useComponent::form([
    'action' => '/submit',
    'content' => 
        useComponent::input(['name' => 'email', 'type' => 'email']) .
        useComponent::button(['label' => 'Submit', 'type' => 'submit'])
]);
```

---

## Usage Patterns
There are two syntax styles, array and chainable, array style is concise for simple components;
chainable style is more readable for complex configurations with many options, you can call render method to get the output.

### Array Style (Immediate)

Pass an array of data. Returns HTML string directly.

```php
echo useComponent::card([
    'title' => 'My Card',
    'content' => 'Card content here',
    'class' => 'mb-4'
]);
```

### Chainable Style (Fluent)

Build components method by method. Returns component instance that renders when echoed after calling the render() method.

```php
echo useComponent::card()
    ->title('My Card')
    ->content('Card content here')
    ->class('mb-4')
    ->id('unique-card')
    ->render();
```

## Common Properties

All components inherit these methods from `BaseComponent`:

| Method                                                  | Description                                        | Example                                           |
|---------------------------------------------------------|----------------------------------------------------|---------------------------------------------------|
| `id(string $id)`                                        | Sets HTML `id` attribute                           | `->id('user-form')`                               |
| `class(string $class)`                                  | Sets HTML `class` attribute                        | `->class('btn-primary')`                          |
| `attribute(string $name, string $value)`                | Sets any HTML attribute                            | `->attribute('aria-label', 'Close')`              |
| `data(string $name, string $value)`                     | Sets `data-*` attributes                           | `->data('controller', 'modal')`                   |
| `loadViewFile(string $viewComponent, array $data = [])` | load component content from specific file location | `->loadViewFile('profile', ['name'=>'John Doe'])` |

**Example with common properties:**

```php
echo useComponent::button()
    ->label('Delete')
    ->id('deleteBtn')
    ->class('btn btn-danger')
    ->attribute('aria-label', 'Delete item')
    ->data('confirm', 'Are you sure?')
    ->data('id', 123)
    ->render();
// Output: <button id="deleteBtn" class="btn btn-danger" aria-label="Delete item" data-confirm="Are you sure?" data-id="123">Delete</button>
```

---
## Component example
As you see an example for more detail you check on [the Reference](Example.md).

**Array style:**
```php
useComponent::card([
    'title' => 'User Profile',
    'content' => '<p>John Doe</p><p>john@example.com</p>',
    'footer' => 'Last updated: today',
    'class' => 'shadow-sm'
])
```

**Chainable style:**
```php
useComponent::card()
    ->title('User Profile')
    ->content(useComponent::input(['name' => 'username']))
    ->footer('Save changes')
    ->class('border-primary')
```
---

## Extending the Library

### Creating Custom Components

1. **Create a component class** extending `BaseComponent`:

```php
<?php
namespace App\Components;

use Wepesi\Core\BaseComponent;

class ProgressBar extends BaseComponent
{
    protected int $percent = 0;
    protected string $color = 'primary';

    public function percent(int $percent): self
    {
        $this->percent = min(100, max(0, $percent));
        return $this;
    }

    public function color(string $color): self
    {
        $this->color = htmlspecialchars($color);
        return $this;
    }

    public function render(array $data = []): string
    {
        // Apply array data if provided
        if (!empty($data)) {
            $this->percent = $data['percent'] ?? $this->percent;
            $this->color = $data['color'] ?? $this->color;
        }

        $attrs = $this->buildAttributes();
        return <<<HTML
<div{$attrs}>
    <div class="progress">
        <div class="progress-bar bg-{$this->color}" 
             style="width: {$this->percent}%">{$this->percent}%</div>
    </div>
</div>
HTML;
    }
}
```

2. **Register your component** in `config/components.php`:

```php
return [
    // ... existing components
    'progress' => \YourLibrary\Component\ProgressBar::class,
];
```

3. **Use your component**:

```php
echo useComponent::progress()
    ->percent(75)
    ->color('success')
    ->class('my-3');

// Or array style
echo useComponent::progress([
    'percent' => 50,
    'color' => 'warning'
]);
```

### Auto-Discovery

Automatically register all components in a folder:

```php
useComponent::registerFromDirectory(__DIR__ . '/app/Components', 'App\Components\');
```

This registers any class implementing `ComponentContract` using its lowercase class name as the component key.

### Config File Registration

You can register manually on the `config/components.php` all your component created

```php
<?php
return [
    'card'  => \YourLibrary\Component\Card::class,
    'alert' => \YourLibrary\Component\Alert::class,
    // Add your custom components here
];
```

Then load once in your bootstrap:

```php
useComponent::loadConfig(__DIR__ . '/config/components.php');
```
---

## Best Practices

### 1. **Always escape output**
The library automatically escapes values passed to setters and array data. Never pass unescaped user input directly.

### 2. **Use configuration file**
Register all components once in your application bootstrap instead of relying on auto-discovery on every request.

### 3. **Leverage the content pattern**
For complex layouts, nest components inside each other using the `content` parameter:

```php
useComponent::card([
    'title' => 'User Form',
    'content' => useComponent::form([
        'action' => '/save',
        'content' => useComponent::input(['name' => 'name']) . 
                    useComponent::button(['label' => 'Save'])
    ])
]);
```
or load with `loadViewFile` method from a designed files only available for the chained option where you can even data:
```php
useComponent::card()
    ->title('User Form')
    ->loadViewFile('footer.php',
       [
          'name'=>'name', 
          'label'=>'save'
       ])
    ->render();
```
### 4. **Combine with old input**
When handling form submissions, repopulate fields with old input:

```php
$old = $_POST;
echo useComponent::input([
    'name' => 'email',
    'value' => $old['email'] ?? ''
]);
```

### 5. **Keep components pure**
Don't add business logic inside components. They should only handle rendering and basic transformations.

### 6. **Use data attributes for JavaScript**
Instead of inline JavaScript, add `data-*` attributes and let external JS handle behavior:

```php
useComponent::button()
    ->label('Delete')
    ->data('confirm', 'Are you sure?')
    ->data('action', 'delete')
    ->render();
```

---

### How do I handle CSRF protection?
**A:** Add a hidden input manually inside forms:

```php
useComponent::form([
    'content' => 
        '<input type="hidden" name="_token" value="' . csrf_token() . '">' .
        useComponent::input(['name' => 'email'])
]);
```
---

## Contributing

Contributions are welcome! Please submit pull requests or open issues on GitHub.

---

**Happy building! 🚀**