<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ config('app.name', 'VaultFlow') }} - @yield('title', 'Authentication')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#34748d",
                        "background-light": "#f9fafb",
                        "background-dark": "#1c1e22",
                    },
                    fontFamily: {
                        "display": ["Manrope", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Manrope', sans-serif;
        }
    </style>
    @stack('styles')
</head>

<body
    class="bg-background-light dark:bg-background-dark font-display text-[#111618] dark:text-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="bg-primary rounded-lg p-2 text-white">
                <i class="ti ti-wallet text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight">VaultFlow</h1>
        </div>

        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>