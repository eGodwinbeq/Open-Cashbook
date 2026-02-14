@extends('layouts.app')
@section('title', $contact->name)

@section('content')
<div class="mb-6">
    <nav class="flex mb-2" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-xs font-bold uppercase tracking-widest text-gray-400">
            <li class="inline-flex items-center">
                <a href="{{ route('contacts.index') }}" class="hover:text-primary transition-colors">Contacts</a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="ti ti-chevron-right text-[10px] mx-1"></i>
                    <span class="text-gray-600 dark:text-gray-300">{{ $contact->name }}</span>
                </div>
            </li>
        </ol>
    </nav>
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-black tracking-tight dark:text-white">{{ $contact->name }}</h1>
            <p class="text-gray-500 dark:text-gray-400">
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $contact->type == 'person' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' }}">
                    {{ ucfirst($contact->type) }}
                </span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('contacts.edit', $contact) }}"
                class="px-6 py-3 bg-primary text-white rounded-xl font-bold hover:opacity-90">
                <i class="ti ti-edit mr-2"></i>Edit
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Contact Information -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold mb-4 dark:text-white">Contact Information</h3>

            <div class="space-y-4">
                @if($contact->email)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Email</p>
                        <p class="dark:text-white"><i class="ti ti-mail mr-2"></i>{{ $contact->email }}</p>
                    </div>
                @endif

                @if($contact->phone)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Phone</p>
                        <p class="dark:text-white"><i class="ti ti-phone mr-2"></i>{{ $contact->phone }}</p>
                    </div>
                @endif

                @if($contact->address)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Address</p>
                        <p class="dark:text-white"><i class="ti ti-map-pin mr-2"></i>{{ $contact->address }}</p>
                    </div>
                @endif

                @if($contact->company)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Company</p>
                        <p class="dark:text-white"><i class="ti ti-building mr-2"></i>{{ $contact->company }}</p>
                    </div>
                @endif

                @if($contact->tax_number)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Tax Number</p>
                        <p class="dark:text-white"><i class="ti ti-file-text mr-2"></i>{{ $contact->tax_number }}</p>
                    </div>
                @endif

                @if($contact->notes)
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Notes</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $contact->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Records -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Invoices -->
        <div class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold mb-4 dark:text-white">Invoices ({{ $contact->invoices->count() }})</h3>

            @if($contact->invoices->count() > 0)
                <div class="space-y-2">
                    @foreach($contact->invoices->take(5) as $invoice)
                        <a href="{{ route('invoices.show', $invoice) }}"
                            class="block p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-bold dark:text-white">{{ $invoice->invoice_number }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->invoice_date->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold dark:text-white">{{ auth()->user()->currency_symbol }}{{ number_format($invoice->total_amount, 2) }}</p>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $invoice->status == 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                @if($contact->invoices->count() > 5)
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                        And {{ $contact->invoices->count() - 5 }} more invoices...
                    </p>
                @endif
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">No invoices yet</p>
            @endif
        </div>

        <!-- Debts -->
        <div class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold dark:text-white">Debts ({{ $contact->debtors->count() }})</h3>
                <a href="{{ route('debtors.create', ['contact' => $contact->id]) }}"
                    class="px-4 py-2 bg-primary text-white rounded-lg font-bold text-sm hover:opacity-90">
                    <i class="ti ti-plus mr-1"></i>New Debt
                </a>
            </div>

            @if($contact->debtors->count() > 0)
                <div class="space-y-2">
                    @foreach($contact->debtors as $debtor)
                        <a href="{{ route('debtors.show', $debtor) }}"
                            class="block p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-bold dark:text-white">{{ $debtor->description ?? 'Loan' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Given: {{ $debtor->date_given->format('M d, Y') }}
                                        @if($debtor->payments->count() > 0)
                                            • {{ $debtor->payments->count() }} payment(s)
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold dark:text-white">{{ auth()->user()->currency_symbol }}{{ number_format($debtor->balance, 2) }}</p>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $debtor->status == 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : ($debtor->status == 'partial' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                        {{ ucfirst($debtor->status) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">No debts recorded</p>
            @endif
        </div>
    </div>
</div>
@endsection

