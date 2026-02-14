#!/usr/bin/env php
<?php

/**
 * Production Environment Diagnostic Script
 *
 * This script checks for common production configuration issues
 * that can cause 403 Forbidden errors and other problems.
 *
 * Note: This script intentionally uses env() to read raw .env values
 * instead of config() to detect configuration issues even when
 * config is cached. This is a diagnostic tool, not application code.
 *
 * Usage: php diagnostics.php
 */

echo "=================================================\n";
echo "Open Cashbook - Production Diagnostics\n";
echo "=================================================\n\n";

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "1. Environment Check\n";
echo "   -------------------\n";
echo "   APP_ENV: " . env('APP_ENV', 'not set') . "\n";
echo "   APP_DEBUG: " . (env('APP_DEBUG') ? 'true (WARNING: Should be false in production!)' : 'false') . "\n";
echo "   APP_URL: " . env('APP_URL', 'not set') . "\n";
echo "   APP_KEY: " . (env('APP_KEY') ? 'set ✓' : 'NOT SET ✗') . "\n\n";

echo "2. Session Configuration\n";
echo "   ----------------------\n";
echo "   SESSION_DRIVER: " . env('SESSION_DRIVER', 'not set') . "\n";
echo "   SESSION_LIFETIME: " . env('SESSION_LIFETIME', 'not set') . " minutes\n";
echo "   SESSION_SECURE_COOKIE: " . (env('SESSION_SECURE_COOKIE') ? 'true' : 'false') . "\n";
echo "   SESSION_SAME_SITE: " . env('SESSION_SAME_SITE', 'not set') . "\n";
echo "   SESSION_DOMAIN: " . (env('SESSION_DOMAIN') ?: 'null (default)') . "\n";
echo "   SESSION_PATH: " . (env('SESSION_PATH', '/')) . "\n\n";

// Check if using HTTPS
$isHttps = strpos(env('APP_URL'), 'https://') === 0;
$secureCookie = env('SESSION_SECURE_COOKIE');

if ($isHttps && !$secureCookie) {
    echo "   ⚠️  WARNING: Using HTTPS but SESSION_SECURE_COOKIE is false\n";
    echo "      Recommendation: Set SESSION_SECURE_COOKIE=true\n\n";
} elseif (!$isHttps && $secureCookie) {
    echo "   ⚠️  WARNING: Using HTTP but SESSION_SECURE_COOKIE is true\n";
    echo "      This will cause session/CSRF issues!\n";
    echo "      Recommendation: Set SESSION_SECURE_COOKIE=false OR switch to HTTPS\n\n";
} else {
    echo "   ✓ Session cookie configuration matches protocol\n\n";
}

echo "3. Database Connection\n";
echo "   --------------------\n";
try {
    $pdo = DB::connection()->getPdo();
    echo "   Status: Connected ✓\n";
    echo "   Driver: " . DB::connection()->getDriverName() . "\n";

    // Check if sessions table exists
    if (DB::connection()->getDriverName() === 'sqlite') {
        $sessionTableExists = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='sessions'");
    } else {
        $sessionTableExists = DB::select("SHOW TABLES LIKE 'sessions'");
    }

    if (!empty($sessionTableExists)) {
        echo "   Sessions table: Exists ✓\n";
        $sessionCount = DB::table('sessions')->count();
        echo "   Active sessions: " . $sessionCount . "\n";
    } else {
        echo "   ✗ Sessions table: NOT FOUND\n";
        echo "      Run: php artisan migrate\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
}
echo "\n";

echo "4. File Permissions\n";
echo "   -----------------\n";
$storagePath = storage_path();
$isWritable = is_writable($storagePath);
echo "   Storage directory: " . ($isWritable ? 'Writable ✓' : 'NOT WRITABLE ✗') . "\n";

$cacheWritable = is_writable(base_path('bootstrap/cache'));
echo "   Bootstrap cache: " . ($cacheWritable ? 'Writable ✓' : 'NOT WRITABLE ✗') . "\n\n";

if (!$isWritable || !$cacheWritable) {
    echo "   Fix permissions:\n";
    echo "   Linux/Mac: chmod -R 775 storage bootstrap/cache\n";
    echo "   Windows: Give IIS_IUSRS/NETWORK SERVICE write access\n\n";
}

echo "5. Cache Status\n";
echo "   -------------\n";
$configCached = file_exists(base_path('bootstrap/cache/config.php'));
$routesCached = file_exists(base_path('bootstrap/cache/routes-v7.php'));

echo "   Config cached: " . ($configCached ? 'Yes' : 'No') . "\n";
echo "   Routes cached: " . ($routesCached ? 'Yes' : 'No') . "\n";

if ($configCached && env('APP_ENV') !== 'production') {
    echo "   ⚠️  Config is cached but not in production mode\n";
    echo "      Run: php artisan config:clear\n";
}
echo "\n";

echo "6. OAuth Configuration\n";
echo "   --------------------\n";
$googleClientId = env('GOOGLE_CLIENT_ID');
$googleSecret = env('GOOGLE_CLIENT_SECRET');
echo "   Google Client ID: " . ($googleClientId ? 'Set ✓' : 'NOT SET ✗') . "\n";
echo "   Google Client Secret: " . ($googleSecret ? 'Set ✓' : 'NOT SET ✗') . "\n";
echo "   Google Redirect: " . env('GOOGLE_REDIRECT_URI', 'not set') . "\n\n";

echo "7. Common Issues & Solutions\n";
echo "   --------------------------\n";

$issues = [];

if (env('APP_ENV') === 'production' && env('APP_DEBUG') === true) {
    $issues[] = "Set APP_DEBUG=false in production for security";
}

if (!env('APP_KEY')) {
    $issues[] = "Generate APP_KEY with: php artisan key:generate";
}

if (env('SESSION_DRIVER') === 'database' && empty($sessionTableExists)) {
    $issues[] = "Run migrations: php artisan migrate";
}

if (!$isWritable || !$cacheWritable) {
    $issues[] = "Fix storage permissions (see section 4)";
}

if ($configCached && env('APP_ENV') !== 'production') {
    $issues[] = "Clear config cache: php artisan config:clear";
}

if (empty($issues)) {
    echo "   ✓ No obvious issues detected!\n\n";
    echo "   If you're still experiencing 403 errors:\n";
    echo "   1. Clear all caches: php artisan cache:clear && php artisan config:clear\n";
    echo "   2. Check storage/logs/laravel.log for errors\n";
    echo "   3. Verify SESSION_SECURE_COOKIE matches your protocol (HTTP/HTTPS)\n";
    echo "   4. Test in a different browser or incognito mode\n";
} else {
    echo "   Issues found:\n";
    foreach ($issues as $i => $issue) {
        echo "   " . ($i + 1) . ". " . $issue . "\n";
    }
}

echo "\n";
echo "=================================================\n";
echo "Diagnostic Complete\n";
echo "=================================================\n";


