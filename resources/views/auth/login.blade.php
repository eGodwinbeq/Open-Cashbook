@extends('layouts.authentication')

@section('title', 'Login')

@section('content')
    <div class="bg-white dark:bg-[#25282c] p-8 rounded-2xl shadow-xl border border-[#eaeff0] dark:border-gray-700">
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-black tracking-tight mb-2">Welcome Back</h2>
            <p class="text-gray-500 text-sm">Sign in to your account to continue</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email
                    Address</label>
                <div class="relative">
                    <i class="ti ti-mail absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary/50 transition-all font-medium"
                        placeholder="name@company.com">
                </div>
                @error('email')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-primary hover:underline">Forgot
                            password?</a>
                    @endif
                </div>
                <div class="relative">
                    <i class="ti ti-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="password" type="password" name="password" required
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-primary/50 transition-all font-medium"
                        placeholder="••••••••">
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox"
                    class="size-4 rounded border-gray-300 text-primary focus:ring-primary/50">
                <label for="remember" class="ml-2 block text-sm font-medium text-gray-500">Remember me for 30 days</label>
            </div>

            <button type="submit"
                class="w-full py-4 bg-primary text-white rounded-xl font-bold shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                <span>Sign In</span>
                <i class="ti ti-arrow-right"></i>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 text-center">
            <p class="text-sm text-gray-500 font-medium">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-primary font-bold hover:underline">Create Account</a>
            </p>
        </div>
    </div>
@endsection