# Architecture Overview & Quick Start Guide

## 🏗️ System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT SIDE (Browser)                    │
├─────────────────────────────────────────────────────────────────┤
│  Frontend Technologies:                                         │
│  • Tailwind CSS (Styling)                                      │
│  • Alpine.js (Interactive Components)                          │
│  • jQuery/Axios (AJAX Requests)                               │
│  • Bootstrap Components (UI Elements)                          │
└─────────────────────────────────────────────────────────────────┘
                                 │
                                 │ HTTP Requests
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                       WEB SERVER (PHP)                         │
├─────────────────────────────────────────────────────────────────┤
│                     Laravel Application                         │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │   Routes Layer  │  │  Middleware     │  │   Controllers   │ │
│  │                 │  │                 │  │                 │ │
│  │ • web.php       │  │ • Auth          │  │ • Traditional   │ │
│  │ • api.php       │  │ • Permissions   │  │ • Livewire     │ │
│  │ • auth.php      │  │ • CSRF          │  │ • API          │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │   Models        │  │   Views         │  │   Services      │ │
│  │                 │  │                 │  │                 │ │
│  │ • User          │  │ • Blade         │  │ • Auth          │ │
│  │ • Transaction   │  │ • Livewire     │  │ • Permissions   │ │
│  │ • Category      │  │ • Components    │  │ • Excel Export │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                                 │
                                 │ Database Queries
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                       DATABASE LAYER                           │
├─────────────────────────────────────────────────────────────────┤
│  Database: MySQL/PostgreSQL/SQLite                             │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │     Users       │  │  Transactions   │  │   Categories    │ │
│  │                 │  │                 │  │                 │ │
│  │ • UUID PK       │  │ • UUID PK       │  │ • UUID PK       │ │
│  │ • name          │  │ • amount        │  │ • name          │ │
│  │ • email         │  │ • description   │  │ • soft_deletes  │ │
│  │ • password      │  │ • type          │  │                 │ │
│  │ • soft_deletes  │  │ • category_id   │  │                 │ │
│  └─────────────────┘  │ • input_by      │  └─────────────────┘ │
│                       │ • date          │                       │
│  ┌─────────────────┐  └─────────────────┘  ┌─────────────────┐ │
│  │   Permissions   │                       │     Roles       │ │
│  │   (Spatie)      │                       │   (Spatie)      │ │
│  │                 │                       │                 │ │
│  │ • roles         │                       │ • permissions   │ │
│  │ • permissions   │                       │ • role_has_     │ │
│  │ • model_has_*   │                       │   permissions   │ │
│  └─────────────────┘                       └─────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

## 🚀 Quick Start Guide

### Prerequisites Checklist
- [ ] PHP 8.2 or higher
- [ ] Composer (PHP package manager)
- [ ] Node.js & npm (JavaScript package manager)
- [ ] MySQL/PostgreSQL/SQLite database
- [ ] Git

### Installation Steps

#### 1. Clone & Setup Project
```bash
# Clone the repository
git clone https://github.com/VinSyahputra/Laravel-Finance.git
cd Laravel-Finance

# Install PHP dependencies
composer install

# Install JavaScript dependencies  
npm install
```

#### 2. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_finance
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### 3. Database Setup
```bash
# Run migrations to create tables
php artisan migrate

# Seed database with initial data (optional)
php artisan db:seed

# Create symbolic link for storage
php artisan storage:link
```

#### 4. Asset Compilation
```bash
# Build assets for production
npm run build

# OR run development server with hot reload
npm run dev
```

#### 5. Start Development Server
```bash
# Option 1: Start all services with one command
composer run dev

# Option 2: Start individual services
php artisan serve              # Laravel server (http://localhost:8000)
npm run dev                    # Vite development server
php artisan queue:work         # Queue worker (background jobs)
php artisan pail              # Log viewer
```

### Default User Access
After seeding, you may have default users:
- **Admin**: admin@example.com / password
- **User**: user@example.com / password

## 📁 Project Structure Deep Dive

### Core Directories
```
Laravel-Finance/
├── app/
│   ├── Http/Controllers/     # Traditional & API controllers
│   ├── Livewire/            # Interactive UI components
│   ├── Models/              # Database models (User, Transaction, Category)
│   ├── Exports/             # Excel export classes
│   └── Providers/           # Service providers
│
├── database/
│   ├── migrations/          # Database schema definitions
│   ├── seeders/             # Test data generators
│   └── factories/           # Model factories for testing
│
├── resources/
│   ├── views/
│   │   ├── layouts/         # Page layouts (app.blade.php)
│   │   ├── livewire/        # Livewire component views
│   │   ├── components/      # Reusable Blade components
│   │   └── partials/        # Header, sidebar, footer
│   ├── css/                 # Stylesheets
│   └── js/                  # JavaScript files
│
├── routes/
│   ├── web.php              # Web routes (pages)
│   ├── api.php              # API endpoints
│   └── auth.php             # Authentication routes
│
├── config/                  # Configuration files
├── storage/                 # File storage, logs, cache
└── public/                  # Public assets, entry point
```

## 🔧 Key Configuration Files

### Environment Variables (.env)
```env
# Application
APP_NAME="Laravel Finance"
APP_ENV=local
APP_KEY=your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_finance
DB_USERNAME=root
DB_PASSWORD=

# Mail (for password reset, notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync
```

### Package Dependencies (composer.json)
```json
{
  "require": {
    "laravel/framework": "^11.31",     // Core framework
    "laravel/breeze": "^2.3",          // Authentication scaffolding
    "livewire/livewire": "^3.6",       // Interactive components
    "spatie/laravel-permission": "^6.16", // Role-based permissions
    "maatwebsite/excel": "^3.1",       // Excel import/export
    "laravel/sanctum": "^4.0"          // API authentication
  }
}
```

## 🎯 Key Features Walkthrough

### 1. Dashboard (`/dashboard`)
**What it does:**
- Shows financial overview with charts
- Displays recent transactions
- Provides monthly/yearly summaries

**Files involved:**
- Route: `routes/web.php` → `Dashboard::class`
- Component: `app/Livewire/Dashboard.php`
- View: `resources/views/livewire/dashboard.blade.php`
- AJAX: Calls `/api/analytics/recent-transactions`

### 2. Transaction Management (`/transactions`)
**What it does:**
- List all income/expense transactions
- Create, edit, delete transactions
- Filter by date, category, type

**Files involved:**
- Route: `routes/web.php` → `Transaction::class`
- Component: `app/Livewire/Transaction.php`
- View: `resources/views/livewire/transaction.blade.php`
- API: `app/Http/Controllers/Api/TransactionController.php`

### 3. Settings (`/settings/*`)
**What it does:**
- Manage categories (`/settings/categories`)
- Manage user roles (`/settings/roles`)
- Manage users (`/settings/users`)

**Files involved:**
- Routes: `routes/web.php` → Settings group
- Components: `app/Livewire/Setting/` (Category.php, Role.php, User.php)
- Views: `resources/views/livewire/setting/`
- Permission checks in each component

### 4. Authentication (`/login`, `/register`)
**What it does:**
- User login/registration
- Email verification
- Password reset

**Files involved:**
- Routes: `routes/auth.php` (Laravel Breeze)
- Controllers: `app/Http/Controllers/Auth/`
- Views: `resources/views/auth/`

## 🔐 Security Features

### 1. Role-Based Access Control
```php
// Check in Livewire components
if (!$this->user->hasPermissionTo('view setting category')) {
    abort(403, 'Unauthorized action.');
}

// Available permissions (examples):
// - 'view setting category'
// - 'view setting user'  
// - 'view setting role'
// - 'create transaction'
// - 'edit transaction'
```

### 2. CSRF Protection
- All forms automatically include CSRF tokens
- AJAX requests include CSRF token in headers
- Laravel validates all POST/PUT/DELETE requests

### 3. Input Validation
- Server-side validation for all form inputs
- Client-side validation with JavaScript
- SQL injection prevention through Eloquent ORM

### 4. Authentication
- Session-based authentication for web interface
- API token authentication (Sanctum) for AJAX requests
- Password hashing with bcrypt

## 📊 Database Relationships

### Entity Relationship Overview
```
Users (1) ──────┐
                │
                │ input_by
                │
                ▼
          Transactions (N)
                │
                │ category_id
                │
                ▼
          Categories (1)

Users (N) ◄──────► Roles (N)    [Many-to-Many via Spatie]
Roles (N) ◄──────► Permissions (N)
```

### Key Relationships Explained
1. **User → Transactions**: One user can create many transactions (`input_by` field)
2. **Category → Transactions**: One category can have many transactions (`category_id` field)
3. **User ↔ Roles**: Many-to-many relationship for role-based permissions
4. **Roles ↔ Permissions**: Many-to-many relationship for permission management

## 🛠️ Development Commands

### Useful Artisan Commands
```bash
# Database
php artisan migrate              # Run migrations
php artisan migrate:refresh      # Reset and re-run migrations
php artisan db:seed             # Seed database

# Caching (production optimization)
php artisan config:cache        # Cache configuration
php artisan route:cache         # Cache routes
php artisan view:cache          # Cache views

# Development
php artisan tinker              # Interactive PHP shell
php artisan serve               # Start development server
php artisan queue:work          # Process background jobs

# Code quality
./vendor/bin/pint               # Format PHP code
php artisan test                # Run tests
```

### Asset Commands
```bash
# Development (with hot reload)
npm run dev

# Production build (optimized)
npm run build

# Watch for changes
npm run watch
```

## 🚨 Troubleshooting

### Common Issues & Solutions

**1. Composer Dependencies Error**
```bash
# Clear composer cache and reinstall
composer clear-cache
composer install --no-cache
```

**2. Permission Denied Errors**
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**3. Database Connection Error**
- Check `.env` database credentials
- Ensure database exists
- Test connection: `php artisan migrate:status`

**4. Assets Not Loading**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild assets
npm run build
```

**5. Livewire Not Working**
- Check if `@livewireStyles` and `@livewireScripts` are included in layout
- Clear browser cache
- Check console for JavaScript errors

---

## 📚 Additional Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Livewire Documentation**: https://laravel-livewire.com/docs
- **Spatie Permission Package**: https://spatie.be/docs/laravel-permission
- **Tailwind CSS**: https://tailwindcss.com/docs

This Laravel Finance application is production-ready and follows industry best practices for security, performance, and maintainability.