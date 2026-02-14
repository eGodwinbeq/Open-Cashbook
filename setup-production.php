#!/usr/bin/env php
<?php

/**
 * Production Setup Helper
 *
 * This interactive script helps configure your production environment
 * to prevent common 403 and CSRF token issues.
 *
 * Usage: php setup-production.php
 */

echo "\n";
echo "╔═══════════════════════════════════════════════════════╗\n";
echo "║                                                       ║\n";
echo "║       Open Cashbook - Production Setup Helper        ║\n";
echo "║                                                       ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n";
echo "\n";

echo "This script will help you configure your .env file for production.\n\n";

// Check if .env exists
if (!file_exists(__DIR__ . '/.env')) {
    echo "❌ Error: .env file not found!\n";
    echo "   Please copy .env.example to .env first:\n";
    echo "   cp .env.example .env\n\n";
    exit(1);
}

// Read current .env
$envContent = file_get_contents(__DIR__ . '/.env');
$envLines = explode("\n", $envContent);
$envVars = [];

foreach ($envLines as $line) {
    if (empty(trim($line)) || strpos(trim($line), '#') === 0) {
        continue;
    }
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $envVars[trim($key)] = trim($value);
    }
}

echo "Current configuration detected:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "APP_URL: " . ($envVars['APP_URL'] ?? 'not set') . "\n";
echo "APP_ENV: " . ($envVars['APP_ENV'] ?? 'not set') . "\n";
echo "SESSION_SECURE_COOKIE: " . ($envVars['SESSION_SECURE_COOKIE'] ?? 'not set') . "\n";
echo "SESSION_SAME_SITE: " . ($envVars['SESSION_SAME_SITE'] ?? 'not set') . "\n\n";

// Ask questions
echo "Please answer the following questions:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Question 1: HTTPS or HTTP
echo "1. Does your production site use HTTPS? (yes/no) [yes]: ";
$useHttps = trim(fgets(STDIN));
$useHttps = empty($useHttps) ? 'yes' : strtolower($useHttps);

// Question 2: Domain
echo "2. What is your production domain? (e.g., example.com): ";
$domain = trim(fgets(STDIN));
if (empty($domain)) {
    $domain = 'localhost';
}

// Question 3: Confirm
echo "\n";
echo "Configuration to apply:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━\n";
$protocol = ($useHttps === 'yes' || $useHttps === 'y') ? 'https' : 'http';
echo "APP_URL: {$protocol}://{$domain}\n";
echo "APP_ENV: production\n";
echo "APP_DEBUG: false\n";
echo "SESSION_SECURE_COOKIE: " . (($protocol === 'https') ? 'true' : 'false') . "\n";
echo "SESSION_SAME_SITE: lax\n";
echo "\n";
echo "Apply these changes? (yes/no) [yes]: ";
$confirm = trim(fgets(STDIN));
$confirm = empty($confirm) ? 'yes' : strtolower($confirm);

if ($confirm !== 'yes' && $confirm !== 'y') {
    echo "\n❌ Cancelled. No changes made.\n\n";
    exit(0);
}

// Apply changes
echo "\n";
echo "Applying changes...\n";

$updates = [
    'APP_URL' => "{$protocol}://{$domain}",
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'SESSION_DRIVER' => 'database',
    'SESSION_SECURE_COOKIE' => ($protocol === 'https') ? 'true' : 'false',
    'SESSION_SAME_SITE' => 'lax',
    'SESSION_PATH' => '/',
    'SESSION_DOMAIN' => 'null',
    'SESSION_HTTP_ONLY' => 'true',
];

foreach ($updates as $key => $value) {
    $pattern = "/^{$key}=.*/m";
    if (preg_match($pattern, $envContent)) {
        $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
        echo "  ✓ Updated {$key}\n";
    } else {
        // Add new line
        $envContent .= "\n{$key}={$value}";
        echo "  ✓ Added {$key}\n";
    }
}

// Write back to .env
file_put_contents(__DIR__ . '/.env', $envContent);

echo "\n";
echo "✅ Configuration updated successfully!\n\n";

echo "Next steps:\n";
echo "━━━━━━━━━━━\n";
echo "1. Run migrations:\n";
echo "   php artisan migrate --force\n\n";
echo "2. Clear all caches:\n";
echo "   php artisan config:clear\n";
echo "   php artisan cache:clear\n";
echo "   php artisan view:clear\n";
echo "   php artisan route:clear\n\n";
echo "3. Cache for production (optional, improves performance):\n";
echo "   php artisan config:cache\n";
echo "   php artisan route:cache\n";
echo "   php artisan view:cache\n\n";
echo "4. Set proper permissions:\n";
echo "   chmod -R 775 storage bootstrap/cache\n\n";

echo "Would you like me to run these commands now? (yes/no) [no]: ";
$runCommands = trim(fgets(STDIN));
$runCommands = strtolower($runCommands);

if ($runCommands === 'yes' || $runCommands === 'y') {
    echo "\n";
    echo "Running commands...\n";
    echo "━━━━━━━━━━━━━━━━━━━\n";

    // Load Laravel
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    // Run migrations
    echo "Running migrations...\n";
    $kernel->call('migrate', ['--force' => true]);

    // Clear caches
    echo "\nClearing caches...\n";
    $kernel->call('config:clear');
    $kernel->call('cache:clear');
    $kernel->call('view:clear');
    $kernel->call('route:clear');

    echo "\n✅ All commands executed successfully!\n";

    echo "\nYou can now cache for production:\n";
    echo "   php artisan config:cache\n";
    echo "   php artisan route:cache\n";
    echo "   php artisan view:cache\n";
} else {
    echo "\nPlease run the commands manually (see above).\n";
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Setup complete! Your app should now work in production.\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

echo "💡 Tips:\n";
echo "   • Test in incognito/private browsing mode\n";
echo "   • Check logs: tail -f storage/logs/laravel.log\n";
echo "   • Run diagnostics: php diagnostics.php\n";
echo "   • See FIX_403_ERRORS.md for troubleshooting\n";
echo "\n";

