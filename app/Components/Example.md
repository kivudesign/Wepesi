## Component Reference

### Card

A container with header, body, and optional footer.

**Methods:**
- `title(string $title)` – Card header text
- `content(string $content)` – Main card content (can include HTML)
- `footer(string $footer)` – Optional footer text
- `class(string $class)` – Additional CSS classes
- `id(string $id)` – HTML ID attribute

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

### Button

Clickable button element.

**Methods:**
- `label(string $label)` – Button text
- `type(string $type)` – `button`, `submit`, or `reset`
- `disabled(bool $disabled = true)` – Disable button
- `class(string $class)` – CSS classes
- `id(string $id)` – HTML ID

**Array style:**
```php
useComponent::button([
    'label' => 'Save Changes',
    'type' => 'submit',
    'class' => 'btn-success',
    'disabled' => false
])
```

**Chainable style:**
```php
useComponent::button()
    ->label('Delete')
    ->type('button')
    ->disabled()
    ->class('btn-danger')
```

---

### Alert

Display status messages (success, error, warning, info).

**Methods:**
- `message(string $message)` – Alert text
- `type(string $type)` – `success`, `error`, `warning`, `info`
- `dismissible(bool $dismissible = true)` – Add close button
- `class(string $class)` – CSS classes

**Array style:**
```php
useComponent::alert([
    'message' => 'Operation completed successfully!',
    'type' => 'success',
    'dismissible' => true
])
```

**Chainable style:**
```php
useComponent::alert()
    ->message('Please check your email')
    ->type('info')
    ->dismissible()
```

---

### Input

Form input fields (text, email, password, number, etc.).

**Methods:**
- `type(string $type)` – Input type (text, email, password, number, etc.)
- `name(string $name)` – Field name
- `value(string $value)` – Current value
- `placeholder(string $placeholder)` – Placeholder text
- `required(bool $required = true)` – Make required
- `disabled(bool $disabled = true)` – Disable field
- `class(string $class)` – CSS classes
- `id(string $id)` – HTML ID

**Array style:**
```php
useComponent::input([
    'type' => 'email',
    'name' => 'user_email',
    'value' => 'john@example.com',
    'placeholder' => 'Enter your email',
    'required' => true,
    'class' => 'form-control'
])
```

**Chainable style:**
```php
useComponent::input()
    ->type('password')
    ->name('password')
    ->placeholder('Enter password')
    ->required()
    ->class('input-large')
```

---
ukuwe
### Textarea

Multi-line text input.

**Methods:**
- `name(string $name)` – Field name
- `value(string $value)` – Current value
- `placeholder(string $placeholder)` – Placeholder text
- `rows(int $rows)` – Number of visible rows
- `required(bool $required = true)` – Make required
- `disabled(bool $disabled = true)` – Disable field
- `class(string $class)` – CSS classes

**Array style:**
```php
useComponent::textarea([
    'name' => 'bio',
    'value' => 'Write something about yourself...',
    'rows' => 5,
    'class' => 'form-textarea'
])
```

**Chainable style:**
```php
useComponent::textarea()
    ->name('description')
    ->rows(8)
    ->placeholder('Detailed description...')
    ->required()
```

---

### Select

Dropdown selection menu with support for optgroups and per-option attributes.

**Methods:**
- `name(string $name)` – Field name
- `options(array $options)` – Options array (see formats below)
- `selected($value)` – Currently selected value
- `multiple(bool $multiple = true)` – Allow multiple selection
- `disabled(bool $disabled = true)` – Disable entire select
- `required(bool $required = true)` – Make required
- `class(string $class)` – CSS classes

**Option Formats:**

**Simple format:**
```php
'options' => ['value1' => 'Label 1', 'value2' => 'Label 2']
```

**Advanced format (per-option attributes):**
```php
'options' => [
    ['value' => 'active', 'label' => 'Active', 'selected' => true],
    ['value' => 'inactive', 'label' => 'Inactive', 'disabled' => true]
]
```

**Optgroups:**
```php
'options' => [
    ['optgroup' => 'Group 1', 'options' => ['val1' => 'Label 1']],
    ['optgroup' => 'Group 2', 'options' => ['val2' => 'Label 2']]
]
```

**Examples:**

```php
// Simple select
useComponent::select([
    'name' => 'country',
    'options' => ['us' => 'USA', 'uk' => 'UK', 'fr' => 'France'],
    'value' => 'uk'
])

// Advanced with disabled option
useComponent::select()
    ->name('status')
    ->options([
        ['value' => 'active', 'label' => 'Active', 'selected' => true],
        ['value' => 'pending', 'label' => 'Pending'],
        ['value' => 'inactive', 'label' => 'Inactive', 'disabled' => true]
    ])

// With optgroups
useComponent::select([
    'name' => 'car',
    'options' => [
        ['optgroup' => 'European', 'options' => ['bmw' => 'BMW', 'audi' => 'Audi']],
        ['optgroup' => 'Japanese', 'options' => ['toyota' => 'Toyota', 'honda' => 'Honda']]
    ]
])
```

---

### Checkbox

Single checkbox with optional label.

**Methods:**
- `name(string $name)` – Field name
- `value(string $value)` – Checkbox value (default: "1")
- `checked(bool $checked = true)` – Check/uncheck
- `label(string $label)` – Optional label text
- `disabled(bool $disabled = true)` – Disable checkbox
- `required(bool $required = true)` – Make required
- `class(string $class)` – CSS classes

**Array style:**
```php
useComponent::checkbox([
    'name' => 'newsletter',
    'label' => 'Subscribe to newsletter',
    'checked' => true,
    'value' => 'yes'
])
```

**Chainable style:**
```php
useComponent::checkbox()
    ->name('terms')
    ->label('I accept the terms')
    ->required()
    ->checked()
```

---

### Radio

Group of radio buttons.

**Methods:**
- `name(string $name)` – Group name
- `options(array $options)` – Options array (`value => label` or advanced format)
- `value($value)` – Currently selected value
- `inline(bool $inline = true)` – Display inline instead of block
- `disabled(bool $disabled = true)` – Disable entire group

**Array style:**
```php
useComponent::radio([
    'name' => 'gender',
    'options' => ['male' => 'Male', 'female' => 'Female'],
    'value' => 'male',
    'inline' => true
])
```

**Chainable style:**
```php
useComponent::radio()
    ->name('payment')
    ->options([
        'card' => 'Credit Card',
        ['value' => 'paypal', 'label' => 'PayPal', 'disabled' => true]
    ])
    ->value('card')
```

---

### Form

Form container that wraps other components.

**Methods:**
- `action(string $action)` – Form action URL
- `method(string $method)` – `post` or `get`
- `enctype(string $enctype)` – `multipart/form-data` for file uploads
- `content(string $content)` – HTML/content inside the form
- `class(string $class)` – CSS classes

**Important:** Use `content` to embed other components or raw HTML.

```php
useComponent::form([
    'action' => '/register',
    'method' => 'post',
    'class' => 'auth-form',
    'enctype' => 'multipart/form-data',
    'content' => 
        useComponent::input(['name' => 'username']) .
        useComponent::input(['type' => 'email', 'name' => 'email']) .
        useComponent::input(['type' => 'password', 'name' => 'password']) .
        useComponent::button(['label' => 'Register', 'type' => 'submit'])
])
```

---

### Table

Display tabular data.

**Methods:**
- `headers(array $headers)` – Column headers
- `rows(array $rows)` – Data rows (array of arrays)
- `emptyText(string $text)` – Message when no data
- `class(string $class)` – CSS classes

**Array style:**
```php
useComponent::table([
    'headers' => ['ID', 'Name', 'Email'],
    'rows' => [
        [1, 'John Doe', 'john@example.com'],
        [2, 'Jane Smith', 'jane@example.com']
    ],
    'class' => 'table-striped',
    'empty' => 'No users found'
])
```

**Chainable style:**
```php
useComponent::table()
    ->headers(['Product', 'Price', 'Stock'])
    ->rows([
        ['Apple', '$1.99', '150'],
        ['Banana', '$0.99', '200']
    ])
    ->class('bordered')
```

---

### Modal

Dialog popup window.

**Methods:**
- `id(string $id)` – Modal identifier (required)
- `title(string $title)` – Modal header
- `body(string $body)` – Modal content
- `footer(string $footer)` – Modal footer (buttons)
- `size(string $size)` – `sm`, `lg`, `xl`

**Array style:**
```php
useComponent::modal([
    'id' => 'userModal',
    'title' => 'Edit User',
    'body' => useComponent::form(['content' => '...']),
    'footer' => '<button data-bs-dismiss="modal">Close</button>',
    'size' => 'lg'
])
```

**Note:** Requires Bootstrap 5 JavaScript for interactive behavior.

---

### Badge

Small status indicators.

**Methods:**
- `text(string $text)` – Badge text
- `color(string $color)` – `primary`, `success`, `danger`, `warning`, `info`, `secondary`
- `class(string $class)` – Additional CSS classes

**Array style:**
```php
useComponent::badge([
    'text' => 'Active',
    'color' => 'success'
])
```

**Chainable style:**
```php
useComponent::badge()
    ->text('New')
    ->color('danger')
    ->class('rounded-pill')
```

---

### Tabs

Tabbed interface for organizing content.

**Methods:**
- `tabs(array $tabs)` – Array of `['title' => 'Tab1', 'content' => '...']`
- `active(int $index)` – Index of active tab (0-based)

**Array style:**
```php
useComponent::tabs([
    'tabs' => [
        ['title' => 'Profile', 'content' => useComponent::input(['name' => 'name'])],
        ['title' => 'Settings', 'content' => useComponent::checkbox(['label' => 'Enable email'])],
        ['title' => 'Billing', 'content' => '<p>Payment info</p>']
    ],
    'active' => 0
])
```

**Note:** Requires Bootstrap 5 JavaScript for interactive behavior.

---

### Dropdown

Menu of actions.

**Methods:**
- `label(string $label)` – Dropdown button text
- `items(array $items)` – Array of `['text'=>'...', 'url'=>'...', 'class'=>'...']`

**Array style:**
```php
useComponent::dropdown([
    'label' => 'Actions',
    'items' => [
        ['text' => 'Edit', 'url' => '/edit/1'],
        ['text' => 'Delete', 'url' => '/delete/1', 'class' => 'text-danger'],
        ['text' => 'Duplicate', 'url' => '/copy/1']
    ]
])
```

**Chainable style:**
```php
useComponent::dropdown()
    ->label('Manage')
    ->items([
        ['text' => 'Export CSV', 'url' => '/export'],
        ['text' => 'Import', 'url' => '/import']
    ])
```

**Note:** Requires Bootstrap 5 JavaScript for interactive behavior.

---

### Pagination

Page navigation links.

**Methods:**
- `current(int $page)` – Current page number
- `total(int $pages)` – Total number of pages
- `urlPattern(string $pattern)` – URL pattern with `{page}` placeholder

**Array style:**
```php
useComponent::pagination([
    'current' => 3,
    'total' => 10,
    'url' => '/users?page={page}'
])
```

**Chainable style:**
```php
useComponent::pagination()
    ->current(2)
    ->total(15)
    ->urlPattern('/products?page={page}')
```

---