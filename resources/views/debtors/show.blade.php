@extends('layouts.app')
@section('title', 'Debtor Details')

@section('content')
<div class="mb-6">
    <nav class="flex mb-2" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-xs font-bold uppercase tracking-widest text-gray-400">
            <li class="inline-flex items-center">
                <a href="{{ route('debtors.index') }}" class="hover:text-primary transition-colors">Debtors</a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="ti ti-chevron-right text-[10px] mx-1"></i>
                    <span class="text-gray-600 dark:text-gray-300">{{ $debtor->contact->name }}</span>
                </div>
            </li>
        </ol>
    </nav>
    <h1 class="text-3xl font-black tracking-tight dark:text-white">{{ $debtor->contact->name }}</h1>
    <p class="text-gray-500 dark:text-gray-400">{{ $debtor->description ?? 'Loan' }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Debt Summary -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold mb-4 dark:text-white">Loan Summary</h3>

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Status</p>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $debtor->status == 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : ($debtor->status == 'partial' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                        {{ ucfirst($debtor->status) }}
                    </span>
                </div>

                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Total Amount</p>
                    <p class="text-2xl font-bold dark:text-white">{{ auth()->user()->currency_symbol }}{{ number_format($debtor->amount, 2) }}</p>
                </div>

                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Amount Paid</p>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ auth()->user()->currency_symbol }}{{ number_format($debtor->paid_amount, 2) }}</p>
                </div>

                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Balance Due</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ auth()->user()->currency_symbol }}{{ number_format($debtor->balance, 2) }}</p>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Date Given</p>
                    <p class="dark:text-white">{{ $debtor->date_given->format('F d, Y') }}</p>
                </div>

                @if($debtor->due_date)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Due Date</p>
                        <p class="dark:text-white">
                            {{ $debtor->due_date->format('F d, Y') }}
                            @if($debtor->isOverdue())
                                <span class="text-red-500 dark:text-red-400 text-xs ml-2">(Overdue)</span>
                            @endif
                        </p>
                    </div>
                @endif

                @if($debtor->notes)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Notes</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $debtor->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Contact Info -->
        <div class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold mb-4 dark:text-white">Contact Info</h3>

            <div class="space-y-3">
                <p class="dark:text-white"><i class="ti ti-user mr-2"></i>{{ $debtor->contact->name }}</p>
                @if($debtor->contact->phone)
                    <p class="dark:text-white"><i class="ti ti-phone mr-2"></i>{{ $debtor->contact->phone }}</p>
                @endif
                @if($debtor->contact->email)
                    <p class="dark:text-white"><i class="ti ti-mail mr-2"></i>{{ $debtor->contact->email }}</p>
                @endif
                <a href="{{ route('contacts.show', $debtor->contact) }}" class="inline-block text-blue-600 dark:text-blue-400 hover:underline text-sm mt-2">
                    View full contact →
                </a>
            </div>
        </div>
    </div>

    <!-- Payments Section -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Add Payment Form -->
        @if($debtor->balance > 0)
            <div class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold mb-4 dark:text-white">
                    <i class="ti ti-cash mr-2"></i>Record Payment
                </h3>

                <form method="POST" action="{{ route('debtors.add-payment', $debtor) }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                Amount
                                <span class="text-primary ml-1">({{ auth()->user()->currency_symbol }})</span>
                                *
                            </label>
                            <input type="number" name="amount" step="0.01" min="0.01" max="{{ $debtor->balance }}"
                                value="{{ old('amount', $debtor->balance) }}" required
                                placeholder="0.00"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white font-semibold">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max: {{ auth()->user()->currency_symbol }}{{ number_format($debtor->balance, 2) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Payment Date *</label>
                            <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Payment Method *</label>
                            <select name="payment_method" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="check">Check</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Reference #</label>
                            <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                                placeholder="Optional"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                        <textarea name="notes" rows="2"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white"
                            placeholder="Payment notes...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-800 dark:text-green-400">
                        <i class="ti ti-info-circle mr-1"></i>
                        This payment will automatically add cash to your balance
                    </div>

                    <button type="submit"
                        class="mt-4 w-full px-6 py-3 bg-success-muted text-white rounded-xl font-bold shadow-lg hover:opacity-90 transition-all">
                        <i class="ti ti-check mr-2"></i>Record Payment
                    </button>
                </form>
            </div>
        @else
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6 text-center">
                <i class="ti ti-circle-check text-4xl text-green-600 dark:text-green-400 mb-2"></i>
                <h3 class="text-lg font-bold text-green-800 dark:text-green-400">Fully Recovered!</h3>
                <p class="text-green-700 dark:text-green-500">This loan has been fully paid back.</p>
            </div>
        @endif

        <!-- Payment History -->
        <div class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold mb-4 dark:text-white">
                Payment History ({{ $debtor->payments->count() }})
            </h3>

            @if($debtor->payments->count() > 0)
                <div class="space-y-3">
                    @foreach($debtor->payments as $payment)
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-lg text-green-600 dark:text-green-400">
                                        +{{ auth()->user()->currency_symbol }}{{ number_format($payment->amount, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $payment->payment_date->format('F d, Y') }} •
                                        <span class="capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
                                        @if($payment->reference_number)
                                            • Ref: {{ $payment->reference_number }}
                                        @endif
                                    </p>
                                    @if($payment->notes)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $payment->notes }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $payment->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-8">No payments recorded yet</p>
            @endif
        </div>
    </div>
</div>
@endsection

