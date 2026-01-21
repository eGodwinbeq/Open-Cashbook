<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ config('app.name', 'Open Cashbook') }} - @yield('title', 'Authentication')</title>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />

    <style>
        body {
            font-family: 'Manrope', sans-serif;
        }
    </style>
    @stack('styles')
</head>

<body
    class="bg-background-light font-display text-[#111618] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="bg-primary rounded-lg p-2 text-white">
                <i class="ti ti-wallet text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight">Open Cashbook</h1>
        </div>

        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>
