<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SingleActionController;

/*
|--------------------------------------------------------------------------
| The Definitive Laravel Routing Blueprint
|--------------------------------------------------------------------------
|
| This file contains every core, intermediate, and advanced routing feature
| available in Laravel. Each section is structured with clean executable code
| and technical documentation.
|
*/

// =========================================================================
// 1. BASIC ROUTING & VIEWS
// =========================================================================

// Standard Closure Route
Route::get('/', function () {
    return view('welcome');
});

// View Route Shortcut (Optimized: skips controller/closure overhead)
Route::view('/about', 'pages.about', [
    'appName' => 'Laravel App',
    'version' => '11.x',
]);


// =========================================================================
// 2. HTTP VERBS & MULTI-VERB ROUTING
// =========================================================================

Route::get('/items', function () { return 'GET (Read)'; });
Route::post('/items', function () { return 'POST (Create)'; });
Route::put('/items/{id}', function ($id) { return 'PUT (Full Replace)'; });
Route::patch('/items/{id}', function ($id) { return 'PATCH (Partial Update)'; });
Route::delete('/items/{id}', function ($id) { return 'DELETE (Destroy)'; });
Route::options('/items', function () { return response('', 204); });

// Match specific multiple HTTP verbs
Route::match(['get', 'post'], '/feedback', function () {
    return 'Handles both GET and POST requests';
});

// Match any HTTP verb
Route::any('/webhook/receiver', function () {
    return 'Handles all standard HTTP verbs';
});


// =========================================================================
// 3. ROUTE PARAMETERS & CONSTRAINTS
// =========================================================================

// Required Parameter
Route::get('/user/{id}', function (string $id) {
    return "User ID: " . $id;
});

// Optional Parameter with Default Fallback
Route::get('/user/{name?}', function (string $name = 'Guest') {
    return "Hello, " . $name;
});

// Custom Regex Constraints using ->where()
Route::get('/post/{id}/{slug}', function ($id, $slug) {
    return "Post ID: $id, Slug: $slug";
})->where([
    'id'   => '[0-9]+',
    'slug' => '[a-z0-9-]+',
]);

// Dedicated Helper Constraint Methods
Route::get('/account/number/{id}', function ($id) { return $id; })->whereNumber('id');
Route::get('/account/handle/{name}', function ($name) { return $name; })->whereAlpha('name');
Route::get('/account/code/{code}', function ($code) { return $code; })->whereAlphaNumeric('code');
Route::get('/account/uuid/{uuid}', function ($uuid) { return $uuid; })->whereUuid('uuid');
Route::get('/account/ulid/{ulid}', function ($ulid) { return $ulid; })->whereUlid('ulid');
Route::get('/category/{type}', function ($type) { return $type; })->whereIn('type', ['tech', 'travel', 'food']);


// =========================================================================
// 4. NAMED ROUTES & URL GENERATION
// =========================================================================

Route::get('/user/profile', function () {
    return 'Profile Page';
})->name('profile.show');

// Generating URLs: $url = route('profile.show');
// Redirecting:     return redirect()->route('profile.show');


// =========================================================================
// 5. ROUTE CONTROLLERS & CONTROLLER GROUPS
// =========================================================================

// Standard Action Routing
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Single-Action Controller (invokes __invoke() magic method)
Route::post('/server/provision', SingleActionController::class)->name('server.provision');

// Controller Group (avoids repeating controller name for related actions)
Route::controller(OrderController::class)->group(function () {
    Route::get('/orders', 'index')->name('orders.index');
    Route::get('/orders/{id}', 'show')->name('orders.show');
    Route::post('/orders', 'store')->name('orders.store');
    Route::delete('/orders/{id}', 'destroy')->name('orders.destroy');
});


// =========================================================================
// 6. RESOURCE & API RESOURCE ROUTING
// =========================================================================

// Standard Resource Route (Creates 7 standard CRUD endpoints)
Route::resource('posts', PostController::class);

// API Resource Route (Omits HTML create and edit form views)
Route::apiResource('comments', CommentController::class);

// Partial Resource (Whitelisting or Blacklisting actions)
Route::resource('photos', UserController::class)->only(['index', 'show']);
Route::resource('videos', UserController::class)->except(['create', 'edit']);

// Nested Resource Routes
Route::resource('posts.comments', CommentController::class);


// =========================================================================
// 7. ROUTE MODEL BINDING (IMPLICIT, EXPLICIT, SCOPED, TRASHED)
// =========================================================================

// Standard Implicit Binding (Resolves Post by primary key ID or returns 404)
Route::get('/articles/{post}', function (Post $post) {
    return $post->title;
});

// Custom Key Binding (Resolves Post model by 'slug' column instead of ID)
Route::get('/articles/slug/{post:slug}', function (Post $post) {
    return $post->body;
});

// Scoped Binding (Ensures nested Comment belongs to the parent Post)
Route::get('/posts/{post}/comments/{comment:id}', function (Post $post, Comment $comment) {
    return $comment;
})->scopeBindings();

// Soft-Deleted / Trashed Binding (Resolves models even if soft-deleted)
Route::get('/trashed-posts/{post}', function (Post $post) {
    return $post->title;
})->withTrashed();


// =========================================================================
// 8. ROUTE GROUPS, PREFIXES, SUBDOMAINS & NAMESPACES
// =========================================================================

// Nested Fluent Group (Prefix, Middleware, and Name Prefix combined)
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return 'Admin Dashboard';
        })->name('dashboard'); // Generates route name: 'admin.dashboard'

        Route::get('/settings', function () {
            return 'Admin Settings';
        })->name('settings');   // Generates route name: 'admin.settings'
    });

// Subdomain Routing (Multi-tenant architectures)
Route::domain('{account}.myapp.com')->group(function () {
    Route::get('/workspace', function ($account) {
        return "Tenant workspace: " . $account;
    });
});

// Route Localization Prefix
Route::prefix('{locale}')->whereIn('locale', ['en', 'es', 'bn'])->group(function () {
    Route::get('/welcome', function ($locale) {
        return "Language set to: " . $locale;
    });
});


// =========================================================================
// 9. SECURITY: RATE LIMITING & SIGNED URLS
// =========================================================================

// Rate Limiting / Throttle Middleware (Allows 10 requests per 1 minute per IP)
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/api/heavy-task', function () {
        return response()->json(['status' => 'success']);
    });
});

// Signed Route (Tamper-proof URL verification with cryptographic signature)
Route::get('/unsubscribe/{user}', function (User $user) {
    return 'You have been unsubscribed from the newsletter.';
})->name('unsubscribe')->middleware('signed');

/*
 * Generating a Signed URL:
 * $signedUrl = URL::temporarySignedRoute(
 *     'unsubscribe',
 *     now()->addMinutes(30),
 *     ['user' => $user->id]
 * );
 */


// =========================================================================
// 10. REDIRECTS & SHORTCUTS
// =========================================================================

// 302 Found (Temporary Redirect)
Route::redirect('/old-page', '/new-page');

// 301 Moved Permanently (Permanent Redirect)
Route::permanentRedirect('/legacy-endpoint', '/modern-endpoint');


// =========================================================================
// 11. FALLBACK ROUTE (CUSTOM 404 HANDLER)
// =========================================================================

// ALWAYS place at the very bottom of web.php; catches unhandled URL requests
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});


// =========================================================================
// 12. GLOBAL ROUTE CONFIGURATION & PERFORMANCE CACHING
// =========================================================================

/*
 * GLOBAL ROUTE PATTERNS (Define in AppServiceProvider::boot()):
 *
 *   public function boot(): void
 *   {
 *       Route::pattern('id', '[0-9]+'); // Enforces numeric ID constraint globally
 *   }
 *
 * PRODUCTION ROUTE CACHING COMMANDS:
 *
 * 1. Compile and Cache Routes (Run during deployment):
 *    php artisan route:cache
 *
 * 2. Clear Cached Routes (Run during local development):
 *    php artisan route:clear
 *
 * 3. Inspect All Registered Routes:
 *    php artisan route:list
 */