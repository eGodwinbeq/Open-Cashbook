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

            <div class="md:hidden flex gap-3">
                <button onclick="openModal('cashInModal')"
                    class="flex-1 flex items-center justify-center gap-2 h-12 bg-success-muted text-white rounded-xl font-bold shadow-lg active:scale-95">
                    <i class="ti ti-circle-plus"></i>
                    <span>In</span>
                </button>
                <button onclick="openModal('cashOutModal')"
                    class="flex-1 flex items-center justify-center gap-2 h-12 bg-danger-muted text-white rounded-xl font-bold shadow-lg active:scale-95">
                    <i class="ti ti-circle-minus"></i>
                    <span>Out</span>
                </button>
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
                <p class="text-2xl md:text-3xl font-black">${{ number_format($totalIn, 2) }}</p>
            </div>

            <div
                class="bg-white dark:bg-[#25282c] p-4 md:p-6 rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 flex flex-col gap-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs md:text-sm font-bold text-gray-400 uppercase tracking-widest">Total Cash Out</span>
                    <div class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-danger-muted">
                        <i class="ti ti-trending-down"></i>
                    </div>
                </div>
                <p class="text-2xl md:text-3xl font-black">${{ number_format($totalOut, 2) }}</p>
            </div>

            <div
                class="bg-primary p-4 md:p-6 rounded-2xl shadow-xl shadow-primary/20 text-white flex flex-col gap-1 sm:col-span-2 md:col-span-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs md:text-sm font-bold text-white/70 uppercase tracking-widest">Net Balance</span>
                    <div class="p-2 rounded-lg bg-white/20 text-white">
                        <i class="ti ti-wallet"></i>
                    </div>
                </div>
                <p class="text-2xl md:text-3xl font-black">${{ number_format($balance, 2) }}</p>
            </div>
        </div>

        <!-- Transaction Table Container -->
        <div
            class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 overflow-hidden">
            <div
                class="px-4 md:px-6 py-4 md:py-5 border-b border-[#eaeff0] dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-bold text-base md:text-lg">Recent Transactions</h3>
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
                                        {{ $transaction->type == 'in' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/30">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

        <!-- Cash In Modal -->
        <div id="cashInModal" class="modal">
            <div
                class="modal-content bg-white dark:bg-[#25282c] rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('transactions.store', $activeChapter) }}">
                    @csrf
                    <input type="hidden" name="type" value="in">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-success-muted/10 p-2 rounded-lg">
                                <i class="ti ti-circle-plus text-success-muted text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold">Add Cash In</h3>
                        </div>
                        <button type="button" onclick="closeModal('cashInModal')" class="text-gray-400 hover:text-gray-600">
                            <i class="ti ti-x text-xl"></i>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Amount</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
                                <input type="number" name="amount" step="0.01" placeholder="0.00" required
                                    class="w-full pl-8 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50 font-semibold text-lg" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <input type="text" name="description" placeholder="e.g., Client payment" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <input type="text" name="category" placeholder="Freelance, Salary, etc."
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Date</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50" />
                        </div>
                    </div>
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                        <button type="button" onclick="closeModal('cashInModal')"
                            class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-bold">Cancel</button>
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-success-muted text-white rounded-xl font-bold shadow-lg shadow-green-500/20 hover:opacity-90 transition-all">Add
                            Entry</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cash Out Modal -->
        <div id="cashOutModal" class="modal">
            <div
                class="modal-content bg-white dark:bg-[#25282c] rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('transactions.store', $activeChapter) }}">
                    @csrf
                    <input type="hidden" name="type" value="out">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-danger-muted/10 p-2 rounded-lg">
                                <i class="ti ti-circle-minus text-danger-muted text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold">Add Cash Out</h3>
                        </div>
                        <button type="button" onclick="closeModal('cashOutModal')" class="text-gray-400 hover:text-gray-600">
                            <i class="ti ti-x text-xl"></i>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Amount</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
                                <input type="number" name="amount" step="0.01" placeholder="0.00" required
                                    class="w-full pl-8 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-danger-muted/50 font-semibold text-lg" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <input type="text" name="description" placeholder="e.g., Grocery shopping" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-danger-muted/50" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <input type="text" name="category" placeholder="Food, Rent, etc."
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-danger-muted/50" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Date</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-danger-muted/50" />
                        </div>
                    </div>
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                        <button type="button" onclick="closeModal('cashOutModal')"
                            class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-bold">Cancel</button>
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-danger-muted text-white rounded-xl font-bold shadow-lg shadow-red-500/20 hover:opacity-90 transition-all">Add
                            Expense</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection