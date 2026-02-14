@echo off
REM Quick fix script for 403 errors in production (Windows)
REM This script clears caches and checks common configuration issues

echo.
echo ==================================================
echo Open Cashbook - 403 Error Quick Fix
echo ==================================================
echo.

REM Check if we're in the right directory
if not exist "artisan" (
    echo Error: artisan file not found!
    echo Please run this script from your project root directory.
    exit /b 1
)

echo 1. Clearing all caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo    Done
echo.

echo 2. Running migrations (if needed)...
php artisan migrate --force
echo.

echo 3. Checking SESSION_SECURE_COOKIE setting...
if exist ".env" (
    findstr /B "APP_URL=" .env
    findstr /B "SESSION_SECURE_COOKIE=" .env
    echo.
    echo    Check if SESSION_SECURE_COOKIE matches your protocol:
    echo    - HTTPS? SESSION_SECURE_COOKIE should be true
    echo    - HTTP? SESSION_SECURE_COOKIE should be false
) else (
    echo    Warning: .env file not found!
)
echo.

echo 4. Checking file permissions...
echo    Ensure IIS_IUSRS has write access to:
echo    - storage/
echo    - bootstrap/cache/
echo.

echo ==================================================
echo Quick fix complete!
echo ==================================================
echo.
echo Next steps:
echo 1. Test in a new incognito/private browser window
echo 2. If still having issues, run: php diagnostics.php
echo 3. Check logs in storage/logs/laravel.log
echo 4. See FIX_403_ERRORS.md for detailed troubleshooting
echo.

pause

