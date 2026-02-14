<!-- Cash In Modal -->
<div id="cashInModal" class="modal">
    <div
        class="modal-content bg-white dark:bg-[#25282c] rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700">
        @if($activeChapter ?? false)
            <form method="POST" action="{{ route('transactions.create') }}">
                @csrf
                <input type="hidden" name="type" value="in">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-success-muted/10 p-2 rounded-lg">
                            <i class="ti ti-circle-plus text-success-muted text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold">Add Cash In</h3>
                    </div>
                    <button type="button" onclick="closeModal('cashInModal')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="ti ti-x text-xl"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Amount
                            <span class="text-primary ml-1">({{ auth()->user()->currency_symbol }})</span>
                        </label>
                        <input type="number" name="amount" step="0.01" placeholder="0.00" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50 font-semibold text-lg"
                            autofocus />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <input type="text" name="description" list="income-descriptions"
                            placeholder="e.g., Client payment, Salary" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50" />
                        <datalist id="income-descriptions">
                            <option value="Client payment">
                            <option value="Salary">
                            <option value="Freelance work">
                            <option value="Investment return">
                            <option value="Bonus">
                            <option value="Refund">
                            <option value="Gift">
                            <option value="Sales">
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Date</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-success-muted/50" />
                    </div>
                </div>
                <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                    <button type="button" onclick="closeModal('cashInModal')"
                        class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">Cancel</button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-success-muted text-white rounded-xl font-bold shadow-lg shadow-green-500/20 hover:opacity-90 transition-all">Add
                        Entry</button>
                </div>
            </form>
        @else
            <div class="p-6">
                <p class="text-center text-gray-500">Please create or select a chapter first.</p>
                <button type="button" onclick="closeModal('cashInModal')"
                    class="w-full mt-4 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">Close</button>
            </div>
        @endif
    </div>
</div>
