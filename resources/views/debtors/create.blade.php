@extends('layouts.app')
@section('title', 'Record New Loan')

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
                    <span class="text-gray-600 dark:text-gray-300">Record Loan</span>
                </div>
            </li>
        </ol>
    </nav>
    <h1 class="text-3xl font-black tracking-tight dark:text-white">Record New Loan</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">This will automatically deduct cash from your balance</p>
</div>

<div class="max-w-3xl">
    <form method="POST" action="{{ route('debtors.store') }}" class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
        @csrf

        <div class="space-y-6">
            <!-- Contact Selection -->
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    Who did you lend money to? *
                </label>
                <select name="contact_id" required
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
                    <option value="">Select a contact...</option>
                    @foreach($contacts as $contact)
                        <option value="{{ $contact->id }}" {{ old('contact_id') == $contact->id ? 'selected' : '' }}>
                            {{ $contact->name }}{{ $contact->company ? ' (' . $contact->company . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Don't see the person? <a href="{{ route('contacts.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline" target="_blank">Add new contact</a>
                </p>
                @error('contact_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    Amount
                    <span class="text-primary ml-1">({{ auth()->user()->currency_symbol }})</span>
                    *
                </label>
                <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required
                    placeholder="0.00"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white font-semibold text-lg">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg text-sm text-yellow-800 dark:text-yellow-400">
                    <i class="ti ti-alert-triangle mr-1"></i>
                    This amount will be deducted from your cash balance
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date Given -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Date Given *</label>
                    <input type="date" name="date_given" value="{{ old('date_given', date('Y-m-d')) }}" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
                    @error('date_given')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Due Date (Optional)</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Chapter (Optional) -->
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Chapter (Optional)</label>
                <select name="chapter_id"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
                    <option value="">Auto-link to Financial Chapter</option>
                    @foreach($chapters as $chapter)
                        <option value="{{ $chapter->id }}" {{ old('chapter_id') == $chapter->id ? 'selected' : '' }}>
                            {{ $chapter->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <input type="text" name="description" value="{{ old('description') }}"
                    placeholder="e.g., Business loan, Emergency assistance"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white"
                    placeholder="Any additional details about this loan...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex gap-3">
            <a href="{{ route('debtors.index') }}"
                class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all text-center">
                Cancel
            </a>
            <button type="submit"
                class="flex-1 px-6 py-3 bg-danger-muted text-white rounded-xl font-bold shadow-lg hover:opacity-90 transition-all">
                <i class="ti ti-cash-off mr-2"></i>Record Loan & Deduct Cash
            </button>
        </div>
    </form>
</div>
@endsection

