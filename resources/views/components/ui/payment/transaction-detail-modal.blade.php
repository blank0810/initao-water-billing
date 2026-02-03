<div x-show="showDetailModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         @click="showDetailModal = false"
         x-show="showDetailModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full"
             x-show="showDetailModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.away="showDetailModal = false">

            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-600 dark:text-blue-400 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaction Details</h3>
                </div>
                <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Content -->
            <div class="p-5 space-y-4">
                <!-- Receipt Info -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Receipt #</span>
                            <p class="font-semibold text-gray-900 dark:text-white font-mono" x-text="selectedTransaction?.receipt_no"></p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Time</span>
                            <p class="font-semibold text-gray-900 dark:text-white" x-text="selectedTransaction?.time"></p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500 dark:text-gray-400">Processed by</span>
                            <p class="font-semibold text-gray-900 dark:text-white">You ({{ auth()->user()->name }})</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Customer</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Name</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="selectedTransaction?.customer_name"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Reference #</span>
                            <span class="font-medium text-gray-900 dark:text-white font-mono" x-text="selectedTransaction?.customer_code"></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Payment</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Type</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="selectedTransaction?.payment_type_label"></span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                            <span class="font-semibold text-gray-900 dark:text-white">Amount Paid</span>
                            <span class="font-bold text-lg text-green-600 dark:text-green-400" x-text="selectedTransaction?.amount_formatted"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex gap-3 p-5 border-t border-gray-200 dark:border-gray-700">
                <button @click="showDetailModal = false"
                        class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                    Close
                </button>
                <a :href="selectedTransaction?.receipt_url"
                   target="_blank"
                   class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-center">
                    <i class="fas fa-print mr-2"></i>Print Receipt
                </a>
            </div>
        </div>
    </div>
</div>
