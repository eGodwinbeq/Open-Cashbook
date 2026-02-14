@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <div class="flex flex-col gap-8">
        <!-- Header -->
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol
                    class="inline-flex items-center space-x-1 md:space-x-3 text-xs font-bold uppercase tracking-widest text-gray-400">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ti ti-chevron-right text-[10px] mx-1"></i>
                            <span class="text-gray-600 dark:text-gray-300">Settings</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-black tracking-tight">Settings</h1>
        </div>

        <!-- Currency Settings -->
        <div
            class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-[#eaeff0] dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="bg-primary/10 p-2 rounded-lg">
                        <i class="ti ti-currency-dollar text-primary text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Currency Settings</h3>
                        <p class="text-sm text-gray-500">Manage your preferred currency symbol</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('settings.currency') }}" class="p-6">
                @csrf
                <div class="max-w-md">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Currency</label>
                    <div class="flex gap-3">
                        <select name="currency_symbol" required
                            class="flex-1 px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 font-semibold">
                            <option value="" disabled>Select a currency</option>
                            <option value="$" {{ auth()->user()->currency_symbol == '$' ? 'selected' : '' }}>$ - US Dollar (USD)</option>
                            <option value="UGX" {{ auth()->user()->currency_symbol == 'UGX' ? 'selected' : '' }}>UGX - Ugandan Shilling</option>
                            <option value="KES" {{ auth()->user()->currency_symbol == 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                            <option value="TZS" {{ auth()->user()->currency_symbol == 'TZS' ? 'selected' : '' }}>TZS - Tanzanian Shilling</option>
                            <option value="€" {{ auth()->user()->currency_symbol == '€' ? 'selected' : '' }}>€ - Euro (EUR)</option>
                            <option value="£" {{ auth()->user()->currency_symbol == '£' ? 'selected' : '' }}>£ - British Pound (GBP)</option>
                            <option value="₹" {{ auth()->user()->currency_symbol == '₹' ? 'selected' : '' }}>₹ - Indian Rupee (INR)</option>
                            <option value="¥" {{ auth()->user()->currency_symbol == '¥' ? 'selected' : '' }}>¥ - Japanese Yen (JPY)</option>
                            <option value="¥" {{ auth()->user()->currency_symbol == '¥' ? 'selected' : '' }}>¥ - Chinese Yuan (CNY)</option>
                            <option value="R" {{ auth()->user()->currency_symbol == 'R' ? 'selected' : '' }}>R - South African Rand (ZAR)</option>
                            <option value="₦" {{ auth()->user()->currency_symbol == '₦' ? 'selected' : '' }}>₦ - Nigerian Naira (NGN)</option>
                            <option value="GH₵" {{ auth()->user()->currency_symbol == 'GH₵' ? 'selected' : '' }}>GH₵ - Ghanaian Cedi (GHS)</option>
                            <option value="ZK" {{ auth()->user()->currency_symbol == 'ZK' ? 'selected' : '' }}>ZK - Zambian Kwacha (ZMW)</option>
                            <option value="RWF" {{ auth()->user()->currency_symbol == 'RWF' ? 'selected' : '' }}>RWF - Rwandan Franc</option>
                            <option value="ETB" {{ auth()->user()->currency_symbol == 'ETB' ? 'selected' : '' }}>ETB - Ethiopian Birr</option>
                            <option value="CA$" {{ auth()->user()->currency_symbol == 'CA$' ? 'selected' : '' }}>CA$ - Canadian Dollar (CAD)</option>
                            <option value="A$" {{ auth()->user()->currency_symbol == 'A$' ? 'selected' : '' }}>A$ - Australian Dollar (AUD)</option>
                            <option value="CHF" {{ auth()->user()->currency_symbol == 'CHF' ? 'selected' : '' }}>CHF - Swiss Franc</option>
                            <option value="₽" {{ auth()->user()->currency_symbol == '₽' ? 'selected' : '' }}>₽ - Russian Ruble (RUB)</option>
                            <option value="R$" {{ auth()->user()->currency_symbol == 'R$' ? 'selected' : '' }}>R$ - Brazilian Real (BRL)</option>
                        </select>
                        <button type="submit"
                            class="px-6 py-3 bg-primary text-white rounded-lg font-bold hover:opacity-90 transition-all">
                            Save
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">This currency will be displayed throughout the app for all monetary
                        values.</p>

                    <div
                        class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex gap-2">
                            <i class="ti ti-info-circle text-blue-600 dark:text-blue-400 flex-shrink-0"></i>
                            <div class="text-sm text-blue-800 dark:text-blue-300">
                                <p class="font-bold mb-1">Popular Currencies Available</p>
                                <p>Select from a list of commonly used currencies including USD, EUR, GBP, and African currencies like UGX, KES, NGN, and more.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Current Preview -->
        <div class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
            <h3 class="font-bold text-lg mb-4">Preview</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                    <p class="text-xs font-bold text-green-600 dark:text-green-400 mb-1">Cash In Example</p>
                    <p class="text-2xl font-black text-green-600 dark:text-green-400">
                        +{{ auth()->user()->currency_symbol }}1,250.00</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800">
                    <p class="text-xs font-bold text-red-600 dark:text-red-400 mb-1">Cash Out Example</p>
                    <p class="text-2xl font-black text-red-600 dark:text-red-400">
                        -{{ auth()->user()->currency_symbol }}850.00</p>
                </div>
            </div>
        </div>
    </div>
@endsection
