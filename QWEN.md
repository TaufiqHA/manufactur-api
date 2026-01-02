# Manufacturing API System - QWEN Context

## Project Overview

This is a Laravel-based API system designed for manufacturing management called "API SYSTEM MANUFACTUR". It provides a comprehensive backend for managing various aspects of manufacturing operations including projects, materials, RFQs (Request for Quotations), purchase orders, receiving goods, and production tasks.

The system is built with Laravel 12 and uses Laravel Sanctum for API authentication. It follows RESTful API principles and is designed to support manufacturing workflow management with features for project tracking, procurement, inventory, and production planning.

### Key Technologies
- **Framework**: Laravel 12 (PHP 8.2+)
- **Authentication**: Laravel Sanctum (token-based)
- **Database**: SQLite (default), with support for other databases
- **Frontend Build Tool**: Vite with Tailwind CSS
- **HTTP Client**: Axios
- **Development Tools**: Laravel Pint (code formatting), Laravel Sail

### Architecture
- **API-First**: Built as a headless API backend
- **Model-View-Controller (MVC)**: Standard Laravel architecture
- **RESTful Endpoints**: Consistent API design patterns
- **Database Migrations**: Version-controlled schema management

## Project Structure

```
backend/
├── app/                    # Application source code
│   ├── Enums/             # PHP enumerations
│   ├── Http/              # Controllers, middleware
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── config/                # Configuration files
├── database/              # Migrations, factories, seeders
├── routes/                # API and web routes
├── storage/               # File storage
├── tests/                 # Test files
├── composer.json          # PHP dependencies
├── package.json           # Node.js dependencies
└── .env                   # Environment configuration
```

## Key Features & Endpoints

### Authentication
- `POST /api/login` - User login with email/password
- `GET /api/me` - Get authenticated user details
- `POST /api/logout` - User logout

### Core Resources
- **Users**: User management with full CRUD operations
- **Projects**: Project management with status tracking, deadlines, and progress
- **Materials**: Raw material and component tracking
- **RFQs (Request for Quotation)**: Quotation requests to suppliers
- **Suppliers**: Supplier information management
- **Purchase Orders**: Procurement order management
- **Receiving Goods**: Inventory receipt tracking
- **Project Items**: Items associated with specific projects
- **Sub Assemblies**: Component assembly management
- **Machines**: Production equipment tracking
- **BOM Items (Bill of Materials)**: Material composition relationships
- **Item Step Configs**: Production step configurations
- **Tasks**: Production task management with status and completion tracking

### Authentication & Authorization
The system uses Laravel Sanctum for API token authentication. All protected routes require a valid Bearer token in the Authorization header.

## Database Schema

The system includes the following main tables:
- `users` - User accounts
- `projects` - Manufacturing projects with status, deadlines, progress
- `materials` - Raw materials and components
- `rfqs` - Request for quotation records
- `suppliers` - Supplier information
- `rfq_items` - Items in RFQs
- `purchase_orders` - Purchase order records
- `po_items` - Items in purchase orders
- `receiving_goods` - Goods receiving records
- `receiving_items` - Items in receiving records
- `project_items` - Items associated with projects
- `sub_assemblies` - Sub-assembly components
- `machines` - Production machines
- `bom_items` - Bill of materials relationships
- `item_step_configs` - Production step configurations
- `tasks` - Production tasks with status tracking
- `personal_access_tokens` - API authentication tokens

## Environment Configuration

The system uses a `.env` file for configuration. Key environment variables include:
- Database connection settings
- Application URL and debugging settings
- Mail configuration
- Redis and queue settings
- AWS storage configuration (optional)

## Building and Running

### Initial Setup
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Build frontend assets
npm run build
```

### Quick Setup Script
The project includes a setup script in composer.json:
```bash
composer run setup
```

### Development Mode
```bash
# Start development server with hot reloading
npm run dev

# Or use the development script from composer
composer run dev
```

### Testing
```bash
# Run unit and feature tests
composer run test

# Or run Laravel tests directly
php artisan test
```

### Database Operations
```bash
# Run migrations
php artisan migrate

# Create migration
php artisan make:migration create_table_name_table

# Seed database
php artisan db:seed

# Create model with migration
php artisan make:model ModelName -m
```

## Development Conventions

### API Design
- RESTful endpoints following Laravel resource route conventions
- JSON responses with consistent structure
- Proper HTTP status codes
- Sanctum token authentication for protected routes

### Code Style
- PSR-12 coding standards
- Laravel Pint for automatic code formatting
- Model-View-Controller architecture
- Proper use of Eloquent relationships

### Naming Conventions
- Snake_case for database tables and columns
- PascalCase for PHP classes
- camelCase for JavaScript variables
- Descriptive names for routes, controllers, and models

### Security
- Input validation using Laravel form request validation
- Authentication via Laravel Sanctum
- SQL injection protection through Eloquent ORM
- CSRF protection for web routes (if applicable)

## Key Models Relationships

- **Projects** have many **Project Items**
- **Projects** have many **Tasks** through project items
- **Materials** can be part of **BOM Items** (Bill of Materials)
- **RFQs** have many **RFQ Items** and connect to **Suppliers**
- **Purchase Orders** have many **PO Items** and connect to **RFQs**
- **Receiving Goods** have many **Receiving Items** and connect to **Purchase Orders**
- **Tasks** connect to **Project Items** and **Machines**

## Common Commands

```bash
# Serve the application locally
php artisan serve

# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Create a new user (if needed)
php artisan tinker
>>> User::create([...])
```

## Testing

The application uses PHPUnit for testing. Tests are located in the `tests/` directory and can be run with:
```bash
php artisan test
# or
./vendor/bin/phpunit
```

## Deployment

For production deployment:
1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Run `php artisan config:cache` to cache configuration
3. Run `php artisan route:cache` to cache routes
4. Run `php artisan view:cache` to cache views
5. Build frontend assets with `npm run build`