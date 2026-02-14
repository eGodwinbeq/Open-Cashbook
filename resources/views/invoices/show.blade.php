@extends('layouts.app')
@section('title', 'Invoice ' . $invoice->invoice_number)
@section('content')
<div class="mb-6">
    <a href="{{ route('invoices.index') }}" class="text-primary hover:underline flex items-center gap-2 mb-4">
        <i class="ti ti-arrow-left"></i>
        Back to Invoices
    </a>
</div>
<!-- Actions Bar -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-4xl font-black tracking-tight mb-1">{{ $invoice->invoice_number }}</h1>
        <p class="text-sm md:text-base text-gray-500">Invoice Details</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('invoices.edit', $invoice) }}"
            class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:opacity-90 transition-all">
            <i class="ti ti-edit"></i> Edit
        </a>
        <a href="{{ route('invoices.download', $invoice) }}" target="_blank"
            class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-xl font-bold hover:opacity-90 transition-all">
            <i class="ti ti-download"></i> Download
        </a>
        @if($invoice->status !== 'paid')
        <div class="flex gap-2">
            @if($invoice->balance_due > 0)
            <a href="{{ route('invoices.payment', $invoice) }}"
               class="px-6 py-3 bg-green-600 text-white rounded-xl font-bold hover:opacity-90 transition-all">
                <i class="ti ti-credit-card"></i> Add Payment
            </a>
            @endif
            <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="px-6 py-3 bg-green-600 text-white rounded-xl font-bold hover:opacity-90 transition-all">
                    <i class="ti ti-check"></i> Mark as Paid
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Invoice Header -->
        <div class="bg-white dark:bg-[#25282c] p-6 md:p-8 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 class="text-3xl font-black dark:text-white">INVOICE</h2>
                    <p class="text-gray-500 mt-1">{{ $invoice->invoice_number }}</p>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'draft' => 'bg-gray-100 text-gray-800',
                            'sent' => 'bg-blue-100 text-blue-800',
                            'paid' => 'bg-green-100 text-green-800',                            'partially_paid' => 'bg-yellow-100 text-yellow-800',                            'overdue' => 'bg-red-100 text-red-800',
                            'cancelled' => 'bg-orange-100 text-orange-800',
                        ];
                    @endphp
                    <span class="px-4 py-2 rounded-full text-sm font-bold {{ $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucwords(str_replace('_', ' ', $invoice->status)) }}
                    </span>
                    @if($invoice->balance_due > 0 && $invoice->paid_amount > 0)
                        <div class="mt-2 text-sm text-gray-600">
                            <div>Paid: {{ auth()->user()->currency_symbol }}{{ number_format($invoice->paid_amount, 2) }}</div>
                            <div>Balance: {{ auth()->user()->currency_symbol }}{{ number_format($invoice->balance_due, 2) }}</div>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-sm font-bold text-gray-500 uppercase mb-2">Billed To:</h3>
                    <div class="dark:text-white">
                        <p class="font-bold text-lg">{{ $invoice->client_name }}</p>
                        @if($invoice->client_email)
                        <p class="text-gray-600 dark:text-gray-400">{{ $invoice->client_email }}</p>
                        @endif
                        @if($invoice->client_phone)
                        <p class="text-gray-600 dark:text-gray-400">{{ $invoice->client_phone }}</p>
                        @endif
                        @if($invoice->client_address)
                        <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $invoice->client_address }}</p>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-gray-500 uppercase mb-2">Invoice Date:</h3>
                        <p class="dark:text-white">{{ $invoice->invoice_date->format('F d, Y') }}</p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-gray-500 uppercase mb-2">Due Date:</h3>
                        <p class="dark:text-white">{{ $invoice->due_date->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Items -->
        <div class="bg-white dark:bg-[#25282c] p-6 md:p-8 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b-2 border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="text-left py-3 text-sm font-bold text-gray-500 uppercase">Description</th>
                            <th class="text-right py-3 text-sm font-bold text-gray-500 uppercase">Qty</th>
                            <th class="text-right py-3 text-sm font-bold text-gray-500 uppercase">Unit Price</th>
                            <th class="text-right py-3 text-sm font-bold text-gray-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($invoice->items as $item)
                        <tr>
                            <td class="py-4 dark:text-white">{{ $item->description }}</td>
                            <td class="py-4 text-right dark:text-white">{{ $item->quantity }}</td>
                            <td class="py-4 text-right dark:text-white">{{ auth()->user()->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-4 text-right font-bold dark:text-white">{{ auth()->user()->currency_symbol }}{{ number_format($item->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Totals -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-end">
                    <div class="w-full md:w-1/2 space-y-2">
                        <div class="flex justify-between dark:text-gray-300">
                            <span>Subtotal:</span>
                            <span>{{ auth()->user()->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @if($invoice->tax_rate > 0)
                        <div class="flex justify-between dark:text-gray-300">
                            <span>Tax ({{ $invoice->tax_rate }}%):</span>
                            <span>{{ auth()->user()->currency_symbol }}{{ number_format($invoice->tax_amount, 2) }}</span>
                        </div>
                        @endif
                        @if($invoice->discount_amount > 0)
                        <div class="flex justify-between dark:text-gray-300">
                            <span>Discount:</span>
                            <span>-{{ auth()->user()->currency_symbol }}{{ number_format($invoice->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-2xl font-bold pt-2 border-t border-gray-300 dark:border-gray-600 dark:text-white">
                            <span>Total:</span>
                            <span>{{ auth()->user()->currency_symbol }}{{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Notes and Terms -->
        @if($invoice->notes || $invoice->terms)
        <div class="bg-white dark:bg-[#25282c] p-6 md:p-8 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
            @if($invoice->notes)
            <div class="mb-6">
                <h3 class="text-lg font-bold mb-2 dark:text-white">Notes</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $invoice->notes }}</p>
            </div>
            @endif
            @if($invoice->terms)
            <div>
                <h3 class="text-lg font-bold mb-2 dark:text-white">Payment Terms</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $invoice->terms }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>
    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Status Management -->
        <div class="bg-white dark:bg-[#25282c] p-6 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
            <h3 class="text-lg font-bold mb-4 dark:text-white">Update Status</h3>
            <form action="{{ route('invoices.update-status', $invoice) }}" method="POST">
                @csrf
                <select name="status" onchange="this.form.submit()"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-[#1e2125] dark:text-white">
                    <option value="draft" {{ $invoice->status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ $invoice->status == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="partially_paid" {{ $invoice->status == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ $invoice->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="cancelled" {{ $invoice->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>
        </div>
        <!-- Payment Info -->
        @if($invoice->status === 'paid' && $invoice->paid_date)
        <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-xl border border-green-200 dark:border-green-800">
            <div class="flex items-center gap-3 mb-2">
                <i class="ti ti-check text-2xl text-green-600"></i>
                <h3 class="text-lg font-bold text-green-800 dark:text-green-400">Paid</h3>
            </div>
            <p class="text-sm text-green-700 dark:text-green-500">
                Paid on {{ $invoice->paid_date->format('F d, Y') }}
            </p>
        </div>
        @elseif($invoice->isOverdue())
        <div class="bg-red-50 dark:bg-red-900/20 p-6 rounded-xl border border-red-200 dark:border-red-800">
            <div class="flex items-center gap-3 mb-2">
                <i class="ti ti-alert-circle text-2xl text-red-600"></i>
                <h3 class="text-lg font-bold text-red-800 dark:text-red-400">Overdue</h3>
            </div>
            <p class="text-sm text-red-700 dark:text-red-500">
                Due date was {{ $invoice->due_date->format('F d, Y') }}
            </p>
        </div>
        @endif
        <!-- Quick Actions -->
        <div class="bg-white dark:bg-[#25282c] p-6 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
            <h3 class="text-lg font-bold mb-4 dark:text-white">Quick Actions</h3>
            <div class="space-y-3">
                <a href="mailto:{{ $invoice->client_email }}?subject=Invoice {{ $invoice->invoice_number }}"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg font-semibold hover:opacity-90 transition-all">
                    <i class="ti ti-mail"></i>
                    Send Email
                </a>
                <button onclick="window.print()"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg font-semibold hover:opacity-90 transition-all">
                    <i class="ti ti-printer"></i>
                    Print
                </button>
                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-100 dark:bg-red-900/20 text-red-600 rounded-lg font-semibold hover:opacity-90 transition-all">
                        <i class="ti ti-trash"></i>
                        Delete Invoice
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
