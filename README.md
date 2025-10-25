# Timesheet - Employee Management SaaS

**Timesheet** is a modern, type-safe employee management and timesheet tracking application built with [Laravel](https://laravel.com), [Inertia.js](https://inertiajs.com), and [React](https://react.dev). This SaaS application provides comprehensive time tracking, employee management, and reporting capabilities for businesses of all sizes.

## Technical Excellence

- **Type-Safe Architecture**: Built with PHP 8.4+ and TypeScript for maximum reliability
- **Actions-Oriented Design**: Clean, testable business logic with single-action classes
- **100% Test Coverage**: Comprehensive test suite using Pest
- **Modern UI**: Built with React, Tailwind CSS v4, and shadcn/ui components
- **Real-Time Updates**: Live notifications and updates using Laravel Echo
- **API-First**: RESTful API for mobile apps and third-party integrations

## Getting Started

> **Requires [PHP 8.4+](https://php.net/releases/), [Node.js 18+](https://nodejs.org/), and [Composer](https://getcomposer.org)**.

Clone and set up the Timesheet application:

```bash
git clone <repository-url> timesheet
cd timesheet

# Setup the project
composer setup

# Start the development server
composer dev
```

The application will be available at `http://localhost:8000`.

### Database Setup

The application uses SQLite by default for development. For production, configure your preferred database in the `.env` file:

```bash
# Copy and configure environment variables
cp .env.example .env

# Run migrations and seed sample data
php artisan migrate --seed
```

### Verify Installation

Run the test suite to ensure everything is configured correctly:

```bash
composer test
```

You should see 100% test coverage and all quality checks passing.

## Development Commands

### Development Server

- `composer dev` - Starts Laravel serve, queue worker, log monitoring, and Vite dev server concurrently
- `php artisan serve` - Laravel development server only
- `pnpm run dev` - Vite development server for frontend assets

### Database

- `php artisan migrate` - Run database migrations
- `php artisan migrate --seed` - Run migrations and seed sample data
- `php artisan migrate:refresh --seed` - Reset database with fresh data

### Code Quality & Testing

- `composer test` - Run complete test suite with 100% coverage requirement
- `composer lint` - Format code with Rector, Pint, and Prettier
- `pnpm run lint` - Lint JavaScript/TypeScript code
- `pnpm run format` - Format all code files

### Production

- `composer install --no-dev --optimize-autoloader` - Install production dependencies
- `pnpm run build` - Build optimized frontend assets
- `php artisan config:cache` - Cache configuration for production

## Tech Stack

- **Backend**: Laravel 12 with PHP 8.4+
- **Frontend**: React 19 with TypeScript
- **Styling**: Tailwind CSS v4 with shadcn/ui components
- **Database**: SQLite (development) / PostgreSQL, MySQL (production)
- **Testing**: Pest with 100% coverage requirement
- **Code Quality**: PHPStan, Rector, ESLint, Prettier
- **Bundling**: Vite with Laravel Wayfinder

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Run tests (`composer test`)
4. Commit changes (`git commit -m 'Add amazing feature'`)
5. Push to branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request

## License

This project is licensed under the **[MIT License](https://opensource.org/licenses/MIT)**.
