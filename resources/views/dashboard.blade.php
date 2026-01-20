@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Daily Expenses')

@section('content')
    <!-- Action Buttons & Heading -->
    <div class="flex flex-col gap-4 mb-6 md:mb-10">
        <div>
            <h1 class="text-2xl md:text-4xl font-black tracking-tight mb-1 md:mb-2">Cash Overview</h1>
            <p class="text-sm md:text-base text-gray-500">Track and manage your daily cash flow entries with ease.</p>
        </div>

        <!-- Month Filter -->
        <div class="flex flex-wrap gap-2 md:gap-3">
            <select
                class="px-3 py-2 bg-gray-100 dark:bg-gray-800 border-none rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary/50">
                <option>January 2026</option>
                <option>December 2025</option>
                <option>November 2025</option>
                <option>October 2025</option>
                <option>September 2025</option>
            </select>
            <button
                class="px-3 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors flex items-center gap-1.5">
                <i class="ti ti-calendar"></i>
                <span class="hidden sm:inline">Custom Range</span>
            </button>
        </div>

        <div class="flex gap-3 md:gap-4">
            <button onclick="openModal('cashInModal')"
                class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 md:px-6 h-12 md:h-14 bg-success-muted text-white rounded-xl font-bold shadow-lg shadow-green-500/20 hover:opacity-90 transition-all active:scale-95">
                <i class="ti ti-circle-plus"></i>
                <span class="text-sm md:text-base">Cash In</span>
            </button>
            <button onclick="openModal('cashOutModal')"
                class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 md:px-6 h-12 md:h-14 bg-danger-muted text-white rounded-xl font-bold shadow-lg shadow-red-500/20 hover:opacity-90 transition-all active:scale-95">
                <i class="ti ti-circle-minus"></i>
                <span class="text-sm md:text-base">Cash Out</span>
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
            <p class="text-2xl md:text-3xl font-black">$12,450.80</p>
            <div class="flex items-center gap-1.5 mt-2">
                <span class="text-success-muted font-bold text-xs">+12.5%</span>
                <span class="text-gray-400 text-[10px] font-medium uppercase tracking-wider">from last month</span>
            </div>
        </div>

        <div
            class="bg-white dark:bg-[#25282c] p-4 md:p-6 rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 flex flex-col gap-1">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs md:text-sm font-bold text-gray-400 uppercase tracking-widest">Total Cash Out</span>
                <div class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-danger-muted">
                    <i class="ti ti-trending-down"></i>
                </div>
            </div>
            <p class="text-2xl md:text-3xl font-black">$4,210.45</p>
            <div class="flex items-center gap-1.5 mt-2">
                <span class="text-danger-muted font-bold text-xs">-4.2%</span>
                <span class="text-gray-400 text-[10px] font-medium uppercase tracking-wider">from last month</span>
            </div>
        </div>

        <div
            class="bg-primary p-4 md:p-6 rounded-2xl shadow-xl shadow-primary/20 text-white flex flex-col gap-1 sm:col-span-2 md:col-span-1">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs md:text-sm font-bold text-white/70 uppercase tracking-widest">Net Balance</span>
                <div class="p-2 rounded-lg bg-white/20 text-white">
                    <i class="ti ti-wallet"></i>
                </div>
            </div>
            <p class="text-2xl md:text-3xl font-black">$8,240.35</p>
            <div class="flex items-center gap-1.5 mt-2">
                <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Available
                    Funds</span>
            </div>
        </div>
    </div>

    <!-- Transaction Table Container -->
    <div
        class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 overflow-hidden">
        <div
            class="px-4 md:px-6 py-4 md:py-5 border-b border-[#eaeff0] dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-bold text-base md:text-lg">Recent Transactions</h3>
            <div class="flex items-center gap-2 md:gap-3">
                <button
                    class="flex items-center gap-1.5 px-2 md:px-3 py-1.5 bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-bold border border-gray-100 dark:border-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="ti ti-filter text-sm"></i>
                    <span class="hidden sm:inline">Filters</span>
                </button>
                <button
                    class="flex items-center gap-1.5 px-2 md:px-3 py-1.5 bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-bold border border-gray-100 dark:border-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="ti ti-download text-sm"></i>
                    <span class="hidden sm:inline">Export</span>
                </button>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700/50">
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-bold text-sm mb-1">Client Project Payment</p>
                        <p class="text-xs text-gray-500">Oct 24, 2023</p>
                    </div>
                    <p class="font-black text-success-muted">+$3,200.00</p>
                </div>
                <span
                    class="px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[10px] font-bold uppercase">Freelance</span>
            </div>

            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-bold text-sm mb-1">Starbucks Coffee</p>
                        <p class="text-xs text-gray-500">Oct 23, 2023</p>
                    </div>
                    <p class="font-black text-danger-muted">-$12.45</p>
                </div>
                <span
                    class="px-2 py-0.5 rounded-full bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 text-[10px] font-bold uppercase">Food
                    & Beverage</span>
            </div>

            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-bold text-sm mb-1">Monthly Rent Payment</p>
                        <p class="text-xs text-gray-500">Oct 22, 2023</p>
                    </div>
                    <p class="font-black text-danger-muted">-$1,800.00</p>
                </div>
                <span
                    class="px-2 py-0.5 rounded-full bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 text-[10px] font-bold uppercase">Housing</span>
            </div>

            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-bold text-sm mb-1">Dividends Distribution</p>
                        <p class="text-xs text-gray-500">Oct 21, 2023</p>
                    </div>
                    <p class="font-black text-success-muted">+$450.25</p>
                </div>
                <span
                    class="px-2 py-0.5 rounded-full bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400 text-[10px] font-bold uppercase">Investment</span>
            </div>

            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-bold text-sm mb-1">Whole Foods Market</p>
                        <p class="text-xs text-gray-500">Oct 20, 2023</p>
                    </div>
                    <p class="font-black text-danger-muted">-$156.80</p>
                </div>
                <span
                    class="px-2 py-0.5 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-[10px] font-bold uppercase">Groceries</span>
            </div>
        </div>

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
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50 text-sm">
                    <tr class="transaction-row transition-all group">
                        <td class="px-6 py-5 font-medium text-gray-500">Oct 24, 2023</td>
                        <td class="px-6 py-5 font-bold">Client Project Payment</td>
                        <td class="px-6 py-5">
                            <span
                                class="px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[10px] font-bold uppercase tracking-wider">Freelance</span>
                        </td>
                        <td class="px-6 py-5 text-right font-black text-success-muted">+$3,200.00</td>
                        <td class="px-6 py-5 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="text-gray-400 hover:text-primary"><i class="ti ti-dots-vertical"></i></button>
                        </td>
                    </tr>
                    <tr class="transaction-row transition-all group">
                        <td class="px-6 py-5 font-medium text-gray-500">Oct 23, 2023</td>
                        <td class="px-6 py-5 font-bold">Starbucks Coffee</td>
                        <td class="px-6 py-5">
                            <span
                                class="px-2.5 py-1 rounded-full bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 text-[10px] font-bold uppercase tracking-wider">Food
                                & Beverage</span>
                        </td>
                        <td class="px-6 py-5 text-right font-black text-danger-muted">-$12.45</td>
                        <td class="px-6 py-5 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="text-gray-400 hover:text-primary"><i class="ti ti-dots-vertical"></i></button>
                        </td>
                    </tr>
                    <tr class="transaction-row transition-all group">
                        <td class="px-6 py-5 font-medium text-gray-500">Oct 22, 2023</td>
                        <td class="px-6 py-5 font-bold">Monthly Rent Payment</td>
                        <td class="px-6 py-5">
                            <span
                                class="px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 text-[10px] font-bold uppercase tracking-wider">Housing</span>
                        </td>
                        <td class="px-6 py-5 text-right font-black text-danger-muted">-$1,800.00</td>
                        <td class="px-6 py-5 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="text-gray-400 hover:text-primary"><i class="ti ti-dots-vertical"></i></button>
                        </td>
                    </tr>
                    <tr class="transaction-row transition-all group">
                        <td class="px-6 py-5 font-medium text-gray-500">Oct 21, 2023</td>
                        <td class="px-6 py-5 font-bold">Dividends Distribution</td>
                        <td class="px-6 py-5">
                            <span
                                class="px-2.5 py-1 rounded-full bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400 text-[10px] font-bold uppercase tracking-wider">Investment</span>
                        </td>
                        <td class="px-6 py-5 text-right font-black text-success-muted">+$450.25</td>
                        <td class="px-6 py-5 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="text-gray-400 hover:text-primary"><i class="ti ti-dots-vertical"></i></button>
                        </td>
                    </tr>
                    <tr class="transaction-row transition-all group">
                        <td class="px-6 py-5 font-medium text-gray-500">Oct 20, 2023</td>
                        <td class="px-6 py-5 font-bold">Whole Foods Market</td>
                        <td class="px-6 py-5">
                            <span
                                class="px-2.5 py-1 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-[10px] font-bold uppercase tracking-wider">Groceries</span>
                        </td>
                        <td class="px-6 py-5 text-right font-black text-danger-muted">-$156.80</td>
                        <td class="px-6 py-5 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="text-gray-400 hover:text-primary"><i class="ti ti-dots-vertical"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="px-4 md:px-6 py-4 bg-gray-50 dark:bg-gray-800/30 flex items-center justify-between">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Showing 5 of 248</p>
            <div class="flex gap-2">
                <button
                    class="p-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 text-gray-400">
                    <i class="ti ti-chevron-left text-sm"></i>
                </button>
                <button
                    class="p-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 text-gray-400">
                    <i class="ti ti-chevron-right text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Footer Summary Info -->
    <div class="mt-6 md:mt-8 flex justify-center">
        <div
            class="flex flex-wrap items-center justify-center gap-4 md:gap-8 py-4 px-6 md:px-10 bg-gray-100 dark:bg-gray-800/50 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
            <div class="text-center">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Books Managed</p>
                <p class="text-lg font-black">12</p>
            </div>
            <div class="h-8 w-px bg-gray-300 dark:bg-gray-700 hidden sm:block"></div>
            <div class="text-center">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Annual Revenue</p>
                <p class="text-lg font-black text-success-muted">$142,400</p>
            </div>
            <div class="h-8 w-px bg-gray-300 dark:bg-gray-700 hidden sm:block"></div>
            <div class="text-center">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Efficiency Score</p>
                <p class="text-lg font-black text-primary">94%</p>
            </div>
        </div>
    </div>

    <!-- Cash In Modal -->
    <div id="cashInModal" class="modal">
        <div
            class="modal-content bg-white dark:bg-[#25282c] rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-success-muted/10 p-2 rounded-lg">
                        <i class="ti ti-circle-plus text-success-muted text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold">Add Cash In</h3>
                </div>
                <button onclick="closeModal('cashInModal')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="ti ti-x text-xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Amount</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
                        <input type="number" step="0.01" placeholder="0.00"
                            class="w-full pl-8 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50 font-semibold text-lg" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <input type="text" placeholder="e.g., Client payment, Salary"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50">
                        <option>Freelance</option>
                        <option>Salary</option>
                        <option>Investment</option>
                        <option>Gift</option>
                        <option>Refund</option>
                        <option>Other Income</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Date</label>
                    <input type="date"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                    <textarea rows="2" placeholder="Add any additional details..."
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50 resize-none"></textarea>
                </div>
            </div>

            <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                <button onclick="closeModal('cashInModal')"
                    class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                    Cancel
                </button>
                <button onclick="closeModal('cashInModal')"
                    class="flex-1 px-6 py-3 bg-success-muted text-white rounded-xl font-bold shadow-lg shadow-green-500/20 hover:opacity-90 transition-all">
                    Add Entry
                </button>
            </div>
        </div>
    </div>

    <!-- Cash Out Modal -->
    <div id="cashOutModal" class="modal">
        <div
            class="modal-content bg-white dark:bg-[#25282c] rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-danger-muted/10 p-2 rounded-lg">
                        <i class="ti ti-circle-minus text-danger-muted text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold">Add Cash Out</h3>
                </div>
                <button onclick="closeModal('cashOutModal')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="ti ti-x text-xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Amount</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
                        <input type="number" step="0.01" placeholder="0.00"
                            class="w-full pl-8 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-danger-muted/50 font-semibold text-lg" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <input type="text" placeholder="e.g., Grocery shopping, Rent"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-danger-muted/50" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-danger-muted/50">
                        <option>Food & Beverage</option>
                        <option>Housing</option>
                        <option>Transportation</option>
                        <option>Groceries</option>
                        <option>Entertainment</option>
                        <option>Healthcare</option>
                        <option>Utilities</option>
                        <option>Other Expenses</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Date</label>
                    <input type="date"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-danger-muted/50" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                    <textarea rows="2" placeholder="Add any additional details..."
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-danger-muted/50 resize-none"></textarea>
                </div>
            </div>

            <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                <button onclick="closeModal('cashOutModal')"
                    class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                    Cancel
                </button>
                <button onclick="closeModal('cashOutModal')"
                    class="flex-1 px-6 py-3 bg-danger-muted text-white rounded-xl font-bold shadow-lg shadow-red-500/20 hover:opacity-90 transition-all">
                    Add Expense
                </button>
            </div>
        </div>
    </div>
@endsection