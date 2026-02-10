<!-- Onboarding Modal -->
<div id="onboardingModal" class="modal" style="display: none;">
    <div
        class="modal-content bg-white dark:bg-[#25282c] rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('onboarding.complete') }}">
            @csrf
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-primary/10 p-3 rounded-full">
                        <i class="ti ti-currency-dollar text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold">Welcome to Open Cashbook!</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Let's get you started by setting up your preferred currency
                    symbol.</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Currency Symbol</label>
                    <input type="text" name="currency_symbol" placeholder="e.g., $, UGX, €, £" required maxlength="10"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary/50 font-semibold text-lg"
                        value="$" />
                    <p class="text-xs text-gray-500 mt-2">This will be used throughout the app for all monetary values.
                    </p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex gap-2">
                        <i class="ti ti-info-circle text-blue-600 dark:text-blue-400 flex-shrink-0"></i>
                        <div class="text-sm text-blue-800 dark:text-blue-300">
                            <p class="font-bold mb-1">Common Examples:</p>
                            <p>$ (USD), UGX (Ugandan Shilling), € (Euro), £ (Pound), ₹ (Rupee)</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                <button type="submit"
                    class="w-full px-6 py-3 bg-primary text-white rounded-xl font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
                    Get Started
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-show onboarding modal if user hasn't completed onboarding
    @if(!auth()->user()->onboarding_completed)
        document.addEventListener('DOMContentLoaded', function () {
            openModal('onboardingModal');
        });
    @endif
</script>