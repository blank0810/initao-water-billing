# Initao Water Billing System

A full-featured water utility billing system built for the Municipality of Initao, Philippines. Manages the complete water service lifecycle — from customer applications and meter readings to billing generation and payment processing.

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## The Problem

Municipal water utilities in the Philippines often rely on manual ledgers or outdated systems that lead to billing errors, payment tracking issues, and inefficient meter reading workflows. Initao needed a modern solution that could handle their complete water service operations.

## The Solution

This system digitizes the entire water billing workflow — customer registration, service applications, meter management, automated bill generation, payment allocation, and financial ledger tracking — giving the municipality accurate, auditable records and streamlined operations.

--

## Features

### Customer Management

- Customer registration with hierarchical Philippine addresses (Province → Town → Barangay → Purok)
- Service application workflow with document tracking
- Multiple service connections per customer
- Auto-generated resolution numbers for unique identification

### Meter & Reading Management

- Meter inventory with assignment history
- Area-based meter reader assignments
- Periodic reading schedules and tracking
- Reading validation and consumption calculation

### Billing

- Automated bill generation based on meter readings
- Configurable water rates and billing periods
- Bill adjustments (credits, penalties, waivers)
- Period-based billing cycles with closure controls

### Payments

- Payment receipt generation
- Flexible payment allocation across multiple bills and charges
- Customer ledger with full transaction history
- Double-entry accounting for audit trails

### Administration

- Role-based access control (RBAC)
- Area and zone management
- Configurable charge items (connection fees, reconnection fees, etc.)
- Audit logging for critical operations

---

## Tech Stack

| Layer               | Technology                                   |
| ------------------- | -------------------------------------------- |
| **Backend**         | Laravel 12 (PHP 8.2+)                        |
| **Frontend**        | Blade Templates, Alpine.js, Tailwind CSS 3  |
| **UI Components**   | Flowbite, DataTables                         |
| **Database**        | MySQL 8                                      |
| **Build Tool**      | Vite 7                                       |
| **Testing**         | Pest PHP                                     |
| **Code Quality**    | Laravel Pint (PSR-12)                        |
| **Visualization**   | Chart.js                                     |
| **Containerization**| Docker                                       |

---

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL 8
- Docker (optional)

### Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/yourusername/initao-water-billing.git
   cd initao-water-billing
   ```

2. **Install dependencies**

   ```bash
   composer install
   npm install
   ```

3. **Configure environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Set up database**

   Update `.env` with your database credentials, then:

   ```bash
   php artisan migrate --seed
   ```

5. **Build assets and run**

   ```bash
   npm run build
   php artisan serve
   ```

   Visit `http://localhost:8000`

### Quick Start (Docker)

```bash
docker-compose up -d
docker-compose exec app php artisan migrate --seed
```

### Development

```bash
composer dev   # Runs Laravel server, queue worker, and Vite concurrently
```

---

## Roadmap

- [ ] **SMS & Email Notifications**
  - Billing reminders and payment confirmations
  - Disconnection warnings
  - Service application status updates

- [ ] **Online Payment Integration**
  - GCash and PayMaya support
  - Real-time payment verification
  - Digital receipt generation

- [ ] **Customer Self-Service Portal**
  - View bills and payment history
  - Download statements
  - Submit service requests online

- [ ] **Legacy System Migration**
  - Complete migration from Consumer-based to ServiceConnection-based billing
  - Data validation and reconciliation tools
  - Phased rollout with parallel operation support

---

## Contributing

Contributions are welcome! Here's how to get started:

1. Fork the repository
2. Create a feature branch (`git checkout -b feat/your-feature`)
3. Make your changes
4. Run tests (`php artisan test`)
5. Run code formatting (`./vendor/bin/pint`)
6. Commit using conventional commits:
   - `feat(scope): add new feature`
   - `fix(scope): correct bug`
   - `docs: update documentation`
7. Push and open a Pull Request

### Code Standards

- Follow PSR-12 coding style
- Keep business logic in Services, not Controllers
- Write tests for new features
- Use Eloquent models directly (no repository pattern)

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

Built with coffee for the Municipality of Initao, Misamis Oriental, Philippines
