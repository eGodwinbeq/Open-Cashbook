<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ config('app.name', 'Open Cashbook') }} - @yield('title', 'Authentication')</title>
    <link rel="icon" type="image/png" href="{{ asset('Open_Cashbook_Logo.png') }}" />

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
            <img src="{{ asset('Open_Cashbook_Logo.png') }}"
                 alt="Open Cashbook Logo"
                 class="h-20 w-auto">
        </div>

        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>
