<div x-data="{ open: false }" 
     @open-payment-confirmation.window="open = true; $nextTick(() => $refs.confirmBtn.focus())"
     @close-modal.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
         @click="open = false"
         x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full"
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.away="open = false">
            
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Confirm Payment</h3>
                </div>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-6" x-data="paymentConfirmation">
                
                <!-- Customer Information -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                        <i class="fas fa-user text-blue-600 mr-2"></i>
                        Customer Information
                    </h4>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Name:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="data.customerName"></span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Code:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="data.customerCode"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-600 dark:text-gray-400">Address:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="data.address"></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                        <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                        Payment Details
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Purpose:</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="data.purposeLabel"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Method:</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="data.methodLabel"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Date:</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="data.paymentDate"></span>
                        </div>
                        <div class="flex justify-between" x-show="data.referenceNumber">
                            <span class="text-gray-600 dark:text-gray-400">Reference:</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="data.referenceNumber"></span>
                        </div>
                    </div>
                </div>

                <!-- Charges Breakdown -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                        <i class="fas fa-file-invoice-dollar text-green-600 mr-2"></i>
                        Charges Breakdown
                    </h4>
                    <div class="space-y-2 text-sm">
                        <template x-for="charge in data.charges" :key="charge.name">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400" x-text="charge.name"></span>
                                <span class="font-medium text-gray-900 dark:text-white" x-text="formatCurrency(charge.amount)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between pt-2 border-t border-gray-300 dark:border-gray-600">
                            <span class="font-bold text-gray-900 dark:text-white">TOTAL:</span>
                            <span class="font-bold text-lg text-green-600 dark:text-green-400" x-text="formatCurrency(data.totalAmount)"></span>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div x-show="data.remarks" class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Remarks:</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="data.remarks"></p>
                </div>

                <!-- Warning -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 mt-0.5"></i>
                        <div class="text-sm text-yellow-800 dark:text-yellow-200">
                            <p class="font-semibold mb-1">Please verify all information before confirming</p>
                            <p>Once confirmed, this payment will be processed and the customer will be moved to the approval queue.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
                <button @click="open = false" 
                        class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition">
                    Cancel
                </button>
                <button @click="confirmPayment()" 
                        x-ref="confirmBtn"
                        :disabled="processing"
                        :class="processing ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                        class="flex-1 px-4 py-2.5 text-white rounded-lg font-medium transition">
                    <span x-show="!processing">Confirm & Process</span>
                    <span x-show="processing"><i class="fas fa-spinner fa-spin mr-2"></i>Processing...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('paymentConfirmation', () => ({
            data: {},
            processing: false,

            init() {
                this.$watch('$store.paymentData', (value) => {
                    this.data = value || {};
                });
            },

            formatCurrency(amount) {
                return '₱ ' + parseFloat(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            async confirmPayment() {
                this.processing = true;
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                this.$dispatch('payment-confirmed');
                this.$dispatch('close-modal');
                this.processing = false;
            }
        }));
    });
</script>
