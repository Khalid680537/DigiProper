# DigiProper

DigiProper is a property management platform for Indian property owners — a centralized way to capture properties, financials, tenure, contacts, and documents, and to surface portfolio-level analytics. Web today, mobile next.

## Stack

- Laravel 13 / PHP 8.4, Pest 4, Pint
- Breeze (web sessions), Sanctum (mobile token auth)
- Vite 8, Tailwind, Alpine.js, Blade
- MySQL in production, SQLite for local dev
- Expo / React Native mobile app at `/mobile/` (scaffold pending)

## Getting started

```bash
composer install
npm ci
cp .env.example .env && php artisan key:generate
php artisan migrate
composer dev   # serve + queue + logs + vite
```

## Day-to-day

- Tests: `./vendor/bin/pest`
- Format: `./vendor/bin/pint`
- See [`CLAUDE.md`](CLAUDE.md) for repo conventions and India-specific rules (₹ formatting, PII handling, +91 phone, IST, RERA/GST).
