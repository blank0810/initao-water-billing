# Setup & Commands - Initao Water Billing System

This document contains all setup instructions, commands, and environment configuration for the Initao Water Billing System.

---

## Common Commands

### Development

```bash
# Full setup (installs dependencies, generates key, migrates, builds assets)
composer setup

# Start development server (concurrent: Laravel server, queue worker, logs, Vite)
composer dev

# Alternative: Start individual services
php artisan serve               # Start Laravel server (port 8000)
php artisan queue:listen        # Start queue worker
php artisan pail                # View logs
npm run dev                     # Start Vite dev server
```

### Testing

```bash
# Run all tests (uses Pest)
composer test
# or
php artisan test

# Run tests in parallel
php artisan test --parallel

# Run specific test file
php artisan test --filter=TestClassName

# Run specific test method
php artisan test --filter=test_method_name
```

### Database

```bash
# Run migrations
php artisan migrate

# Run migrations with seeding
php artisan migrate --seed

# Rollback last migration batch
php artisan migrate:rollback

# Fresh migrate (drop all tables and re-migrate)
php artisan migrate:fresh
```

### Code Quality

```bash
# Run Laravel Pint (code formatter)
./vendor/bin/pint

# Fix all files
./vendor/bin/pint

# Check without fixing
./vendor/bin/pint --test
```

---

## Docker Environment

### Docker Commands

```bash
# Start all services (nginx:9000, mysql:3307, phpmyadmin:8080, mailpit:8025)
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# Execute commands in app container
docker-compose exec water_billing_app php artisan migrate
docker-compose exec water_billing_app composer install
```

### Service URLs

- **App:** http://localhost:9000
- **PhpMyAdmin:** http://localhost:8080
- **Mailpit UI:** http://localhost:8025

### Docker Services

Services (defined in `docker-compose.yml`):
- `water_billing_app` - PHP-FPM (Laravel)
- `water_billing_nginx` - Nginx web server (port 9000)
- `water_billing_db` - MySQL (port 3307 external, 3306 internal)
- `water_billing_phpmyadmin` - Database admin (port 8080)
- `mailpit` - Email testing (UI: 8025, SMTP: 1025)

Container names and ports configured via `.env` variables.

---

## Environment Configuration

### Key `.env` Settings

```env
APP_URL=http://localhost:9000      # Docker nginx port
DB_HOST=water_billing_db           # container name
DB_PORT=3306                        # internal container port
MAIL_HOST=mailpit                  # for local email testing
VITE_DEV_SERVER_URL=http://localhost:5173
```

### Accessing from Host Machine

- **Database:** `127.0.0.1:3307` (external port)
- **PhpMyAdmin:** `http://localhost:8080`
- **Mailpit UI:** `http://localhost:8025`

---

## Testing

Uses **Pest PHP** with Laravel plugin:
- **Feature tests** for API endpoints and full flows
- **Unit tests** for core service logic (billing calculations)
- Test database: SQLite in-memory (`:memory:`)
- Test environment variables in `phpunit.xml`

```bash
# Run all tests
composer test

# Run in parallel
php artisan test --parallel

# Run specific feature
php artisan test --filter=BillingTest
```

---

## Installation from Scratch

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd initao-water-billing
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   - Update `.env` with database credentials
   - For Docker: Use values from docker-compose.yml

5. **Run migrations**
   ```bash
   php artisan migrate --seed
   ```

6. **Build assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

7. **Start server**
   ```bash
   # With Docker
   docker-compose up -d

   # Or native
   composer dev
   ```

---

## Troubleshooting

### Common Issues

**Vendor folder missing:**
```bash
composer install
```

**Permission issues (Docker):**
```bash
docker-compose exec water_billing_app chmod -R 775 storage bootstrap/cache
docker-compose exec water_billing_app chown -R www-data:www-data storage bootstrap/cache
```

**Migration errors:**
```bash
# Check database connection
php artisan migrate:status

# Reset and retry
php artisan migrate:fresh --seed
```

**Asset build issues:**
```bash
# Clear cache
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
npm run build
```

---

## Development Workflow

1. **Start development environment:**
   ```bash
   docker-compose up -d
   composer dev
   ```

2. **Make changes** to code

3. **Run tests:**
   ```bash
   composer test
   ```

4. **Format code:**
   ```bash
   ./vendor/bin/pint
   ```

5. **Commit changes:**
   ```bash
   git add .
   git commit -m "feat: description"
   git push
   ```

---

For feature documentation, architecture, and business rules, see `.claude/FEATURES.md`
