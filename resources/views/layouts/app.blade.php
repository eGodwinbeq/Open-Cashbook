<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ config('app.name', 'VaultFlow') }} - @yield('title', 'Cashbook Dashboard')</title>
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
                        "success-muted": "#4B8E65",
                        "danger-muted": "#D64545"
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

        .transaction-row:hover {
            background-color: rgba(52, 116, 141, 0.03);
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 50;
            backdrop-filter: blur(4px);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
    @stack('styles')
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-[#111618] dark:text-gray-100 min-h-screen">

    <!-- Mobile Menu Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden" onclick="toggleSidebar()"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <aside id="sidebar"
            class="sidebar w-72 bg-white dark:bg-[#25282c] border-r border-[#eaeff0] dark:border-gray-700 flex flex-col fixed h-full z-40">
            <div class="p-6 flex flex-col h-full">
                <div class="flex items-center justify-between mb-10">
                    <div class="flex items-center gap-3">
                        <div class="bg-primary rounded-lg p-2 text-white">
                            <i class="ti ti-wallet text-2xl"></i>
                        </div>
                        <h1 class="text-xl font-extrabold tracking-tight">VaultFlow</h1>
                    </div>
                    <button class="md:hidden text-gray-500" onclick="toggleSidebar()">
                        <i class="ti ti-x text-2xl"></i>
                    </button>
                </div>

                <div class="flex flex-col gap-1 mb-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest px-3 mb-2">My Cashbooks</p>
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary">
                        <i class="ti ti-book-2"></i>
                        <p class="text-sm font-semibold">Daily Expenses</p>
                    </div>
                    <div
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                        <i class="ti ti-briefcase"></i>
                        <p class="text-sm font-medium">Business Revenue</p>
                    </div>
                    <div
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                        <i class="ti ti-plane"></i>
                        <p class="text-sm font-medium">Travel Fund</p>
                    </div>
                    <div
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer">
                        <i class="ti ti-home"></i>
                        <p class="text-sm font-medium">Home Savings</p>
                    </div>
                </div>

                <div class="mt-auto pt-6 border-t border-gray-100 dark:border-gray-700">
                    <button
                        class="w-full flex items-center justify-center gap-2 bg-primary text-white font-bold py-3 rounded-xl shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all">
                        <i class="ti ti-plus"></i>
                        <span>New Cashbook</span>
                    </button>
                    <div class="mt-8 flex items-center gap-3 px-3 py-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                        <img class="size-10 rounded-full object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDz5xul2S5jv19wqK0Oln5CD6nlLUwJRN77TecgYJGTvMo2sGwKmd0t5PwV9Qn4gsikE_JKJ4BYMkwt9_u9-9oZ74E566eXLuE71b5fQSaCnhYfupvuoGHuq1TGgwfjBEJ77y1qiuWrkAabCS4utVUKDgxfp9evRpaMQ9K7EunduzHiyT4SkROchogQG38FSOmoMV7Rn2kuKcO6M9FPy718jguPgR2JKlB70KAu2c4VfliDPf3gRcphpeBIlqYor016L6cC0Fq_dWe7"
                            alt="User profile" />
                        <div class="flex flex-col">
                            <p class="text-sm font-bold truncate max-w-[120px]">Alex Thompson</p>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Premium Plan</p>
                        </div>
                        <i class="ti ti-settings text-gray-400 ml-auto cursor-pointer"></i>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="md:ml-72 flex-1 flex flex-col w-full">
            <!-- Top Navbar -->
            <header
                class="h-16 md:h-20 bg-white/80 dark:bg-[#1c1e22]/80 backdrop-blur-md border-b border-[#eaeff0] dark:border-gray-700 px-4 md:px-8 flex items-center justify-between sticky top-0 z-10">
                <div class="flex items-center gap-3 md:gap-4">
                    <button class="md:hidden text-gray-600 dark:text-gray-300" onclick="toggleSidebar()">
                        <i class="ti ti-menu-2 text-2xl"></i>
                    </button>
                    <h2 class="text-lg md:text-xl font-bold tracking-tight">@yield('page_title', 'Daily Expenses')</h2>
                    <span
                        class="hidden sm:inline bg-gray-100 dark:bg-gray-800 text-gray-500 text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-widest">Active</span>
                </div>

                <div class="flex items-center gap-2 md:gap-6">
                    <div class="hidden md:block relative w-64">
                        <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input
                            class="w-full pl-10 pr-4 py-2 bg-gray-100 dark:bg-gray-800 border-none rounded-lg focus:ring-2 focus:ring-primary/50 text-sm transition-all"
                            placeholder="Search transactions..." type="text" />
                    </div>
                    <div class="flex gap-2">
                        <button
                            class="p-2 md:p-2.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors relative">
                            <i class="ti ti-bell text-xl"></i>
                            <span
                                class="absolute top-1.5 right-1.5 size-2 bg-red-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                        </button>
                    </div>
                </div>
            </header>

            <div class="p-4 md:p-8 max-w-[1200px] mx-auto w-full">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('hidden');
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal(modal.id);
                }
            });
        });

        // Close modals with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal.active').forEach(modal => {
                    closeModal(modal.id);
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>