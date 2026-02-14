@extends('layouts.app')
@section('title', 'Invoices')
@section('content')
<div class="flex flex-col gap-4 mb-6 md:mb-10">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl md:text-4xl font-black tracking-tight mb-1 md:mb-2">Invoices</h1>
            <p class="text-sm md:text-base text-gray-500">Create, manage and track your invoices</p>
        </div>
        <a href="{{ route('invoices.create') }}"
            class="flex items-center gap-2 px-6 h-14 bg-primary text-white rounded-xl font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all active:scale-95">
            <i class="ti ti-plus"></i>
            <span class="hidden md:inline">New Invoice</span>
        </a>
    </div>
</div>
<!-- Filters -->
<div class="bg-white dark:bg-[#25282c] p-4 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700 mb-6">
    <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by invoice number or client..."
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-[#1e2125] dark:text-white">
        </div>
        <div>
            <select name="status"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-[#1e2125] dark:text-white">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <button type="submit"
            class="px-6 py-2 bg-primary text-white rounded-lg font-bold hover:opacity-90 transition-all">
            Filter
        </button>
    </form>
</div>
<!-- Invoices List -->
@if($invoices->count() > 0)
<div class="bg-white dark:bg-[#25282c] rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-[#1e2125] border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Invoice #</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Due Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($invoices as $invoice)
                <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2125] transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-primary font-bold hover:underline">
                            {{ $invoice->invoice_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-semibold dark:text-white">{{ $invoice->client_name }}</div>
                        @if($invoice->client_email)
                        <div class="text-sm text-gray-500">{{ $invoice->client_email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell dark:text-gray-300">
                        {{ $invoice->invoice_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell dark:text-gray-300">
                        {{ $invoice->due_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-bold dark:text-white">
                        {{ auth()->user()->currency_symbol }}{{ number_format($invoice->total_amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'sent' => 'bg-blue-100 text-blue-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'overdue' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-orange-100 text-orange-800',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('invoices.show', $invoice) }}"
                                class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('invoices.edit', $invoice) }}"
                                class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            @if($invoice->status !== 'paid')
                            <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors"
                                    title="Mark as Paid">
                                    <i class="ti ti-check"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors"
                                    title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">
    {{ $invoices->links() }}
</div>
@else
<div class="flex flex-col items-center justify-center py-20 text-center">
    <div class="bg-primary/10 p-6 rounded-full mb-6">
        <i class="ti ti-file-invoice text-6xl text-primary"></i>
    </div>
    <h2 class="text-3xl font-black mb-2">No Invoices Found</h2>
    <p class="text-gray-500 max-w-md mb-8">Start creating invoices to manage your billing and payments.</p>
    <a href="{{ route('invoices.create') }}"
        class="px-8 py-4 bg-primary text-white rounded-xl font-bold shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all">
        Create First Invoice
    </a>
</div>
@endif
@endsection
