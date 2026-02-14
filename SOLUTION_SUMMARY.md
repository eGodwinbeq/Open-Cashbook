# Production 403 Errors - Complete Solution Summary

## What Was the Problem?

You reported that in production, several routes were showing **403 Forbidden** errors:
- `/invoices/{id}/payment` (Add Payment)
- `/invoices/{id}/mark-paid` (Mark as Paid)  
- `/invoices/{id}` (View Invoice)
- `/revenue-report` (Revenue Report)
- Cash in/cash out transactions

## Root Cause Analysis

The 403 errors are caused by **CSRF token mismatch** issues, which occur when:

1. **SESSION_SECURE_COOKIE mismatch**: The most common cause
   - If you're using `http://` but `SESSION_SECURE_COOKIE=true` → CSRF tokens fail
   - If you're using `https://` but `SESSION_SECURE_COOKIE=false` → May also cause issues
   
2. **Missing session table**: Database session driver requires a `sessions` table

3. **Cached configuration**: Old cached config with wrong settings

4. **File permissions**: Session files can't be written to storage

5. **Proxy/Load Balancer**: Not trusting proxies when behind Cloudflare, AWS, etc.

## What We Fixed

### 1. Added Session Configuration to `.env.example`

Added missing session variables:
```env
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

### 2. Created TrustProxies Middleware

Created `app/Http/Middleware/TrustProxies.php` for sites behind proxies.

Updated `bootstrap/app.php` with commented instructions on when to enable it.

### 3. Created Diagnostic Tools

**File: `diagnostics.php`**
- Checks environment configuration
- Validates session settings
- Verifies database connectivity
- Checks file permissions
- Identifies common misconfigurations
- Provides actionable recommendations

**Usage:**
```bash
php diagnostics.php
```

### 4. Created Quick Fix Scripts

**For Linux/Mac: `fix-403.sh`**
```bash
chmod +x fix-403.sh
./fix-403.sh
```

**For Windows: `fix-403.bat`**
```cmd
fix-403.bat
```

**Interactive Setup: `setup-production.php`**
```bash
php setup-production.php
```

### 5. Created Documentation

**FIX_403_ERRORS.md**
- Quick reference guide
- Step-by-step fix instructions
- Common issues and solutions

**PRODUCTION_DEPLOYMENT.md**
- Complete deployment checklist
- Environment variables reference
- Security recommendations
- Debugging tips
- Post-deployment verification steps

### 6. Updated README.md

Added links to troubleshooting documentation and diagnostic tools.

## How to Fix Your Production Site

### Option 1: Quick Fix (Recommended)

**Linux/Mac:**
```bash
./fix-403.sh
```

**Windows:**
```cmd
fix-403.bat
```

### Option 2: Manual Fix

1. **Edit `.env` file:**
   ```env
   # If using HTTPS
   SESSION_SECURE_COOKIE=true
   
   # If using HTTP
   SESSION_SECURE_COOKIE=false
   
   # Always set these
   SESSION_SAME_SITE=lax
   SESSION_DRIVER=database
   ```

2. **Clear caches:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   ```

3. **Run migrations:**
   ```bash
   php artisan migrate --force
   ```

4. **Fix permissions:**
   ```bash
   # Linux/Mac
   chmod -R 775 storage bootstrap/cache
   
   # Windows: Give IIS_IUSRS full control to storage and bootstrap/cache
   ```

### Option 3: Interactive Setup

```bash
php setup-production.php
```

Follow the prompts to configure your environment correctly.

### Option 4: Run Diagnostics First

```bash
php diagnostics.php
```

This will show you exactly what's wrong, then you can fix it manually.

## Prevention

To prevent this issue in the future:

1. **Always match SESSION_SECURE_COOKIE to your protocol**
   - HTTPS → `SESSION_SECURE_COOKIE=true`
   - HTTP → `SESSION_SECURE_COOKIE=false`

2. **Clear caches after changing `.env`**
   ```bash
   php artisan config:clear
   ```

3. **Use the diagnostic tool** before deploying to production
   ```bash
   php diagnostics.php
   ```

4. **Follow the deployment checklist** in `PRODUCTION_DEPLOYMENT.md`

## Additional Features Fixed

### Light Mode by Default

The app is already configured to always use light mode in production. No action needed.

This is controlled in `resources/js/app.js`:
```javascript
document.documentElement.classList.remove('dark');
document.documentElement.classList.add('light');
localStorage.setItem('theme', 'light');
```

## Files Created/Modified

### New Files:
1. `FIX_403_ERRORS.md` - Quick troubleshooting guide
2. `PRODUCTION_DEPLOYMENT.md` - Complete deployment guide
3. `diagnostics.php` - Diagnostic tool
4. `setup-production.php` - Interactive setup script
5. `fix-403.sh` - Quick fix script (Linux/Mac)
6. `fix-403.bat` - Quick fix script (Windows)
7. `app/Http/Middleware/TrustProxies.php` - Proxy middleware

### Modified Files:
1. `.env.example` - Added session variables
2. `bootstrap/app.php` - Added proxy middleware instructions
3. `README.md` - Added links to documentation

## Testing Your Fix

After applying the fix:

1. **Clear browser data** (or use incognito/private mode)
2. **Log out completely**
3. **Log in again**
4. **Test these actions:**
   - Create a new invoice
   - Add a payment to an invoice
   - Mark an invoice as paid
   - View revenue report
   - Add cash in transaction
   - Add cash out transaction

All should work without 403 errors.

## Still Having Issues?

1. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Run diagnostics:**
   ```bash
   php diagnostics.php
   ```

3. **Verify environment:**
   - Check `APP_URL` matches your actual domain
   - Check protocol (http vs https)
   - Check `SESSION_SECURE_COOKIE` matches protocol

4. **Test in different browser**
   - Sometimes browser cache causes issues
   - Try incognito/private mode

5. **Check server logs**
   - Apache: `/var/log/apache2/error.log`
   - Nginx: `/var/log/nginx/error.log`
   - IIS: Event Viewer

## Support

If you continue to experience issues:
1. Run `php diagnostics.php` and check the output
2. Check `storage/logs/laravel.log` for errors
3. Verify all migrations have run: `php artisan migrate:status`
4. Ensure PHP version is 8.2 or higher: `php -v`

## Summary

The 403 errors you're experiencing are **session/CSRF configuration issues**, not authorization issues with your code. The routes themselves are correctly protected with authentication middleware. The problem is that the CSRF tokens aren't being validated properly due to session configuration mismatches.

By ensuring `SESSION_SECURE_COOKIE` matches your site's protocol (HTTP/HTTPS) and clearing caches, the 403 errors should be completely resolved.

