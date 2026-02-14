@extends('layouts.app')
@section('title', 'Edit Contact')

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
                    <a href="{{ route('contacts.show', $contact) }}" class="hover:text-primary transition-colors">{{ $contact->name }}</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="ti ti-chevron-right text-[10px] mx-1"></i>
                    <span class="text-gray-600 dark:text-gray-300">Edit</span>
                </div>
            </li>
        </ol>
    </nav>
    <h1 class="text-3xl font-black tracking-tight dark:text-white">Edit Contact</h1>
</div>

<div class="max-w-3xl">
    <form method="POST" action="{{ route('contacts.update', $contact) }}" class="bg-white dark:bg-[#25282c] rounded-2xl shadow-sm border border-[#eaeff0] dark:border-gray-700 p-6">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <!-- Type Selection -->
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Contact Type *</label>
                <div class="flex gap-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="type" value="person" {{ old('type', $contact->type) == 'person' ? 'checked' : '' }} required class="mr-2">
                        <span class="text-sm font-semibold dark:text-gray-300">👤 Person</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="type" value="organization" {{ old('type', $contact->type) == 'organization' ? 'checked' : '' }} required class="mr-2">
                        <span class="text-sm font-semibold dark:text-gray-300">🏢 Organization</span>
                    </label>
                </div>
            </div>

            <!-- Name -->
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                <input type="text" name="name" value="{{ old('name', $contact->name) }}" required
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $contact->email) }}"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $contact->phone) }}"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Address</label>
                <textarea name="address" rows="2"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">{{ old('address', $contact->address) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company -->
                <div id="company-field">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Company</label>
                    <input type="text" name="company" value="{{ old('company', $contact->company) }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
                </div>

                <!-- Tax Number -->
                <div id="tax-field">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tax Number</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number', $contact->tax_number) }}"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 dark:text-white">{{ old('notes', $contact->notes) }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex gap-3">
            <a href="{{ route('contacts.show', $contact) }}"
                class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all text-center">
                Cancel
            </a>
            <button type="submit"
                class="flex-1 px-6 py-3 bg-primary text-white rounded-xl font-bold shadow-lg hover:opacity-90 transition-all">
                Update Contact
            </button>
        </div>
    </form>
</div>

<script>
    // Toggle fields based on type
    const typeInputs = document.querySelectorAll('input[name="type"]');
    const companyField = document.getElementById('company-field');
    const taxField = document.getElementById('tax-field');

    function updateFields() {
        const selectedType = document.querySelector('input[name="type"]:checked').value;
        if (selectedType === 'organization') {
            companyField.style.display = 'none';
            taxField.style.display = 'block';
        } else {
            companyField.style.display = 'block';
            taxField.style.display = 'none';
        }
    }

    typeInputs.forEach(input => {
        input.addEventListener('change', updateFields);
    });

    // Set initial state
    updateFields();
</script>
@endsection

