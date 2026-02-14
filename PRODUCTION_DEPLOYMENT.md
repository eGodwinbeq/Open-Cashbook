# Production Deployment Guide

## 403 Forbidden Error Troubleshooting

If you're experiencing 403 Forbidden errors on invoice routes, transaction routes, or other authenticated pages in production, follow these steps:

### Common Causes and Solutions

#### 1. CSRF Token Mismatch (Most Common)

**Symptoms:**
- 403 errors on POST/PUT/DELETE requests
- Forms submission fails
- `/invoices/{id}/payment`, `/invoices/{id}/mark-paid`, etc. return 403

**Solution:**

Check your `.env` file in production and ensure the following settings are correct:

```env
# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# IMPORTANT: Set to false if NOT using HTTPS
SESSION_SECURE_COOKIE=false

# IMPORTANT: Should be 'lax' for most cases
SESSION_SAME_SITE=lax

# HTTP Only (should be true for security)
SESSION_HTTP_ONLY=true
```

**For HTTPS sites:**
```env
SESSION_SECURE_COOKIE=true
```

**For HTTP sites (development/non-SSL):**
```env
SESSION_SECURE_COOKIE=false
```

#### 2. Clear Configuration Cache

After changing `.env`, you MUST clear the config cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

#### 3. Database Session Table

Ensure the sessions table exists in your database:

```bash
php artisan migrate
```

Check that the `sessions` table was created properly. It should have these columns:
- id (string, primary key)
- user_id (nullable)
- ip_address
- user_agent
- payload
- last_activity

#### 4. Storage Permissions

Ensure storage directories are writable:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

On Windows:
- Right-click `storage` folder → Properties → Security
- Give IIS_IUSRS or NETWORK SERVICE full control

#### 5. APP_URL Configuration

Make sure `APP_URL` matches your actual domain:

```env
# For production
APP_URL=https://yourdomain.com

# NOT
APP_URL=http://localhost
```

#### 6. Session Domain Configuration

For subdomains, set SESSION_DOMAIN:

```env
# For main domain only
SESSION_DOMAIN=null

# For domain and subdomains
SESSION_DOMAIN=.yourdomain.com
```

#### 7. Mixed Content Issues (HTTP/HTTPS)

If your site uses HTTPS but `APP_URL` is HTTP, update:

```env
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
```

#### 8. Reverse Proxy / Load Balancer

If behind a proxy (Cloudflare, AWS ELB, etc.), add trusted proxies.

Create `app/Http/Middleware/TrustProxies.php`:

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*'; // Or specific IPs

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
```

Register in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');
})
```

## Complete Production Deployment Checklist

### Pre-Deployment

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY` (if not already set)
- [ ] Configure proper `APP_URL`
- [ ] Configure database credentials
- [ ] Configure session settings (see above)
- [ ] Set up OAuth credentials (Google)

### Deployment Steps

1. **Upload files** to production server

2. **Install dependencies:**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install
   npm run build
   ```

3. **Set proper permissions:**
   ```bash
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```

4. **Run migrations:**
   ```bash
   php artisan migrate --force
   ```

5. **Clear and cache everything:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. **Verify `.env` settings** (see above)

### Post-Deployment Verification

1. **Test authentication:**
   - Log in
   - Log out
   - Register new account

2. **Test invoice operations:**
   - View invoices list
   - Create new invoice
   - Edit invoice
   - Delete invoice
   - Add payment to invoice
   - Mark invoice as paid
   - Download invoice

3. **Test transaction operations:**
   - Add cash in
   - Add cash out
   - View transactions

4. **Test revenue report:**
   - Access `/revenue-report`
   - Click on links
   - Verify data loads

### Debugging Production Issues

Enable temporary debugging (ONLY for troubleshooting, disable after):

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Check logs:
```bash
tail -f storage/logs/laravel.log
```

Common error messages:
- "CSRF token mismatch" → Session configuration issue
- "TokenMismatchException" → Clear cache and check SESSION_SECURE_COOKIE
- "Session store not set" → Run `php artisan config:clear`

## Environment Variables Reference

### Required for Production

```env
APP_NAME="Open Cashbook"
APP_ENV=production
APP_KEY=base64:YOUR_32_CHARACTER_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=sqlite
# OR for MySQL/PostgreSQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=opencashbook
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true  # true for HTTPS, false for HTTP
SESSION_SAME_SITE=lax
SESSION_HTTP_ONLY=true

CACHE_STORE=database

GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

## Security Recommendations

1. **Always use HTTPS in production**
2. **Set `APP_DEBUG=false`**
3. **Use strong `APP_KEY`**
4. **Keep `.env` file secure** (not in version control)
5. **Use `SESSION_SECURE_COOKIE=true` with HTTPS**
6. **Enable `SESSION_HTTP_ONLY=true`**
7. **Regularly update dependencies**
8. **Use proper file permissions**

## Support

If issues persist after following this guide:
1. Check `storage/logs/laravel.log`
2. Verify all migrations have run
3. Verify session table exists and is accessible
4. Check server PHP version (requires PHP 8.2+)
5. Verify all required PHP extensions are installed

