<div x-data="penaltyData()" x-init="init()" class="mb-8">
    <!-- Penalty Action Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-xl">
                    <i class="fas fa-gavel text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Late Payment Penalties</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <span class="inline-flex items-center gap-1">
                            <span class="font-medium text-orange-600 dark:text-orange-400" x-text="stats.without_penalty">0</span>
                            <span>pending</span>
                        </span>
                        <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                        <span class="inline-flex items-center gap-1">
                            <span class="font-medium text-green-600 dark:text-green-400" x-text="stats.with_penalty">0</span>
                            <span>penalized</span>
                        </span>
                        <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                        <span class="inline-flex items-center gap-1">
                            <span class="font-medium text-gray-900 dark:text-white" x-text="stats.total_overdue">0</span>
                            <span>total overdue</span>
                        </span>
                    </p>
                </div>
            </div>
            <button
                @click="showModal = true"
                :disabled="stats.without_penalty === 0"
                class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
            >
                <i class="fas fa-gavel mr-2"></i>
                Apply Penalties
            </button>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4" @click.outside="showModal = false">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-gavel text-2xl text-red-600 dark:text-red-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Apply Late Payment Penalties?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    This will create a <span class="font-bold text-red-600">&#8369;10.00 penalty</span> charge for each overdue bill.
                    <br><br>
                    <span class="font-medium text-gray-900 dark:text-white" x-text="stats.without_penalty"></span> bill(s) will be penalized.
                </p>
                <div class="flex justify-center gap-3">
                    <button @click="showModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button @click="processPenalties()" :disabled="processing" class="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white rounded-lg transition-colors">
                        <i x-show="processing" class="fas fa-spinner fa-spin mr-2"></i>
                        <span x-text="processing ? 'Processing...' : 'Apply Penalties'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="notification.show" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-4 right-4 z-50"
    >
        <div :class="{
            'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200': notification.type === 'success',
            'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200': notification.type === 'error'
        }" class="flex items-center gap-3 p-4 rounded-lg border shadow-lg">
            <i :class="{
                'fas fa-check-circle': notification.type === 'success',
                'fas fa-exclamation-circle': notification.type === 'error'
            }"></i>
            <span x-text="notification.message"></span>
            <button @click="notification.show = false" class="ml-2 opacity-70 hover:opacity-100">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

<script>
function penaltyData() {
    return {
        stats: { total_overdue: 0, with_penalty: 0, without_penalty: 0 },
        showModal: false,
        processing: false,
        notification: { show: false, type: 'success', message: '' },

        init() {
            this.fetchStats();
        },

        async fetchStats() {
            try {
                const response = await fetch('/penalties/summary');
                const data = await response.json();
                if (data.success) {
                    this.stats = data.data;
                }
            } catch (error) {
                console.error('Error fetching penalty stats:', error);
            }
        },

        async processPenalties() {
            this.processing = true;
            try {
                const response = await fetch('/penalties/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification('success', data.message);
                    this.showModal = false;
                    this.fetchStats();
                    // Refresh the billing summary cards if available
                    if (window.refreshBillingSummary) {
                        window.refreshBillingSummary();
                    }
                } else {
                    this.showNotification('error', data.message || 'Failed to process penalties');
                }
            } catch (error) {
                console.error('Error processing penalties:', error);
                this.showNotification('error', 'An error occurred while processing penalties');
            } finally {
                this.processing = false;
            }
        },

        showNotification(type, message) {
            this.notification = { show: true, type, message };
            setTimeout(() => {
                this.notification.show = false;
            }, 5000);
        }
    };
}
</script>
