@extends('layouts.authentication')

@section('title', 'Create Account')

@section('content')
    <div class="bg-white dark:bg-[#25282c] p-8 rounded-2xl shadow-xl border border-[#eaeff0] dark:border-gray-700">
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-black tracking-tight mb-2">Create Account</h2>
            <p class="text-gray-500 text-sm">Join Open Cashbook to manage your finances</p>
        </div>

        <!-- Error Message -->
        @if (session('error'))
            <div class="mb-4 font-medium text-sm text-red-600 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Google Sign-In (Primary) -->
        <a href="{{ route('auth.google') }}"
            class="w-full py-4 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all active:scale-[0.98] flex items-center justify-center gap-3 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <span>Continue with Google</span>
        </a>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
            </div>
            <div class="relative flex justify-center text-xs">
                <span class="px-3 bg-white dark:bg-[#25282c] text-gray-500 font-medium">Or register with email</span>
            </div>
        </div>

        <!-- Email/Password Form (Secondary) -->
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                <div class="relative">
                    <i class="ti ti-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                        autocomplete="name"
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary/50 transition-all font-medium"
                        placeholder="John Doe">
                </div>
                @if ($errors->has('name'))
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $errors->first('name') }}</p>
                @endif
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email
                    Address</label>
                <div class="relative">
                    <i class="ti ti-mail absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary/50 transition-all font-medium"
                        placeholder="name@company.com">
                </div>
                @if ($errors->has('email'))
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Password</label>
                <div class="relative">
                    <i class="ti ti-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary/50 transition-all font-medium"
                        placeholder="••••••••">
                </div>
                @if ($errors->has('password'))
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $errors->first('password') }}</p>
                @endif
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation"
                    class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                <div class="relative">
                    <i class="ti ti-lock-check absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        autocomplete="new-password"
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary/50 transition-all font-medium"
                        placeholder="••••••••">
                </div>
                @if ($errors->has('password_confirmation'))
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $errors->first('password_confirmation') }}</p>
                @endif
            </div>

            <button type="submit"
                class="w-full py-4 bg-primary text-white rounded-xl font-bold shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                <span>Create Account</span>
                <i class="ti ti-user-plus"></i>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 text-center">
            <p class="text-sm text-gray-500 font-medium">
                Already have an account?
                <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Sign In</a>
            </p>
        </div>
    </div>
@endsection
