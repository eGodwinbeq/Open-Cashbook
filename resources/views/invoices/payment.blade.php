@extends('layouts.app')

@section('title', 'Process Payment')

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
                            <a href="{{ route('invoices.index') }}"
                                class="hover:text-primary transition-colors">Invoices</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ti ti-chevron-right text-[10px] mx-1"></i>
                            <a href="{{ route('invoices.show', $invoice) }}"
                                class="hover:text-primary transition-colors">Invoice #{{ $invoice->invoice_number }}</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ti ti-chevron-right text-[10px] mx-1"></i>
                            <span class="text-gray-600 dark:text-gray-300">Add Payment</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-black tracking-tight">Process Payment</h1>
            <p class="text-gray-500 mt-1">Invoice #{{ $invoice->invoice_number }}</p>
        </div>

        <!-- Invoice Summary -->
        <div class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold mb-4">Invoice Summary</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Client</label>
                    <p class="text-base font-semibold">{{ $invoice->client_name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Amount</label>
                    <p class="text-base font-semibold">
                        {{ auth()->user()->currency_symbol }}{{ number_format($invoice->total_amount, 2) }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Paid Amount</label>
                    <p class="text-base font-semibold text-green-600">
                        {{ auth()->user()->currency_symbol }}{{ number_format($invoice->paid_amount, 2) }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Balance Due</label>
                    <p class="text-base font-semibold text-red-600">
                        {{ auth()->user()->currency_symbol }}{{ number_format($invoice->balance_due, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div
            class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-[#eaeff0] dark:border-gray-700">
                <h3 class="font-bold text-lg">Add Payment</h3>
            </div>

            <form method="POST" action="{{ route('invoices.process-payment', $invoice) }}" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="amount" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Payment
                            Amount</label>
                        <div class="relative">
                            <span
                                class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-500 font-bold">{{ auth()->user()->currency_symbol }}</span>
                            <input type="number" step="0.01" max="{{ $invoice->balance_due }}" name="amount" id="amount"
                                value="{{ old('amount', $invoice->balance_due) }}" required
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 font-semibold">
                        </div>
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_method"
                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                        <select name="payment_method" id="payment_method" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 font-semibold">
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>
                                Bank Transfer</option>
                            <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                            <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit
                                Card</option>
                            <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_date"
                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Payment Date</label>
                        <input type="date" name="payment_date" id="payment_date"
                            value="{{ old('payment_date', now()->format('Y-m-d')) }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 font-semibold">
                        @error('payment_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="reference_number"
                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Reference Number
                            (Optional)</label>
                        <input type="text" name="reference_number" id="reference_number"
                            value="{{ old('reference_number') }}" placeholder="e.g., CHK-12345"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 font-semibold">
                        @error('reference_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="notes" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Notes
                        (Optional)</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Add any additional payment details..."
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 resize-none">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex gap-3">
                    <a href="{{ route('invoices.show', $invoice) }}"
                        class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                        Cancel
                    </a>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-primary text-white rounded-xl font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
                        Process Payment
                    </button>
                </div>
            </form>
        </div>

        <!-- Payment History -->
        @if($invoice->payments->count() > 0)
            <div
                class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-[#eaeff0] dark:border-gray-700">
                    <h3 class="font-bold text-lg">Payment History</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50/50 dark:bg-gray-800/50 text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Method</th>
                                <th class="px-6 py-4">Reference</th>
                                <th class="px-6 py-4">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50 text-sm">
                            @foreach($invoice->payments as $payment)
                                <tr class="transition-all hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                    <td class="px-6 py-5 font-medium text-gray-500">
                                        {{ $payment->payment_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-5 font-bold text-green-600">
                                        {{ auth()->user()->currency_symbol }}{{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-600">
                                            {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 font-medium">
                                        {{ $payment->reference_number ?? '-' }}
                                    </td>
                                    <td class="px-6 py-5">
                                        @if($payment->receipt)
                                            <a href="{{ route('receipts.download', $payment->receipt) }}"
                                                class="text-primary hover:underline font-bold">
                                                <i class="ti ti-download"></i> Download
                                            </a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection