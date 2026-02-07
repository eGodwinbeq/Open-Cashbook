<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Process Payment for Invoice #') . $invoice->invoice_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Invoice Summary -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Invoice Summary</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Client</label>
                                <p class="text-gray-900">{{ $invoice->client_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                                <p class="text-gray-900">${{ number_format($invoice->total_amount, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Balance Due</label>
                                <p class="text-red-600 font-semibold">${{ number_format($invoice->balance_due, 2) }}</p>
                            </div>
                        </div>
                        
                        @if($invoice->paid_amount > 0)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Paid Amount</label>
                                <p class="text-green-600">${{ number_format($invoice->paid_amount, 2) }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Form -->
                    <form method="POST" action="{{ route('invoices.process-payment', $invoice) }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Payment Amount</label>
                                <input type="number" step="0.01" max="{{ $invoice->balance_due }}" name="amount" id="amount" 
                                       value="{{ old('amount', $invoice->balance_due) }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-white dark:text-black dark:border-gray-300">
                                @error('amount')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <select name="payment_method" id="payment_method" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-white dark:text-black dark:border-gray-300">
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                                <input type="date" name="payment_date" id="payment_date" 
                                       value="{{ old('payment_date', now()->format('Y-m-d')) }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-white dark:text-black dark:border-gray-300">
                                @error('payment_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700">Reference Number (Optional)</label>
                                <input type="text" name="reference_number" id="reference_number" 
                                       value="{{ old('reference_number') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-white dark:text-black dark:border-gray-300">
                                @error('reference_number')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-white dark:text-black dark:border-gray-300">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex justify-between">
                            <a href="{{ route('invoices.show', $invoice) }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Process Payment
                            </button>
                        </div>
                    </form>

                    <!-- Payment History -->
                    @if($invoice->payments->count() > 0)
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold mb-4">Payment History</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($invoice->payments as $payment)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $payment->payment_date->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    ${{ number_format($payment->amount, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $payment->reference_number ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @if($payment->receipt)
                                                        <a href="{{ route('receipts.download', $payment->receipt) }}" 
                                                           class="text-blue-600 hover:text-blue-900">Download</a>
                                                    @else
                                                        -
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
            </div>
        </div>
    </div>
</x-app-layout>