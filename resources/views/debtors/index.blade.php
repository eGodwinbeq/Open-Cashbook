@extends('layouts.app')
@section('title', 'Debtors')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-4xl font-black tracking-tight dark:text-white">Debtors</h1>
            <p class="text-sm md:text-base text-gray-500 dark:text-gray-400">Track money you've lent out</p>
        </div>
        <a href="{{ route('debtors.create') }}"
            class="px-6 py-3 bg-primary text-white rounded-xl font-bold shadow-lg hover:opacity-90">
            <i class="ti ti-plus mr-2"></i>Record New Loan
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-[#25282c] p-6 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
        <div class="flex items-center gap-4">
            <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full">
                <i class="ti ti-cash-off text-2xl text-red-600 dark:text-red-400"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Outstanding</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->currency_symbol }}{{ number_format($totalOutstanding, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-[#25282c] p-6 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
        <div class="flex items-center gap-4">
            <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-full">
                <i class="ti ti-users text-2xl text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Debtors</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $debtors->where('status', '!=', 'paid')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-[#25282c] p-6 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
        <div class="flex items-center gap-4">
            <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                <i class="ti ti-check text-2xl text-green-600 dark:text-green-400"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fully Recovered</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $debtors->where('status', 'paid')->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="bg-white dark:bg-[#25282c] p-4 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700 mb-6">
    <form method="GET" class="flex gap-3">
        <select name="status" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
        </select>
        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg font-bold">Filter</button>
    </form>
</div>

<!-- Debtors List -->
<div class="bg-white dark:bg-[#25282c] rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
    @if($debtors->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date Given</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Paid</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#25282c] divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($debtors as $debtor)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 {{ $debtor->isOverdue() ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('contacts.show', $debtor->contact) }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $debtor->contact->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-300">{{ $debtor->description ?? 'Loan' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $debtor->date_given->format('M d, Y') }}
                                @if($debtor->isOverdue())
                                    <span class="text-red-500 dark:text-red-400 text-xs">(Overdue)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
                                {{ auth()->user()->currency_symbol }}{{ number_format($debtor->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400">
                                {{ auth()->user()->currency_symbol }}{{ number_format($debtor->paid_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $debtor->balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ auth()->user()->currency_symbol }}{{ number_format($debtor->balance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $debtor->status == 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : ($debtor->status == 'partial' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                    {{ ucfirst($debtor->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('debtors.show', $debtor) }}" class="text-blue-600 dark:text-blue-400 hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $debtors->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <i class="ti ti-receipt-off text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No debtors yet</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Record loans you've given out to track repayments</p>
            <a href="{{ route('debtors.create') }}" class="inline-block px-6 py-3 bg-primary text-white rounded-xl font-bold">
                Record First Loan
            </a>
        </div>
    @endif
</div>
@endsection

