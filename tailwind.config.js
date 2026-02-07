/**
 * Tailwind CSS v4 Configuration
 *
 * In Tailwind CSS v4, most configuration is done in your CSS file using @theme and @plugin directives.
 * See resources/css/app.css for theme customization.
 *
 * This file is kept for compatibility but most settings are now in CSS.
 */

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
};
