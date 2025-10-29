
<h1 align="center">
  <br>
  <a href="https://github.com/VinSyahputra/Laravel-Finance"><img src="https://raw.githubusercontent.com/VinSyahputra/Laravel-Finance/main/public/assets/images/logos/new-logo2.png" alt="Laravel Finance" width="200"></a>
  <br>
  Laravel Finance
  <br>
</h1>

<h4 align="center">A comprehensive financial management web application built with <a href="https://laravel.com" target="_blank">Laravel</a> and <a href="https://livewire.laravel.com" target="_blank">Livewire</a>.</h4>

<p align="center">
  <a href="https://laravel.com">
    <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel" alt="Laravel Version">
  </a>
  <a href="https://livewire.laravel.com">
    <img src="https://img.shields.io/badge/Livewire-3.x-4E56A6?style=flat&logo=livewire" alt="Livewire Version">
  </a>
  <a href="https://www.php.net">
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php" alt="PHP Version">
  </a>
  <a href="https://tailwindcss.com">
    <img src="https://img.shields.io/badge/Tailwind-3.x-06B6D4?style=flat&logo=tailwindcss" alt="Tailwind CSS">
  </a>
</p>

<p align="center">
  <a href="#key-features">Key Features</a> •
  <a href="#installation">Installation</a> •
  <a href="#usage">Usage</a> •
  <a href="#tech-stack">Tech Stack</a> •
  <a href="#contributing">Contributing</a> •
  <a href="#license">License</a>
</p>

## Key Features

### 💰 Financial Management
* **Transaction Tracking** - Record and categorize income and expenses with detailed descriptions
* **Category Management** - Organize transactions with customizable categories
* **Dashboard Analytics** - Get real-time insights into your financial data with interactive charts
* **Excel Integration** - Import and export financial data with Excel files

### 👥 User Management
* **Authentication System** - Secure login and registration with Laravel Breeze
* **Role-Based Access Control** - Manage user permissions with different roles
* **User Administration** - Admin panel for managing users and their access levels
* **Profile Management** - Users can update their personal information

### 🎨 Modern Interface
* **Responsive Design** - Works seamlessly on desktop, tablet, and mobile devices
* **Real-time Updates** - Live data updates using Livewire components
* **Clean UI/UX** - Modern interface built with Tailwind CSS
* **Interactive Components** - Dynamic forms and data tables for better user experience

### 🔧 Technical Features
* **Database Flexibility** - Supports SQLite and MySQL databases
* **Soft Deletes** - Safe data deletion with recovery options
* **UUID Support** - Secure unique identifiers for all records
* **Queue System** - Background job processing for better performance
* **API Ready** - Built with Laravel Sanctum for potential API integration

## Installation

### Prerequisites

Before you begin, ensure you have the following installed on your system:

- [PHP](https://www.php.net/downloads.php) (8.2 or higher)
- [Composer](https://getcomposer.org/) (PHP dependency manager)
- [Node.js](https://nodejs.org/) (18.x or higher)
- [NPM](https://www.npmjs.com/) (comes with Node.js)
- Database (SQLite, MySQL, or PostgreSQL)

### Quick Start

1. **Clone the repository**
   ```bash
   git clone https://github.com/VinSyahputra/Laravel-Finance.git
   cd Laravel-Finance
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   # Copy the environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   ```

5. **Database Setup**
   ```bash
   # Create SQLite database (default)
   touch database/database.sqlite
   
   # Or configure MySQL/PostgreSQL in .env file
   # DB_CONNECTION=mysql
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=laravel_finance
   # DB_USERNAME=your_username
   # DB_PASSWORD=your_password
   ```

6. **Run Database Migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed the Database (Optional)**
   ```bash
   php artisan db:seed
   ```

8. **Build Assets**
   ```bash
   npm run build
   ```

9. **Start the Development Server**
   ```bash
   # Option 1: Use Laravel's built-in server
   php artisan serve
   
   # Option 2: Use the dev script (includes queue, logs, and vite)
   composer run dev
   ```

10. **Access the Application**
    Open your browser and navigate to `http://localhost:8000`

### Production Deployment

For production deployment, please refer to the [Laravel Deployment Documentation](https://laravel.com/docs/deployment).


## Usage

### Getting Started

1. **Register/Login**: Create a new account or login with existing credentials
2. **Dashboard**: View your financial overview with charts and summaries
3. **Add Transactions**: Record your income and expenses with categories
4. **Manage Categories**: Create and organize transaction categories
5. **Export Data**: Download your financial data in Excel format

### User Roles

- **Admin**: Full access to all features including user management
- **User**: Access to personal transactions and dashboard

### Key Workflows

#### Recording Transactions
1. Navigate to the Transactions page
2. Click "Add Transaction"
3. Fill in the amount, description, category, and type (income/expense)
4. Save the transaction

#### Managing Categories
1. Go to Settings > Categories
2. Add new categories or edit existing ones
3. Assign categories to organize your transactions

#### Exporting Data
1. From the Dashboard or Transactions page
2. Use the export function to download Excel files
3. Choose date ranges and filters as needed

## Tech Stack

### Backend
- **Laravel 11** - PHP framework for web applications
- **Livewire 3** - Full-stack framework for Laravel
- **Laravel Breeze** - Authentication scaffolding
- **Laravel Sanctum** - API authentication
- **Spatie Laravel Permission** - Role and permission management

### Frontend
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Blade Templates** - Laravel's templating engine

### Database & Storage
- **SQLite/MySQL** - Database options
- **Eloquent ORM** - Database abstraction layer

### Development Tools
- **Vite** - Frontend build tool
- **Laravel Pint** - Code style fixer
- **PHPUnit** - Testing framework

### Additional Packages
- **Maatwebsite Excel** - Excel import/export functionality
- **Laravel Pail** - Real-time log monitoring

## Development

### Running in Development Mode

```bash
# Start all development services (recommended)
composer run dev

# Or start services individually:
php artisan serve          # Web server
php artisan queue:work      # Queue worker
php artisan pail           # Log monitoring
npm run dev                # Vite development server
```

### Code Quality

```bash
# Fix code style with Laravel Pint
./vendor/bin/pint

# Run tests
php artisan test
```

### Database Management

```bash
# Create a new migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Refresh migrations (drop all tables and re-run)
php artisan migrate:refresh --seed
```

### Creating Components

```bash
# Create a new Livewire component
php artisan make:livewire ComponentName

# Create a new model
php artisan make:model ModelName -m

# Create a new controller
php artisan make:controller ControllerName
```

## Contributing

We welcome contributions to Laravel Finance! Here's how you can help:

### How to Contribute

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Make your changes**
4. **Run tests and ensure code quality**
   ```bash
   php artisan test
   ./vendor/bin/pint
   ```
5. **Commit your changes**
   ```bash
   git commit -m "Add your descriptive commit message"
   ```
6. **Push to your branch**
   ```bash
   git push origin feature/your-feature-name
   ```
7. **Create a Pull Request**

### Development Guidelines

- Follow Laravel coding standards
- Write tests for new features
- Update documentation when necessary
- Use meaningful commit messages
- Ensure backward compatibility

### Reporting Issues

If you find a bug or have a feature request:

1. Check existing issues first
2. Create a new issue with detailed information
3. Include steps to reproduce (for bugs)
4. Provide system information and versions

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credits

Laravel Finance is built with these amazing open source technologies:

- **[Laravel](https://laravel.com/)** - The PHP Framework for Web Artisans
- **[Livewire](https://livewire.laravel.com/)** - A full-stack framework for Laravel
- **[Tailwind CSS](https://tailwindcss.com/)** - A utility-first CSS framework
- **[Alpine.js](https://alpinejs.dev/)** - A rugged, minimal framework for composing JavaScript behavior
- **[Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/)** - Associate users with permissions and roles
- **[Maatwebsite Excel](https://laravel-excel.com/)** - Supercharged Excel exports and imports

## Support

If you find this project helpful, please consider:

- ⭐ Starring the repository
- 🐛 Reporting bugs and issues
- 💡 Suggesting new features
- 🤝 Contributing to the codebase

---

<p align="center">
  Made with ❤️ by <a href="https://github.com/VinSyahputra">VinSyahputra</a>
</p>

