# SwiftWallet

A fintech wallet application built with a modern microservices architecture. SwiftWallet allows users to manage digital wallets and perform real-time peer-to-peer transfers, powered by GraphQL and Redis.

---

## Tech Stack

### Backend
- **Laravel 13** — PHP framework for each microservice (PHP 8.3+ required)
- **Lighthouse PHP** — GraphQL server for Laravel
- **Laravel Sanctum** — API authentication
- **Filament v5** — Admin panel for internal management
- **MySQL 8** — Primary database per service
- **Redis** — Caching, pub/sub, and queue management

### Frontend
- **Svelte 5** — Reactive UI framework
- **SvelteKit** — Routing and SSR
- **TailwindCSS v4** — Utility-first styling
- **GraphQL Client** — API communication

---

## Architecture

SwiftWallet follows a **monorepo microservices** architecture. Each service is an independent Laravel application with its own database.

```
swiftwallet/
├── apps/
│   └── web/                    # Svelte 5 + SvelteKit frontend
│
├── services/
│   ├── api-gateway/            # GraphQL entry point
│   ├── auth-service/           # Authentication & authorization
│   ├── wallet-service/         # Wallet & balance management
│   ├── transaction-service/    # Transfer & transaction history
│   └── notification-service/   # Email & in-app notifications
│
└── packages/
    ├── graphql-types/           # Shared GraphQL schema types
    └── helpers/                 # Shared utilities
```

### Service Responsibilities

| Service | Responsibility |
|---|---|
| `api-gateway` | Single GraphQL entry point for all client requests |
| `auth-service` | Register, login, JWT via Sanctum |
| `wallet-service` | Create wallet, top up, check balance |
| `transaction-service` | Transfer between users, transaction history |
| `notification-service` | Consume Redis queue, send notifications |

### Redis Usage

| Use Case | Detail |
|---|---|
| **Caching** | Wallet balance cached, invalidated on every transaction |
| **Rate Limiting** | Max 5 transfers per minute per user |
| **Pub/Sub + Queue** | Transaction service publishes events → Notification service consumes |

---

## GraphQL Schema Overview

```graphql
type Query {
  me: User
  wallet: Wallet
  transactions(filter: TransactionFilter): [Transaction]
}

type Mutation {
  topUp(amount: Float!): Wallet
  transfer(toUserId: ID!, amount: Float!): Transaction
}

type Subscription {
  onTransactionReceived: Transaction
}
```

---

## Prerequisites

- [Laravel Herd](https://herd.laravel.com/) — local PHP & MySQL environment
- PHP 8.3+
- Node.js 20+
- Redis (included in Herd Pro, or via Homebrew)
- Composer
- npm

---

## Getting Started

### 1. Clone the repository

```bash
git clone https://github.com/yourusername/swiftwallet.git
cd swiftwallet
```

### 2. Setup each service

Repeat the following steps for each service inside `services/`:

```bash
cd services/auth-service

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate
```

### 3. Configure environment variables

Update `.env` in each service:

```env
APP_NAME=auth-service
APP_URL=http://auth-service.test

DB_DATABASE=swiftwallet_auth
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

> Each service uses its own database. Create the following databases in MySQL:
> `swiftwallet_auth`, `swiftwallet_wallet`, `swiftwallet_transaction`, `swiftwallet_notification`

### 4. Register sites in Laravel Herd

Add each service as a separate site in Herd pointing to its `public/` folder:

| Site | Folder |
|---|---|
| `auth-service.test` | `services/auth-service/public` |
| `wallet-service.test` | `services/wallet-service/public` |
| `transaction-service.test` | `services/transaction-service/public` |
| `notification-service.test` | `services/notification-service/public` |
| `api-gateway.test` | `services/api-gateway/public` |

### 5. Setup frontend

```bash
cd apps/web
npm install
npm run dev
```

Frontend will be available at `http://localhost:5173`

---

## Admin Panel

SwiftWallet uses **Filament v5** as the internal admin panel, accessible via the `api-gateway` service.

```
http://api-gateway.test/admin
```

Filament v5 highlights used in this project:
- **Performance** — table rendering is 2-3x faster compared to v3
- **Tailwind CSS v4** — reworked configuration with faster builds
- **Built-in MFA** — multi-factor authentication out of the box
- **Nested resources** — manage child resources in context of a parent
- **Schema namespace** — unified form and infolist components

To create an admin user:

```bash
cd services/api-gateway
php artisan make:filament-user
```

---

## Running Tests

SwiftWallet uses **Pest PHP** for testing. Run tests per service:

```bash
cd services/auth-service
php artisan test
```

Run with coverage:

```bash
php artisan test --coverage
```

---

## Redis Queue Worker

The notification service relies on Redis queues. Start the queue worker:

```bash
cd services/notification-service
php artisan queue:work redis
```

---

## Environment Variables Reference

| Variable | Description |
|---|---|
| `APP_URL` | Service URL (e.g. `http://auth-service.test`) |
| `DB_DATABASE` | Database name per service |
| `REDIS_HOST` | Redis host (default: `127.0.0.1`) |
| `REDIS_PORT` | Redis port (default: `6379`) |
| `SANCTUM_STATEFUL_DOMAINS` | Allowed frontend domains |
| `QUEUE_CONNECTION` | Set to `redis` for notification service |
| `FILAMENT_FILESYSTEM_DISK` | Storage disk for Filament file uploads |

---

## Roadmap

- [x] Project structure & monorepo setup
- [ ] Auth service — register & login
- [ ] Wallet service — create wallet & top up
- [ ] Transaction service — transfer & history
- [ ] Notification service — Redis queue consumer
- [ ] GraphQL subscriptions — real-time notifications
- [ ] Filament v5 admin panel
- [ ] Docker setup

---

## License

MIT License. Built as a portfolio project.
