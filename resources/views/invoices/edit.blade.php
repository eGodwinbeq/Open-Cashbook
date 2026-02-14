@extends('layouts.app')
@section('title', 'Edit Invoice ' . $invoice->invoice_number)
@section('content')
<div class="mb-6">
    <a href="{{ route('invoices.index') }}" class="text-primary hover:underline flex items-center gap-2 mb-4">
        <i class="ti ti-arrow-left"></i>
        Back to Invoice
    </a>
    <h1 class="text-2xl md:text-4xl font-black tracking-tight mb-1 md:mb-2">Edit Invoice</h1>
    <p class="text-sm md:text-base text-gray-500">Update invoice details</p>
</div>
<form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Client Information -->
            <div class="bg-white dark:bg-[#25282c] p-6 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
                <h2 class="text-xl font-bold mb-4 dark:text-white">Client Information</h2>

                <!-- Contact Selection -->
                <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <label class="block text-sm font-bold mb-2 dark:text-gray-200">
                        <i class="ti ti-users mr-1"></i>Select from Contacts (Optional)
                    </label>
                    <select id="contactSelect" name="contact_id"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black">
                        <option value="">-- Keep current or select new --</option>
                        @foreach($contacts as $contact)
                            <option value="{{ $contact->id }}"
                                {{ old('contact_id', $invoice->contact_id) == $contact->id ? 'selected' : '' }}
                                data-name="{{ $contact->name }}"
                                data-email="{{ $contact->email }}"
                                data-phone="{{ $contact->phone }}"
                                data-address="{{ $contact->address }}">
                                {{ $contact->name }}{{ $contact->company ? ' (' . $contact->company . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold mb-2 dark:text-gray-200">Client Name *</label>
                        <input type="text" id="client_name" name="client_name" value="{{ old('client_name', $invoice->client_name) }}" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black">
                        @error('client_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 dark:text-gray-200">Client Email</label>
                        <input type="email" id="client_email" name="client_email" value="{{ old('client_email', $invoice->client_email) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black">
                        @error('client_email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 dark:text-gray-200">Client Phone</label>
                        <input type="text" id="client_phone" name="client_phone" value="{{ old('client_phone', $invoice->client_phone) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold mb-2 dark:text-gray-200">Client Address</label>
                        <textarea id="client_address" name="client_address" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black">{{ old('client_address', $invoice->client_address) }}</textarea>
                    </div>
                </div>
            </div>
            <!-- Invoice Items -->
            <div class="bg-white dark:bg-[#25282c] p-6 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold dark:text-white">Items</h2>
                    <button type="button" onclick="addItem()"
                        class="px-4 py-2 bg-primary text-white rounded-lg font-bold hover:opacity-90 transition-all">
                        <i class="ti ti-plus"></i> Add Item
                    </button>
                </div>
                <div id="items-container" class="space-y-4">
                    @foreach($invoice->items as $index => $item)
                    <div class="item-row grid grid-cols-12 gap-3 items-start">
                        <div class="col-span-12 md:col-span-5">
                            <label class="block text-sm font-bold mb-2 dark:text-gray-200">Description *</label>
                            <input type="text" name="items[{{ $index }}][description]" value="{{ $item->description }}" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black">
                        </div>
                        <div class="col-span-6 md:col-span-2">
                            <label class="block text-sm font-bold mb-2 dark:text-gray-200">Qty *</label>
                            <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black"
                                onchange="calculateItemAmount(this)">
                        </div>
                        <div class="col-span-6 md:col-span-3">
                            <label class="block text-sm font-bold mb-2 dark:text-gray-200">
                                Unit Price
                                <span class="text-primary ml-1">({{ auth()->user()->currency_symbol }})</span>
                                *
                            </label>
                            <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" min="0" step="0.01" placeholder="0.00" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black"
                                onchange="calculateItemAmount(this)">
                        </div>
                        <div class="col-span-10 md:col-span-2 flex items-end">
                            <div class="w-full">
                                <label class="block text-sm font-bold mb-2 dark:text-gray-200">
                                    Amount
                                    <span class="text-primary ml-1">({{ auth()->user()->currency_symbol }})</span>
                                </label>
                                <input type="text" readonly value="{{ number_format($item->amount, 2) }}"
                                    class="w-full px-4 py-2 bg-gray-100 dark:bg-white border border-gray-300 dark:border-gray-300 rounded-lg dark:text-black item-amount">
                            </div>
                        </div>
                        <div class="col-span-2 md:col-span-12 lg:col-span-2 flex items-end justify-end md:justify-start">
                            <button type="button" onclick="removeItem(this)" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- Additional Details -->
            <div class="bg-white dark:bg-[#25282c] p-6 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
                <h2 class="text-xl font-bold mb-4 dark:text-white">Additional Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-2 dark:text-gray-200">Notes</label>
                        <textarea name="notes" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black">{{ old('notes', $invoice->notes) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 dark:text-gray-200">Payment Terms</label>
                        <textarea name="terms" rows="3" placeholder="e.g., Payment due within 30 days"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black">{{ old('terms', $invoice->terms) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Invoice Details -->
            <div class="bg-white dark:bg-[#25282c] p-6 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
                <h2 class="text-xl font-bold mb-4 dark:text-white">Invoice Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-2 dark:text-gray-200">Invoice Number</label>
                        <input type="text" value="{{ $invoice->invoice_number }}" readonly
                            class="w-full px-4 py-2 bg-gray-100 dark:bg-white border border-gray-300 dark:border-gray-300 rounded-lg dark:text-black">
                    </div>
                    <!-- Chapter is auto-linked in backend, hidden from user -->
                    <input type="hidden" name="chapter_id" value="{{ $invoice->chapter_id }}">
                    <div>
                        <label class="block text-sm font-bold mb-2 dark:text-gray-200">Invoice Date *</label>
                        <input type="date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 dark:text-gray-200">Due Date *</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black">
                    </div>
                </div>
            </div>
            <!-- Totals -->
            <div class="bg-white dark:bg-[#25282c] p-6 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
                <h2 class="text-xl font-bold mb-4 dark:text-white">Totals</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-2 dark:text-gray-200">Tax Rate (%)</label>
                        <input type="number" name="tax_rate" value="{{ old('tax_rate', $invoice->tax_rate) }}" min="0" max="100" step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black"
                            onchange="calculateTotals()">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 dark:text-gray-200">
                            Discount
                            <span class="text-primary ml-1">({{ auth()->user()->currency_symbol }})</span>
                        </label>
                        <input type="number" name="discount_amount" value="{{ old('discount_amount', $invoice->discount_amount) }}" min="0" step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-white dark:text-black"
                            onchange="calculateTotals()">
                    </div>
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between mb-2 dark:text-gray-300">
                            <span>Subtotal:</span>
                            <span id="subtotal">$0.00</span>
                        </div>
                        <div class="flex justify-between mb-2 dark:text-gray-300">
                            <span>Tax:</span>
                            <span id="tax">$0.00</span>
                        </div>
                        <div class="flex justify-between mb-2 dark:text-gray-300">
                            <span>Discount:</span>
                            <span id="discount">$0.00</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold dark:text-white">
                            <span>Total:</span>
                            <span id="total">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit" id="submitBtn"
                    class="flex-1 px-6 py-3 bg-primary text-white rounded-xl font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
                    Update Invoice
                </button>
                <a href="{{ route('invoices.show', $invoice) }}"
                    class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-xl font-bold hover:opacity-90 transition-all">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>
@push('scripts')
<script>
// Form validation before submit
document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    const itemsContainer = document.getElementById('items-container');
    const itemRows = itemsContainer.querySelectorAll('.item-row');

    if (itemRows.length === 0) {
        e.preventDefault();
        alert('Please add at least one item to the invoice.');
        return false;
    }

    // Check if all items have description
    let hasEmptyDescription = false;
    itemRows.forEach(row => {
        const description = row.querySelector('input[name*="[description]"]').value.trim();
        if (!description) {
            hasEmptyDescription = true;
        }
    });

    if (hasEmptyDescription) {
        e.preventDefault();
        alert('Please fill in the description for all items.');
        return false;
    }

    return true;
});

// Contact auto-fill
document.getElementById('contactSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (this.value) {
        document.getElementById('client_name').value = selected.dataset.name || '';
        document.getElementById('client_email').value = selected.dataset.email || '';
        document.getElementById('client_phone').value = selected.dataset.phone || '';
        document.getElementById('client_address').value = selected.dataset.address || '';
    }
});

let itemCount = {{ $invoice->items->count() }};
function addItem() {
    const container = document.getElementById('items-container');
    const newItem = container.firstElementChild.cloneNode(true);
    newItem.querySelectorAll('input').forEach(input => {
        if (input.name) {
            input.name = input.name.replace(/\[\d+\]/, `[${itemCount}]`);
        }
        if (!input.readOnly) {
            input.value = input.name.includes('quantity') ? '1' : '0';
        } else {
            input.value = '$0.00';
        }
    });
    const deleteBtn = newItem.querySelector('button[onclick^="removeItem"]');
    deleteBtn.disabled = false;
    container.appendChild(newItem);
    itemCount++;
    calculateTotals();
}
function removeItem(btn) {
    const container = document.getElementById('items-container');
    if (container.children.length > 1) {
        btn.closest('.item-row').remove();
        calculateTotals();
    }
}
function calculateItemAmount(input) {
    const row = input.closest('.item-row');
    const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
    const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
    const amount = quantity * unitPrice;
    row.querySelector('.item-amount').value = amount.toFixed(2);
    calculateTotals();
}
function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
        const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
        subtotal += quantity * unitPrice;
    });
    const taxRate = parseFloat(document.querySelector('input[name="tax_rate"]').value) || 0;
    const discountAmount = parseFloat(document.querySelector('input[name="discount_amount"]').value) || 0;
    const tax = (subtotal * taxRate) / 100;
    const total = subtotal + tax - discountAmount;
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '$' + tax.toFixed(2);
    document.getElementById('discount').textContent = '$' + discountAmount.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}
document.addEventListener('DOMContentLoaded', calculateTotals);
</script>
@endpush
@endsection


