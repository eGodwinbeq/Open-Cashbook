# Open Cashbook

Open Cashbook is a simple, privacy-focused cashbook application for tracking daily cash inflows and outflows.

This repository contains a Laravel-based backend and a Tailwind CSS v4-powered frontend.

## Tech Stack

- **Backend:** Laravel 12
- **Frontend:** Tailwind CSS v4 (with Vite)
- **Database:** SQLite
- **JavaScript:** Alpine.js

## Recent Updates

- ✅ **January 2026:** Rebranded to "Open Cashbook"
- ✅ **January 2026:** Mobile UX improvements (right sidebar, sticky buttons)
- ✅ **January 2026:** Upgraded to Tailwind CSS v4

## Getting Started

```bash
# Install dependencies
composer install
npm install

# Build assets
npm run build

# Run migrations
php artisan migrate

# Start server
php artisan serve
```

Visit http://127.0.0.1:8000

## Development

```bash
# Watch for changes (hot reload)
npm run dev
```

## Documentation

- [Mobile UX Improvements](MOBILE_UX_IMPROVEMENTS.md)
- [Tailwind v4 Upgrade Guide](TAILWIND_V4_UPGRADE.md)
- [Testing Checklist](TESTING_CHECKLIST.md)
- **[Production Deployment Guide](PRODUCTION_DEPLOYMENT.md)** ⭐
- **[Fix 403 Errors (Quick Guide)](FIX_403_ERRORS.md)** ⭐

## Production Issues?

**Experiencing 403 Forbidden errors in production?**

The issue was **missing route model binding scoping**. We've fixed it!

See: **[The Real 403 Fix Explanation](REAL_FIX_403_ERRORS.md)** ⭐

To deploy the fix:
```bash
git pull
php artisan migrate --force
php artisan cache:clear
php artisan route:clear
```

Other helpful guides:
- [Production Deployment Guide](PRODUCTION_DEPLOYMENT.md) - Complete deployment checklist
- [Session Configuration Guide](FIX_403_ERRORS.md) - CSRF/session troubleshooting

Run diagnostics:
```bash
php diagnostics.php
```
