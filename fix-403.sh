#!/bin/bash

# Quick fix script for 403 errors in production
# This script clears caches and checks common configuration issues

echo ""
echo "=================================================="
echo "Open Cashbook - 403 Error Quick Fix"
echo "=================================================="
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found!"
    echo "   Please run this script from your project root directory."
    exit 1
fi

echo "1. Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo "   ✓ Caches cleared"
echo ""

echo "2. Running migrations (if needed)..."
php artisan migrate --force
echo ""

echo "3. Checking SESSION_SECURE_COOKIE setting..."
if [ -f ".env" ]; then
    APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2)
    SESSION_SECURE=$(grep "^SESSION_SECURE_COOKIE=" .env | cut -d '=' -f2)

    echo "   APP_URL: $APP_URL"
    echo "   SESSION_SECURE_COOKIE: $SESSION_SECURE"

    if [[ $APP_URL == https://* ]] && [[ $SESSION_SECURE != "true" ]]; then
        echo ""
        echo "   ⚠️  WARNING: Using HTTPS but SESSION_SECURE_COOKIE is not true"
        echo "   This will cause 403 errors!"
        echo ""
        echo "   Fix it now? (y/n)"
        read -r fix_session
        if [ "$fix_session" = "y" ] || [ "$fix_session" = "Y" ]; then
            sed -i.bak 's/^SESSION_SECURE_COOKIE=.*/SESSION_SECURE_COOKIE=true/' .env
            echo "   ✓ Updated SESSION_SECURE_COOKIE=true"
            echo "   Clearing config cache again..."
            php artisan config:clear
        fi
    elif [[ $APP_URL == http://* ]] && [[ $SESSION_SECURE == "true" ]]; then
        echo ""
        echo "   ⚠️  WARNING: Using HTTP but SESSION_SECURE_COOKIE is true"
        echo "   This will cause 403 errors!"
        echo ""
        echo "   Fix it now? (y/n)"
        read -r fix_session
        if [ "$fix_session" = "y" ] || [ "$fix_session" = "Y" ]; then
            sed -i.bak 's/^SESSION_SECURE_COOKIE=.*/SESSION_SECURE_COOKIE=false/' .env
            echo "   ✓ Updated SESSION_SECURE_COOKIE=false"
            echo "   Clearing config cache again..."
            php artisan config:clear
        fi
    else
        echo "   ✓ SESSION_SECURE_COOKIE matches your protocol"
    fi
else
    echo "   ⚠️  .env file not found!"
fi
echo ""

echo "4. Checking file permissions..."
if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
    echo "   ✓ Permissions OK"
else
    echo "   ⚠️  Storage directories may not be writable"
    echo "   Run: chmod -R 775 storage bootstrap/cache"
fi
echo ""

echo "=================================================="
echo "Quick fix complete!"
echo "=================================================="
echo ""
echo "Next steps:"
echo "1. Test in a new incognito/private browser window"
echo "2. If still having issues, run: php diagnostics.php"
echo "3. Check logs: tail -f storage/logs/laravel.log"
echo "4. See FIX_403_ERRORS.md for detailed troubleshooting"
echo ""

