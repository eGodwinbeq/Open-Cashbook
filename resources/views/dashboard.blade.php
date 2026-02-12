@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if(!$activeChapter)
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="bg-primary/10 p-6 rounded-full mb-6">
                <i class="ti ti-book-off text-6xl text-primary"></i>
            </div>
            <h2 class="text-3xl font-black mb-2">No Financial Chapter Found</h2>
            <p class="text-gray-500 max-w-md mb-8">Start by creating your first financial chapter to track your cash flow for a
                specific period or purpose.</p>
            <button onclick="openModal('newChapterModal')"
                class="px-8 py-4 bg-primary text-white rounded-xl font-bold shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all">
                Create First Chapter
            </button>
        </div>
    @else
        <!-- Action Buttons & Heading -->
        <div class="flex flex-col gap-4 mb-6 md:mb-10">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl md:text-4xl font-black tracking-tight mb-1 md:mb-2">{{ $activeChapter->name }} Overview
                    </h1>
                    <p class="text-sm md:text-base text-gray-500">
                        {{ $activeChapter->description ?? 'Track and manage your daily cash flow entries with ease.' }}</p>
                </div>
                <div class="hidden md:flex gap-3">
                    <button onclick="openModal('cashInModal')"
                        class="flex items-center gap-2 px-6 h-14 bg-success-muted text-white rounded-xl font-bold shadow-lg shadow-green-500/20 hover:opacity-90 transition-all active:scale-95">
                        <i class="ti ti-circle-plus"></i>
                        <span>Cash In</span>
                    </button>
                    <button onclick="openModal('cashOutModal')"
                        class="flex items-center gap-2 px-6 h-14 bg-danger-muted text-white rounded-xl font-bold shadow-lg shadow-red-500/20 hover:opacity-90 transition-all active:scale-95">
                        <i class="ti ti-circle-minus"></i>
                        <span>Cash Out</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-10">
            <div
                class="bg-white dark:bg-[#25282c] p-4 md:p-6 rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 flex flex-col gap-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs md:text-sm font-bold text-gray-400 uppercase tracking-widest">Total Cash In</span>
                    <div class="p-2 rounded-lg bg-green-50 dark:bg-green-900/20 text-success-muted">
                        <i class="ti ti-trending-up"></i>
                    </div>
                </div>
                <p class="text-2xl md:text-3xl font-black">{{ auth()->user()->currency_symbol }}{{ number_format($totalIn, 2) }}</p>
            </div>

            <div
                class="bg-white dark:bg-[#25282c] p-4 md:p-6 rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 flex flex-col gap-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs md:text-sm font-bold text-gray-400 uppercase tracking-widest">Total Cash Out</span>
                    <div class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-danger-muted">
                        <i class="ti ti-trending-down"></i>
                    </div>
                </div>
                <p class="text-2xl md:text-3xl font-black">{{ auth()->user()->currency_symbol }}{{ number_format($totalOut, 2) }}</p>
            </div>

            <div
                class="bg-primary p-4 md:p-6 rounded-2xl shadow-xl shadow-primary/20 text-white flex flex-col gap-1 sm:col-span-2 md:col-span-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs md:text-sm font-bold text-white/70 uppercase tracking-widest">Net Balance</span>
                    <div class="p-2 rounded-lg bg-white/20 text-white">
                        <i class="ti ti-wallet"></i>
                    </div>
                </div>
                <p class="text-2xl md:text-3xl font-black">{{ auth()->user()->currency_symbol }}{{ number_format($balance, 2) }}</p>
            </div>
        </div>

        <!-- Transaction Table Container -->
        <div
            class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 overflow-hidden">
            <div
                class="px-4 md:px-6 py-4 md:py-5 border-b border-[#eaeff0] dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-bold text-base md:text-lg">Recent Transactions</h3>
                <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                    <span>View All</span>
                    <i class="ti ti-chevron-right"></i>
                </a>
            </div>

            @if($transactions->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-gray-400 font-bold italic">No transactions recorded in this chapter yet.</p>
                </div>
            @else
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-gray-800/50 text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50 text-sm">
                            @foreach($transactions as $transaction)
                                <tr class="transaction-row transition-all group">
                                    <td class="px-6 py-5 font-medium text-gray-500">
                                        {{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</td>
                                    <td class="px-6 py-5 font-bold">{{ $transaction->description }}</td>
                                    <td class="px-6 py-5">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                {{ $transaction->type == 'in' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                            {{ $transaction->category ?? 'General' }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-5 text-right font-black {{ $transaction->type == 'in' ? 'text-success-muted' : 'text-danger-muted' }}">
                                        {{ $transaction->type == 'in' ? '+' : '-' }}{{ auth()->user()->currency_symbol }}{{ number_format($transaction->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile List View -->
                <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700/50">
                    @foreach($transactions as $transaction)
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex flex-col gap-1 min-w-0">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('M d') }}
                                </p>
                                <p class="font-bold text-sm truncate">{{ $transaction->description }}</p>
                                <span class="w-fit px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $transaction->type == 'in' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                    {{ $transaction->category ?? 'General' }}
                                </span>
                            </div>
                            <p class="font-black text-base {{ $transaction->type == 'in' ? 'text-success-muted' : 'text-danger-muted' }}">
                                {{ $transaction->type == 'in' ? '+' : '-' }}{{ auth()->user()->currency_symbol }}{{ number_format($transaction->amount, 2) }}
                            </p>
                        </div>
                    @endforeach
                    <div class="p-4">
                        <a href="{{ route('transactions.index') }}" class="w-full flex items-center justify-center gap-2 py-3 bg-gray-50 dark:bg-gray-800 text-primary font-bold rounded-xl text-sm">
                            <span>View All Transactions</span>
                            <i class="ti ti-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/30">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

        @push('transaction-modals')
            @include('partials.onboarding-modal')
            @include('partials.cash-in-modal')
            @include('partials.cash-out-modal')
        @endpush

        @push('mobile-actions')
            <!-- Sticky Mobile Action Buttons (only on mobile) -->
            <div class="md:hidden mobile-sticky-actions">
                <div class="flex gap-3 max-w-[1200px] mx-auto bg-white/70 dark:bg-gray-900/70 backdrop-blur-sm border border-[#eaeff0]/60 dark:border-gray-700/40 rounded-xl p-3 shadow-lg dark:shadow-black/40">
                    <button onclick="openModal('cashInModal')"
                        class="flex-1 flex items-center justify-center gap-2 h-14 bg-success-muted dark:bg-success-muted text-white rounded-xl font-bold shadow-lg shadow-green-500/30 dark:shadow-green-500/20 hover:opacity-90 active:scale-95 transition-all">
                        <i class="ti ti-circle-plus text-xl"></i>
                        <span>Cash In</span>
                    </button>
                    <button onclick="openModal('cashOutModal')"
                        class="flex-1 flex items-center justify-center gap-2 h-14 bg-danger-muted dark:bg-danger-muted text-white rounded-xl font-bold shadow-lg shadow-red-500/30 dark:shadow-red-500/20 hover:opacity-90 active:scale-95 transition-all">
                        <i class="ti ti-circle-minus text-xl"></i>
                        <span>Cash Out</span>
                    </button>
                </div>
            </div>
        @endpush
    @endif
@endsection
