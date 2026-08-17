<div align="center">

# 🚀 The Definitive Laravel Routing Handbook
### *A Production-Ready, Visual Reference & Architecture Guide*

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x%20%7C%2012.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](#)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](#)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](#)

<p align="center">
  <b>Master every routing mechanism in Laravel:</b> from fundamental verbs to scoped model binding, tenant subdomains, cryptographic signatures, and production performance tuning.
</p>

---

</div>

## 📑 Table of Contents

- [1. HTTP Verbs & Direct Routing](#1-http-verbs--direct-routing)
- [2. Route Parameters & Validation](#2-route-parameters--validation)
- [3. Named Routes & Link Generation](#3-named-routes--link-generation)
- [4. Route Groups, Prefixes & Namespaces](#4-route-groups-prefixes--namespaces)
- [5. Controllers & Single Action Invocations](#5-controllers--single-action-invocations)
- [6. Resourceful RESTful Routing](#6-resourceful-restful-routing)
- [7. Route Model Binding Mastery](#7-route-model-binding-mastery)
- [8. Security, Rate Limiting & Signed URLs](#8-security-rate-limiting--signed-urls)
- [9. Subdomains, Fallbacks & Redirects](#9-subdomains-fallbacks--redirects)
- [10. Performance Optimization & Caching](#10-performance-optimization--caching)

---

## 1. HTTP Verbs & Direct Routing

Laravel's routing engine handles incoming HTTP requests by matching URIs and HTTP methods.

### 💡 Core Verb Registration

```php
use Illuminate\Support\Facades\Route;

// Standard GET Route returning a View or String
Route::get('/welcome', function () {
    return view('welcome');
});

// Full RESTful HTTP Verb Coverage
Route::get('/users', function () { return 'GET: Read resource list'; });
Route::post('/users', function () { return 'POST: Create new resource'; });
Route::put('/users/{id}', function ($id) { return 'PUT: Complete resource replace'; });
Route::patch('/users/{id}', function ($id) { return 'PATCH: Partial resource update'; });
Route::delete('/users/{id}', function ($id) { return 'DELETE: Remove resource'; });
Route::options('/users', function () { return response('', 204); });
```

### 🔀 Multi-Verb Matching

```php
// Match specific HTTP verbs
Route::match(['get', 'post'], '/feedback', function () {
    return 'Handles both GET submission form and POST submission payload';
});

// Accept ANY incoming HTTP verb
Route::any('/webhook/gateway', function () {
    return 'Handles incoming webhooks regardless of HTTP method';
});
```

> **Pro Tip:** For purely static views with no dynamic logic, use `Route::view()` to skip controller initialization overhead:
> ```php
> Route::view('/about', 'pages.about', ['appName' => 'Enterprise App']);
> ```

---

## 2. Route Parameters & Validation

Capture segments of the URI within your route by defining parameter variables.

```
URI: /users/{id}/posts/{slug}
            └── Parameter 1    └── Parameter 2
```

### 🏷️ Basic & Optional Parameters

```php
// Required Parameter
Route::get('/user/{id}', function (string $id) {
    return "Fetching User: {$id}";
});

// Optional Parameter (Must supply default value in Closure/Action)
Route::get('/profile/{username?}', function (string $username = 'Guest') {
    return "Active Profile: {$username}";
});
```

### 🛡️ Regex Constraints & Fluent Helpers

| Method | Pattern Enforced | Example Value |
| :--- | :--- | :--- |
| `->whereNumber('id')` | `[0-9]+` | `1042` |
| `->whereAlpha('name')` | `[a-zA-Z]+` | `alex` |
| `->whereAlphaNumeric('code')` | `[a-zA-Z0-9]+` | `ord99B` |
| `->whereUuid('uuid')` | `[0-9a-f]{8}-[0-9a-f]{4}...` | `550e8400-e29b-41d4-a716-446655440000` |
| `->whereUlid('ulid')` | `[0-9A-HJKMNP-TV-Z]{26}` | `01ARZ3NDEKTSV4RRFFQ69G5FAV` |
| `->whereIn('role', ['admin', 'user'])` | Array whitelist | `admin` |

```php
// Chained Fluent Regex Constraints
Route::get('/orders/{id}/{status}', function ($id, $status) {
    return "Order #{$id} is {$status}";
})
->whereNumber('id')
->whereIn('status', ['pending', 'shipped', 'delivered']);
```

---

## 3. Named Routes & Link Generation

Named routes eliminate hardcoded URIs across Blade templates, controllers, and redirects.

```php
// Registering a named route
Route::get('/billing/invoice/{id}', [InvoiceController::class, 'show'])
    ->name('invoices.show');
```

```php
// Generating a raw URL
$url = route('invoices.show', ['id' => 952]); 
// Output: "https://yourdomain.com/billing/invoice/952"

// Generating URLs with extra Query Parameters
$url = route('invoices.show', ['id' => 952, 'download' => 'pdf']);
// Output: "https://yourdomain.com/billing/invoice/952?download=pdf"

// Redirecting directly to a named route
return redirect()->route('invoices.show', ['id' => 952]);
```

---

## 4. Route Groups, Prefixes & Namespaces

Group shared attributes such as middleware, path prefixes, and route name prefixes together to maintain DRY (*Don't Repeat Yourself*) architecture.

```php
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        
        // Full URI: /admin/dashboard | Name: admin.dashboard
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Full URI: /admin/settings | Name: admin.settings
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings');
        
    });
```

---

## 5. Controllers & Single Action Invocations

### Standard Actions vs Single-Action Controllers

```php
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProvisionServerController;

// 1. Standard Controller Method
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// 2. Single-Action Controller (Invokes __invoke() automatically)
Route::post('/server/provision', ProvisionServerController::class)->name('server.provision');

// 3. Controller Group (DRY definition for multiple actions of the same controller)
Route::controller(OrderController::class)->group(function () {
    Route::get('/orders', 'index')->name('orders.index');
    Route::get('/orders/{id}', 'show')->name('orders.show');
    Route::post('/orders', 'store')->name('orders.store');
    Route::delete('/orders/{id}', 'destroy')->name('orders.destroy');
});
```

---

## 6. Resourceful RESTful Routing

A single `Route::resource()` line creates all 7 conventional CRUD routes.

```php
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

// 1. Full 7-route CRUD Resource
Route::resource('posts', PostController::class);

// 2. API Resource (Omits create/edit HTML form views)
Route::apiResource('comments', CommentController::class);

// 3. Nested Resource
Route::resource('posts.comments', CommentController::class);

// 4. Scoped/Partial Resource
Route::resource('photos', PostController::class)->only(['index', 'show']);
Route::resource('media', PostController::class)->except(['destroy']);
```

### 📋 Resource Controller Endpoint Map

<table>
  <thead>
    <tr>
      <th>HTTP Verb</th>
      <th>URI Path</th>
      <th>Controller Action</th>
      <th>Route Name</th>
      <th>Intended Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><code>GET</code></td>
      <td><code>/posts</code></td>
      <td><code>index()</code></td>
      <td><code>posts.index</code></td>
      <td>Display index / listing of items</td>
    </tr>
    <tr>
      <td><code>GET</code></td>
      <td><code>/posts/create</code></td>
      <td><code>create()</code></td>
      <td><code>posts.create</code></td>
      <td>Render HTML form to create an item</td>
    </tr>
    <tr>
      <td><code>POST</code></td>
      <td><code>/posts</code></td>
      <td><code>store()</code></td>
      <td><code>posts.store</code></td>
      <td>Persist newly created item in database</td>
    </tr>
    <tr>
      <td><code>GET</code></td>
      <td><code>/posts/{post}</code></td>
      <td><code>show()</code></td>
      <td><code>posts.show</code></td>
      <td>Display a specific item instance</td>
    </tr>
    <tr>
      <td><code>GET</code></td>
      <td><code>/posts/{post}/edit</code></td>
      <td><code>edit()</code></td>
      <td><code>posts.edit</code></td>
      <td>Render HTML form to edit existing item</td>
    </tr>
    <tr>
      <td><code>PUT/PATCH</code></td>
      <td><code>/posts/{post}</code></td>
      <td><code>update()</code></td>
      <td><code>posts.update</code></td>
      <td>Save updated record changes to database</td>
    </tr>
    <tr>
      <td><code>DELETE</code></td>
      <td><code>/posts/{post}</code></td>
      <td><code>destroy()</code></td>
      <td><code>posts.destroy</code></td>
      <td>Remove record permanently or soft-delete</td>
    </tr>
  </tbody>
</table>

---

## 7. Route Model Binding Mastery

Laravel automatically resolves Eloquent models injected into route Closures or controller actions.

```php
use App\Models\Post;
use App\Models\Comment;

// 1. Standard Implicit Binding (Resolves by primary ID; returns 404 if missing)
Route::get('/posts/{post}', function (Post $post) {
    return view('posts.show', ['post' => $post]);
});

// 2. Custom Key Resolution (Binds via 'slug' column instead of ID)
Route::get('/posts/read/{post:slug}', function (Post $post) {
    return view('posts.view', ['post' => $post]);
});

// 3. Scoped Child Binding (Ensures comment strictly belongs to this exact post)
Route::get('/posts/{post}/comments/{comment:id}', function (Post $post, Comment $comment) {
    return response()->json($comment);
})->scopeBindings();

// 4. Soft-Deleted / Trashed Resolution
Route::get('/posts/trashed/{post}', function (Post $post) {
    return view('posts.archived', ['post' => $post]);
})->withTrashed();
```

---

## 8. Security, Rate Limiting & Signed URLs

### ⏱️ Rate Limiting (Throttling)

```php
// Limit incoming traffic to 60 requests per 1 minute window per IP/User
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/api/search', [SearchController::class, 'query']);
});
```

### 🔏 Tamper-Proof Cryptographic Signed URLs

Signed URLs prevent users from altering URL query strings or parameter IDs.

```php
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

// 1. Protect Route via 'signed' middleware
Route::get('/unsubscribe/{user}', function (Request $request, User $user) {
    if (! $request->hasValidSignature()) {
        abort(403, 'Invalid or expired signature.');
    }
    return "User {$user->email} has been unsubscribed.";
})->name('unsubscribe')->middleware('signed');

// 2. Generating a Temporary Signed URL (e.g. inside an email notification)
$signedUrl = URL::temporarySignedRoute(
    'unsubscribe',
    now()->addHours(24),
    ['user' => $user->id]
);
```

---

## 9. Subdomains, Fallbacks & Redirects

```php
// 1. Multi-Tenant Subdomain Routing
Route::domain('{tenant}.enterprise.com')->group(function () {
    Route::get('/portal', function (string $tenant) {
        return "Loaded workspace for organization: {$tenant}";
    });
});

// 2. Direct Redirects
Route::redirect('/old-route', '/new-route');               // 302 Found
Route::permanentRedirect('/legacy-docs', '/v2/docs');      // 301 Moved Permanently

// 3. Custom Fallback Handler (MUST ALWAYS sit at the bottom of web.php)
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
```

---

## 10. Performance Optimization & Caching

> ⚠️ **Production Imperative:** In production environments, always compile routes into a serialized cache to avoid parsing route files on every request.

```bash
# 1. Compile and cache all registered routes
php artisan route:cache

# 2. Clear cached routes (Execute when modifying routes in local development)
php artisan route:clear

# 3. Inspect application routing table
php artisan route:list

# 4. Filter routes by prefix, name, or controller
php artisan route:list --path=api
php artisan route:list --name=admin.
```

---

<div align="center">
  <b>Built with ❤️ for Laravel Developers</b> • <i>Save this guide to your GitHub notes!</i>
</div>