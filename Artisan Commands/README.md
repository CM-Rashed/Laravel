<div align="center">

# ⚡ The Definitive Laravel Artisan CLI Handbook
### *From Junior Essentials to Principal Engineer Production Operations*

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x%20%7C%2012.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](#)
[![Artisan CLI](https://img.shields.io/badge/Artisan-CLI%20Mastery-red?style=for-the-badge&logo=gnu-bash&logoColor=white)](#)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](#)

<p align="center">
  <b>A comprehensive roadmap of Laravel Artisan commands:</b> scaffolding, database lifecycle, queue workers, custom commands, scheduler mechanics, optimization matrices, and maintenance controls.
</p>

---

</div>

## 📑 Table of Contents

- [1. CLI Foundations & Information Diagnostics](#1-cli-foundations--information-diagnostics)
- [2. Code Scaffolding (`make:*`) Master Matrix](#2-code-scaffolding-make-master-matrix)
- [3. Database Lifecycle & Migrations](#3-database-lifecycle--migrations)
- [4. Performance Tuning & Configuration Caching](#4-performance-tuning--configuration-caching)
- [5. Queues, Jobs & Background Processing](#5-queues-jobs--background-processing)
- [6. Task Scheduling & Cron Execution](#6-task-scheduling--cron-execution)
- [7. Interactive REPL: Laravel Tinker](#7-interactive-repl-laravel-tinker)
- [8. Application Health & Maintenance Modes](#8-application-health--maintenance-modes)
- [9. Building Custom Artisan Commands (0 to 100%)](#9-building-custom-artisan-commands-0-to-100)
- [10. Senior/Lead Production Operations Checklist](#10-seniorlead-production-operations-checklist)

---

## 1. CLI Foundations & Information Diagnostics

Artisan is the command-line interface included with Laravel. It provides helpful commands while building applications.

```bash
# Display core Laravel version, environment, and CLI help
php artisan --version
php artisan help <command_name>

# List all available registered commands (grouped by namespace)
php artisan list

# Inspect application environment and database connection state
php artisan env
php artisan about

# Interactive application health and architecture overview
php artisan about --only=drivers
```

---

## 2. Code Scaffolding (`make:*`) Master Matrix

Laravel offers dedicated generators to eliminate boilerplate and ensure framework-standard directory structures.

### 🛠️ Architecture & Backend Generators

| Target Layer | Command | Key Flags / Modifiers |
| :--- | :--- | :--- |
| **Model** | `php artisan make:model Post` | `-m` (migration), `-c` (controller), `-r` (resourceful), `-f` (factory), `-s` (seeder), `-a` (all) |
| **Controller** | `php artisan make:controller UserController` | `--resource` (CRUD), `--api` (API CRUD), `--invokable` (Single action) |
| **Middleware** | `php artisan make:middleware CheckRole` | Generates standard request pipeline filter |
| **Form Request** | `php artisan make:request StoreUserRequest` | Form validation and authorization rules |
| **Service Class** | `php artisan make:class Services/PaymentService` | Plain PHP architectural utility class |
| **Interface** | `php artisan make:interface Repositories/UserInterface` | Architectural contract definition |
| **Trait** | `php artisan make:trait Traits/HasSlug` | Reusable PHP trait |
| **Enum** | `php artisan make:enum Enums/OrderStatus` | PHP 8.1+ backed enum scaffold |

### 🚀 All-in-One Generator Command

```bash
# Generates Model, Migration, Controller (Resource), Factory, Seeder, and Form Requests at once:
php artisan make:model Product -a --requests
```

### 📦 Async, Event & Notification Scaffolding

```bash
# Jobs & Queues
php artisan make:job ProcessPayment --sync

# Events & Listeners
php artisan make:event OrderShipped
php artisan make:listener SendShipmentNotification --event=OrderShipped

# Notifications & Mailables
php artisan make:mail OrderReceipt --markdown=emails.orders.receipt
php artisan make:notification InvoicePaid --markdown=notifications.invoice-paid

# Policy & Authorization Gates
php artisan make:policy PostPolicy --model=Post
```

---

## 3. Database Lifecycle & Migrations

Manage database state, schema mutations, and seeding across local and cloud environments.

```bash
# Run pending migrations
php artisan migrate

# Force execution in production environments (bypasses confirmation safety check)
php artisan migrate --force

# Rollback operations
php artisan migrate:rollback          # Rollback the last migration batch
php artisan migrate:rollback --step=3 # Rollback the last 3 migration files
php artisan migrate:reset             # Rollback all database migrations

# Destructive rebuilds (Local development only!)
php artisan migrate:fresh             # Drops all tables and runs all migrations from scratch
php artisan migrate:fresh --seed      # Drops tables, runs migrations, and seeds test data
php artisan migrate:refresh           # Rollbacks all and re-runs migrations

# Inspection and Status
php artisan migrate:status

# Seeders & Factories
php artisan db:seed
php artisan db:seed --class=UserSeeder
```

---

## 4. Performance Tuning & Configuration Caching

In production environments, Laravel needs compiled configuration, route, and view manifests to achieve sub-millisecond dispatch times.

```
┌─────────────────────────────────────────────────────────────┐
│                    PRODUCTION CACHE MATRIX                  │
├──────────────────────────┬──────────────────────────────────┤
│ Command                  │ Purpose                          │
├──────────────────────────┼──────────────────────────────────┤
│ php artisan config:cache │ Compiles all config files to 1   │
│ php artisan route:cache  │ Serializes all route patterns    │
│ php artisan view:cache   │ Precompiles Blade templates      │
│ php artisan event:cache  │ Discovers events & listeners     │
└──────────────────────────┴──────────────────────────────────┘
```

```bash
# --- PRODUCTION OPTIMIZATION (Run during CI/CD Deployments) ---
php artisan optimize             # Caches config and routes in a single command
php artisan view:cache           # Pre-compiles Blade views
php artisan event:cache          # Caches event listener mapping

# --- LOCAL DEVELOPMENT FLUSH (Run when editing .env or config files) ---
php artisan optimize:clear       # Clears all cached artifacts (config, routes, views, events, cache)
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## 5. Queues, Jobs & Background Processing

Manage asynchronous execution workers, track delayed jobs, and monitor failures.

```bash
# Start queue worker processing
php artisan queue:work

# Production Queue Worker parameters
php artisan queue:work database --queue=high,default --tries=3 --timeout=90 --sleep=3

# Run worker in listen mode (reloads codebase per job - ideal for local dev)
php artisan queue:listen

# Gracefully restart running queue workers (Call after deploying code updates)
php artisan queue:restart

# Failed Job Management
php artisan queue:failed              # List all failed jobs
php artisan queue:retry all           # Retry all failed jobs
php artisan queue:retry <job-id>      # Retry specific job by UUID/ID
php artisan queue:forget <job-id>     # Delete a single failed job
php artisan queue:flush               # Flush all failed jobs from storage
```

---

## 6. Task Scheduling & Cron Execution

Laravel replaces standard crontab spam with a single system entry point configured in PHP.

### 🕒 System Cron Configuration

Add this single cron record to your production server:

```cron
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 🛠️ Schedule Inspection & Testing

```bash
# Inspect all registered scheduled commands and their due timing
php artisan schedule:list

# Test and run due scheduled tasks manually in the console
php artisan schedule:run

# Run a dedicated background worker for the scheduler locally (No crontab needed!)
php artisan schedule:work

# Clear scheduler cache mutexes
php artisan schedule:clear-cache
```

---

## 7. Interactive REPL: Laravel Tinker

Tinker is a REPL (Read-Eval-Print Loop) powered by PsySH that executes arbitrary PHP and interacts with Eloquent directly inside your application lifecycle.

```bash
# Launch interactive REPL session
php artisan tinker
```

### 💡 Common Tinker Operations

```php
// Query Database Models
$user = App\Models\User::first();
$user->update(['role' => 'admin']);

// Dispatch Jobs or Fire Events
App\Jobs\ProcessPayment::dispatchSync($user);
event(new App\Events\UserRegistered($user));

// Inspect Configuration / Container Bindings
config('services.stripe.secret');
app()->make(App\Services\PaymentService::class);
```

---

## 8. Application Health & Maintenance Modes

Safely take your platform offline for updates or inspect system health.

```bash
# Put application into Maintenance Mode (503 Service Unavailable)
php artisan down

# Maintenance Mode with Bypass Secret (Allows developers to test using cookie)
php artisan down --secret="my-super-secret-bypass-token"
# Access via: https://yourdomain.com/my-super-secret-bypass-token

# Maintenance Mode with custom retry header and redirect
php artisan down --redirect="/" --retry=60

# Bring the application back online
php artisan up

# Storage Linking (Creates symlink from public/storage to storage/app/public)
php artisan storage:link
```

---

## 9. Building Custom Artisan Commands (0 to 100%)

Create your own automation scripts and operational commands.

### Step 1: Generate the Command Scaffold

```bash
php artisan make:command SendWeeklyReports --command=reports:send
```

### Step 2: Write Command Logic

```php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;

class SendWeeklyReports extends Command
{
    /**
     * The name and signature of the console command.
     * Supports: {argument}, {argument?}, {--option}, {--flag}
     */
    protected $signature = 'reports:send 
                            {department : The target department name} 
                            {--queue : Queue the reports instead of synchronous dispatch}';

    /**
     * The console command description.
     */
    protected $description = 'Dispatches weekly analytics reports to staff members';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $department = $this->argument('department');
        $shouldQueue = $this->option('queue');

        $this->info("Preparing reports for: {$department}");

        $users = User::where('department', $department)->get();

        if ($users->isEmpty()) {
            $this->warn('No users found matching this department.');
            return self::FAILURE;
        }

        // Beautiful visual progress bar
        $this->output->progressStart($users->count());

        foreach ($users as $user) {
            // Process reporting logic
            usleep(50000); 
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info('All reports generated and dispatched successfully!');

        return self::SUCCESS;
    }
}
```

---

## 10. Senior/Lead Production Operations Checklist

Use this quick-reference sequence when deploying updates to zero-downtime production environments:

```bash
# --- DEPLOYMENT AUTOMATION PIPELINE ---

# 1. Enter Maintenance Mode (Optional for non-zero downtime setups)
# php artisan down --render="errors::503"

# 2. Update code and composer dependencies
# git pull origin main && composer install --no-dev --optimize-autoloader

# 3. Database Migration (Always force in production)
php artisan migrate --force

# 4. Flush stale cache layers
php artisan optimize:clear

# 5. Build high-speed production caches
php artisan optimize
php artisan view:cache
php artisan event:cache

# 6. Restart Queue Workers to load new code state
php artisan queue:restart

# 7. Bring Application Online
# php artisan up
```

---

<div align="center">
  <b>Built with ❤️ for Laravel Engineers</b> • <i>Save this reference directly to your GitHub Wiki or Notes!</i>
</div>
