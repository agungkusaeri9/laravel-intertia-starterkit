# Architecture Documentation

## Overview

This project is built using:

- Backend: Laravel 13
- Frontend: Vue.js 3
- Inertia.js
- MySQL
- Laravel Breeze / Laravel Sanctum (optional)
- Tailwind CSS

The application follows the Repository-Service Pattern and uses Inertia.js as the communication layer between Laravel and Vue.js.

---

# Architecture Pattern

```text
Browser
    ↓
Laravel Route
    ↓
Controller
    ↓
Service
    ↓
Repository Interface
    ↓
Repository Implementation
    ↓
Model
    ↓
MySQL
```

Response Flow

```text
MySQL
    ↓
Model
    ↓
Repository
    ↓
Service
    ↓
Controller
    ↓
Inertia Render
    ↓
Vue Page
```

---

# Application Layers

## Controller Layer

Responsibilities:

- Receive HTTP requests
- Call Service layer
- Return Inertia pages
- Redirect after actions

Example:

```php
class UserController extends Controller
{
    public function index()
    {
        $users = $this->userService->getAll();

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }
}
```

Controller should not contain:

- Query logic
- Business logic
- Transaction handling

---

## Service Layer

Responsibilities:

- Business rules
- Domain workflows
- Transaction handling
- Event dispatching

Example:

```php
class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            return $this->userRepository->create($data);

        });
    }
}
```

---

## Repository Layer

Responsibilities:

- Database access
- Query building
- Data persistence

Example:

```php
class UserRepository implements UserRepositoryInterface
{
    public function getAll()
    {
        return User::latest()->get();
    }

    public function create(array $data)
    {
        return User::create($data);
    }
}
```

---

## Model Layer

Responsibilities:

- Table mapping
- Relationships
- Scopes
- Accessors & Mutators

Example:

```php
class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

---

# Request Lifecycle

Page Visit

```text
User
  ↓
GET /users
  ↓
Route
  ↓
UserController@index
  ↓
UserService@getAll()
  ↓
UserRepository@getAll()
  ↓
User Model
  ↓
MySQL
  ↓
Inertia::render()
  ↓
Vue Page
```

Create Data

```text
Vue Form
  ↓
POST /users
  ↓
Controller
  ↓
Form Request Validation
  ↓
Service
  ↓
Repository
  ↓
Model
  ↓
MySQL
  ↓
Redirect
  ↓
Inertia Refresh
```

---

# Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Middleware/
│
├── Services/
│
├── Repositories/
│   ├── Contracts/
│   └── Eloquent/
│
├── Models/
│
├── Providers/
│
├── Policies/
│
├── Events/
│
├── Listeners/
│
└── Exceptions/
```

---

# Frontend Structure

```text
resources/js/
├── Pages/
│   ├── Dashboard/
│   ├── Users/
│   └── Products/
│
├── Components/
│
├── Layouts/
│
├── Composables/
│
├── Types/
│
└── Utils/
```

---

# Dependency Injection

Repository bindings are registered in AppServiceProvider.

```php
public function register(): void
{
    $this->app->bind(
        UserRepositoryInterface::class,
        UserRepository::class
    );
}
```

---

# Database Rules

Tables:

```text
users
products
orders
order_items
```

Primary Key:

```text
id
```

Foreign Key:

```text
user_id
product_id
order_id
```

---

# Transaction Rules

Transactions belong only in Service layer.

```php
DB::transaction(function () {

    $order = $this->orderRepository->create($data);

    $this->paymentRepository->create([
        'order_id' => $order->id
    ]);

});
```

---

# Validation Rules

All validation must use Form Requests.

```text
app/Http/Requests
```

Examples:

```text
StoreUserRequest
UpdateUserRequest
StoreOrderRequest
```

---

# Architectural Principles

1. Controllers stay thin.
2. Services contain business logic.
3. Repositories contain database queries.
4. Models contain relationships only.
5. Vue Pages are page-level components.
6. Reusable UI belongs in Components.
7. All database access goes through repositories.
8. All business rules go through services.

---

# Future Scalability

```text
Controller
    ↓
Service
    ↓
Repository
    ↓
Model
```

Can be extended with:

```text
Repository
    ├── MySQL
    ├── Redis
    ├── External API
    └── Search Engine
```
