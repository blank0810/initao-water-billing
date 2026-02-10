<!-- Cancel Payment Confirmation Modal -->
<div x-show="showCancelPaymentModal"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50" @click="showCancelPaymentModal = false"></div>

    <!-- Modal -->
    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md z-10"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform scale-95 opacity-0"
         x-transition:enter-end="transform scale-100 opacity-100">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cancel Payment</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">This action cannot be undone</p>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-4 space-y-4">
            <!-- Payment Info -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Receipt #</span>
                    <span class="font-mono font-medium text-gray-900 dark:text-white" x-text="cancelPaymentData?.receipt_no"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Customer</span>
                    <span class="font-medium text-gray-900 dark:text-white" x-text="cancelPaymentData?.customer_name"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Amount</span>
                    <span class="font-bold text-red-600 dark:text-red-400" x-text="cancelPaymentData?.amount_formatted"></span>
                </div>
            </div>

            <!-- Reason Input -->
            <div>
                <label for="cancel-reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Reason for Cancellation <span class="text-red-500">*</span>
                </label>
                <textarea id="cancel-reason"
                          x-model="cancelPaymentReason"
                          rows="3"
                          maxlength="500"
                          placeholder="Explain why this payment is being cancelled..."
                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-red-500 focus:border-red-500"
                          :class="cancelPaymentError ? 'border-red-500' : ''"></textarea>
                <div class="flex justify-between mt-1">
                    <p x-show="cancelPaymentError" class="text-xs text-red-500" x-text="cancelPaymentError"></p>
                    <p class="text-xs text-gray-400 ml-auto" x-text="(cancelPaymentReason?.length || 0) + '/500'"></p>
                </div>
            </div>

            <!-- Warning -->
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-3">
                <div class="flex gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                    <div class="text-xs text-amber-700 dark:text-amber-300">
                        <p class="font-medium mb-1">This will:</p>
                        <ul class="list-disc ml-4 space-y-0.5">
                            <li>Cancel this payment and all its allocations</li>
                            <li>Create reversal entries in the customer ledger</li>
                            <li>Make the associated bills/charges available for payment again</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button @click="showCancelPaymentModal = false; cancelPaymentReason = ''; cancelPaymentError = '';"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                Keep Payment
            </button>
            <button @click="confirmCancelPayment()"
                    :disabled="cancelPaymentLoading"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors flex items-center gap-2">
                <i x-show="cancelPaymentLoading" class="fas fa-spinner fa-spin"></i>
                <span x-text="cancelPaymentLoading ? 'Cancelling...' : 'Cancel Payment'"></span>
            </button>
        </div>
    </div>
</div>
