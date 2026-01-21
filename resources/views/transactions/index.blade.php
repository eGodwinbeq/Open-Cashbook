@extends('layouts.app')

@section('title', 'Transaction History')

@section('content')
    <div class="flex flex-col gap-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
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
                                <span class="text-gray-600 dark:text-gray-300">Transactions</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black tracking-tight">{{ $activeChapter->name }} Transactions</h1>
            </div>

            <div class="hidden md:flex gap-3">
                <button onclick="openModal('cashInModal')"
                    class="flex items-center gap-2 px-6 h-12 bg-success-muted text-white rounded-xl font-bold shadow-lg shadow-green-500/20 hover:opacity-90 transition-all active:scale-95">
                    <i class="ti ti-circle-plus"></i>
                    <span>Cash In</span>
                </button>
                <button onclick="openModal('cashOutModal')"
                    class="flex items-center gap-2 px-6 h-12 bg-danger-muted text-white rounded-xl font-bold shadow-lg shadow-red-500/20 hover:opacity-90 transition-all active:scale-95">
                    <i class="ti ti-circle-minus"></i>
                    <span>Cash Out</span>
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
            <form action="{{ route('transactions.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Type</label>
                    <select name="type" onchange="this.form.submit()"
                        class="w-full bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-lg focus:ring-primary font-bold text-sm">
                        <option value="">All Types</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Cash In (+)</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Cash Out (-)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Category</label>
                    <input type="text" name="category" value="{{ request('category') }}" placeholder="Search category..."
                        class="w-full bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-lg focus:ring-primary font-bold text-sm">
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">From Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-lg focus:ring-primary font-bold text-sm">
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">To Date</label>
                    <div class="flex gap-2">
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-lg focus:ring-primary font-bold text-sm">
                        <button type="submit" class="bg-primary text-white p-2 rounded-lg hover:opacity-90 transition-all">
                            <i class="ti ti-search text-xl"></i>
                        </button>
                        <a href="{{ route('transactions.index') }}"
                            class="bg-gray-100 dark:bg-gray-800 text-gray-500 p-2 rounded-lg hover:bg-gray-200 transition-all"
                            title="Reset Filters">
                            <i class="ti ti-refresh text-xl"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Transactions Table -->
        <div
            class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 overflow-hidden">
            @if($transactions->isEmpty())
                <div class="p-20 text-center">
                    <div class="bg-gray-50 dark:bg-gray-800 inline-block p-4 rounded-full mb-4">
                        <i class="ti ti-search text-4xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-400 font-bold italic">No transactions found matching your filters.</p>
                </div>
            @else
                <div class="overflow-x-auto">
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
                                        {{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}
                                    </td>
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
                                        {{ $transaction->type == 'in' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/30 border-t border-[#eaeff0] dark:border-gray-700">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('transaction-modals')
        @include('partials.cash-in-modal')
        @include('partials.cash-out-modal')
    @endpush

        <!-- Sticky Mobile Action Buttons (only on mobile) -->
        @push('mobile-actions')
            <!-- Sticky Mobile Action Buttons (only on mobile) -->
            <div class="md:hidden mobile-sticky-actions ">
                <div
                    class="flex gap-3 max-w-[1200px] mx-auto bg-white/70 dark:bg-gray-900/70 backdrop-blur-sm border border-[#eaeff0]/60 dark:border-gray-700/40 rounded-xl p-3 shadow-lg dark:shadow-black/40">
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

    <!-- Modals are inherited from layout -->
@endsection
