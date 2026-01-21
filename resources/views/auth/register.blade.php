@extends('layouts.authentication')

@section('title', 'Create Account')

@section('content')
    <div class="bg-white dark:bg-[#25282c] p-8 rounded-2xl shadow-xl border border-[#eaeff0] dark:border-gray-700">
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-black tracking-tight mb-2">Create Account</h2>
            <p class="text-gray-500 text-sm">Join Open Cashbook to manage your finances</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                <div class="relative">
                    <i class="ti ti-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
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
