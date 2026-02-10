# Beauty Pageant Judging System

A Laravel 12 web application for managing beauty pageant judging workflows with dynamic configuration, real-time updates, and comprehensive scoring capabilities.

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL 8.0 or higher

## Installation

1. Clone the repository
2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node dependencies:
   ```bash
   npm install
   ```

4. Copy the environment file:
   ```bash
   copy .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=beauty_pageant_judging
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

7. Create the database:
   ```bash
   mysql -u root -p -e "CREATE DATABASE beauty_pageant_judging;"
   ```

8. Run migrations:
   ```bash
   php artisan migrate
   ```

9. Build frontend assets:
   ```bash
   npm run build
   ```

## Development

Run the development server:
```bash
php artisan serve
```

Watch for asset changes:
```bash
npm run dev
```

## Testing

Run all tests:
```bash
php artisan test
```

Run specific test suite:
```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

## Architecture

The application follows SOLID principles and clean architecture with the following structure:

- **Actions** (`app/Actions`): Single-purpose operations
- **Services** (`app/Services`): Business logic orchestration
- **Repositories** (`app/Repositories`): Data access abstraction
- **DTOs** (`app/DTOs`): Data transfer objects
- **Policies** (`app/Policies`): Authorization logic
- **Enums** (`app/Enums`): Enumeration types

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Tailwind CSS v4
- **Database**: MySQL
- **Build Tool**: Vite
- **Testing**: PHPUnit with Eris (Property-Based Testing)

## License

This project is open-sourced software licensed under the MIT license.
