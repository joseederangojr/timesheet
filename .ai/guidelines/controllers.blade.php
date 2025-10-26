# Controller Guidelines

## RESTful Method Naming Convention

Controllers in this application follow Laravel's RESTful resource controller conventions. Use these standardized method names for consistent API design:

### Standard RESTful Methods

| HTTP Verb | Method Name | Purpose | Example Usage |
|-----------|-------------|---------|---------------|
| GET | `index()` | Display a listing of resources | List all users |
| GET | `create()` | Show form for creating new resource | Show user creation form |
| POST | `store()` | Store a newly created resource | Create new user |
| GET | `show()` | Display specific resource | Show single user |
| GET | `edit()` | Show form for editing resource | Show user edit form |
| PUT/PATCH | `update()` | Update specific resource | Update existing user |
| DELETE | `destroy()` | Remove specific resource | Delete user |

### Implementation Rules

#### Controller Structure
- Controllers MUST NOT extend Laravel's base Controller class
- Use `final readonly class` for immutable controllers when appropriate
- Controllers should be focused on HTTP concerns only - delegate business logic to Actions, Queries, or Services
- Use dependency injection in constructor for better testability

#### Method Naming
- Always use RESTful method names (`store`, `show`, `update`, `destroy`) instead of custom names
- Avoid descriptive method names like `authenticate`, `sendMagicLink`, `logout`
- Group related functionality using resource routes with proper HTTP verbs

#### Facade Usage
- Prefer Laravel Facades over helper functions for easier testing and mocking
- Use `Auth::`, `Session::`, `URL::` instead of `auth()`, `session()`, `url()` helpers
- This enables better testing with `Auth::fake()`, `Session::fake()`, etc.

### Examples

#### ✅ Good: RESTful Controller
```php
final readonly class AuthController
{
    public function store(LoginRequest $request): RedirectResponse
    {
        // Handle login logic
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();
        Session::invalidate();
        return to_route('login');
    }
}
```

#### ❌ Bad: Non-RESTful Controller
```php
class AuthController extends Controller
{
    public function authenticate(LoginRequest $request): RedirectResponse
    {
        // Handle login logic
    }

    public function logout(): RedirectResponse
    {
        auth()->logout();
        session()->invalidate();
        return to_route('login');
    }
}
```

### Route Mapping
- Use resource routes where possible: `Route::resource('users', UserController::class)`
- For custom authentication flows, group routes logically:
  ```php
  Route::prefix('auth')->name('auth.')->group(function () {
      Route::post('/password', [PasswordController::class, 'store'])->name('password.store');
      Route::delete('/session', [LogoutController::class, 'destroy'])->name('session.destroy');
  });
  ```

### Testing Considerations
- RESTful method names make tests more predictable and maintainable
- Use route names in tests instead of hardcoded URLs
- Facade usage enables better mocking in unit tests

### Migration Strategy
When refactoring existing controllers:
1. Rename methods to RESTful conventions
2. Update route definitions
3. Update frontend form actions
4. Update all test references
5. Ensure proper HTTP verbs are used (POST for create, DELETE for destroy, etc.)
