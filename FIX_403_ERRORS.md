# 403 Forbidden Error - Quick Fix Guide

## Problem

You're experiencing 403 Forbidden errors on:
- `/invoices/{id}/payment` (Add Payment)
- `/invoices/{id}/mark-paid` (Mark as Paid)
- `/invoices/{id}` (View Invoice)
- `/revenue-report` (Revenue Report)
- Cash in/out transactions
- Other authenticated routes

## Root Cause

The 403 errors are caused by **CSRF token mismatch** due to session configuration issues in production.

## Quick Fix (Do This Now)

### Step 1: Check Your `.env` File

Open your `.env` file on the production server and verify these settings:

```env
# Are you using HTTPS or HTTP?
APP_URL=https://yourdomain.com   # Or http://yourdomain.com

# Session settings - CRITICAL!
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# If using HTTPS, set to true. If HTTP, MUST be false
SESSION_SECURE_COOKIE=false    # Change to true ONLY if using HTTPS

SESSION_SAME_SITE=lax
SESSION_HTTP_ONLY=true
```

**IMPORTANT:**
- If your site uses `http://` → Set `SESSION_SECURE_COOKIE=false`
- If your site uses `https://` → Set `SESSION_SECURE_COOKIE=true`
- Mismatch between protocol and this setting causes 403 errors!

### Step 2: Clear All Caches

Run these commands on your production server:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 3: Verify Migrations

Make sure all database tables exist:

```bash
php artisan migrate --force
```

This ensures the `sessions` table exists.

### Step 4: Check Permissions

Ensure storage is writable:

**Linux/Mac:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

**Windows:**
- Right-click `storage` folder → Properties → Security
- Give `IIS_IUSRS` or `NETWORK SERVICE` full control
- Do the same for `bootstrap/cache`

### Step 5: Test

1. Log out completely
2. Clear browser cookies/cache (or use incognito)
3. Log in again
4. Try adding a payment or marking an invoice as paid

## Still Having Issues?

### Run Diagnostics

We've included a diagnostic script. Run it to check your configuration:

```bash
php diagnostics.php
```

This will show you exactly what's misconfigured.

### Check Logs

View your Laravel error logs:

```bash
tail -f storage/logs/laravel.log
```

Look for errors mentioning:
- "CSRF token mismatch"
- "TokenMismatchException"
- "Session store not set"

### Common Issues

#### Issue: "CSRF token mismatch"
**Solution:** Your `SESSION_SECURE_COOKIE` doesn't match your protocol (HTTP/HTTPS). Fix it in `.env` and clear caches.

#### Issue: 403 on all POST/PUT/DELETE requests
**Solution:** Sessions aren't working. Check:
1. `sessions` table exists in database
2. Storage directory is writable
3. Config is cleared (`php artisan config:clear`)

#### Issue: Works locally but not in production
**Solution:** Production environment variables differ. Compare your local `.env` with production `.env`.

#### Issue: Behind a proxy (Cloudflare, AWS, etc.)
**Solution:** Uncomment the TrustProxies line in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');
})
```

## Prevention

After fixing the issue, in production:

1. **Cache your config** (improves performance):
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Set proper environment**:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

3. **Use HTTPS** (strongly recommended for production)

## Need More Help?

See the complete deployment guide: [PRODUCTION_DEPLOYMENT.md](./PRODUCTION_DEPLOYMENT.md)

## Light Mode

The app is configured to always use light mode in production. Dark mode support is disabled by default.

