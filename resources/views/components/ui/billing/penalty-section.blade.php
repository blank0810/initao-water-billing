<div x-data="penaltyData()" x-init="init()" class="mb-8 space-y-4">
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

    <!-- Penalty Rate Configuration Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <button @click="showConfig = !showConfig" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-750 rounded-xl transition-colors">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <i class="fas fa-cog text-gray-600 dark:text-gray-400"></i>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Penalty Rate Configuration</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Current rate: <span class="font-medium text-red-600 dark:text-red-400" x-text="config.rate_percentage + '%'">10%</span>
                        <template x-if="config.effective_date">
                            <span> &middot; Effective <span x-text="config.effective_date"></span></span>
                        </template>
                    </p>
                </div>
            </div>
            <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="{ 'rotate-180': showConfig }"></i>
        </button>

        <div x-show="showConfig" x-collapse x-cloak class="border-t border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rate (%)</label>
                        <template x-if="!editingConfig">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="config.rate_percentage + '%'"></p>
                        </template>
                        <template x-if="editingConfig">
                            <input type="number" x-model="configForm.rate_percentage" min="0.01" max="100" step="0.01"
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </template>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Effective Date</label>
                        <p class="text-sm text-gray-900 dark:text-white" x-text="config.effective_date || 'N/A'"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Last Updated By</label>
                        <p class="text-sm text-gray-900 dark:text-white" x-text="config.updated_by_name || 'System Default'"></p>
                    </div>
                </div>

                @can('billing.manage')
                <div class="flex gap-2 shrink-0">
                    <template x-if="!editingConfig">
                        <button @click="startEditConfig()" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                            <i class="fas fa-edit mr-1.5"></i> Edit Rate
                        </button>
                    </template>
                    <template x-if="editingConfig">
                        <div class="flex gap-2">
                            <button @click="cancelEditConfig()" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                Cancel
                            </button>
                            <button @click="saveConfig()" :disabled="savingConfig" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white text-xs font-medium rounded-lg transition-colors">
                                <i x-show="savingConfig" class="fas fa-spinner fa-spin mr-1.5"></i>
                                <span x-text="savingConfig ? 'Saving...' : 'Save'"></span>
                            </button>
                        </div>
                    </template>
                </div>
                @endcan
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4" @click.outside="!processing && (showModal = false)">
            <div class="p-6 text-center">
                <!-- Pre-processing state -->
                <template x-if="!processing && !processResult">
                    <div>
                        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-gavel text-2xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Apply Late Payment Penalties?</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            This will apply a <span class="font-bold text-red-600"><span x-text="stats.penalty_rate">10</span>% penalty</span> on each overdue bill's total amount.
                            <br><br>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="stats.without_penalty"></span> bill(s) will be penalized
                            <template x-if="stats.estimated_total > 0">
                                <span>(est. <span class="font-bold text-red-600">&#8369;<span x-text="parseFloat(stats.estimated_total).toFixed(2)">0.00</span></span> total)</span>
                            </template>.
                        </p>
                        <div class="flex justify-center gap-3">
                            <button @click="showModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                Cancel
                            </button>
                            <button @click="processPenalties()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                Apply Penalties
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Processing state with live progress -->
                <template x-if="processing">
                    <div>
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 relative">
                            <div class="absolute inset-0 rounded-full animate-ping opacity-30" style="background: rgba(255, 106, 0, 0.3);"></div>
                            <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background: rgba(255, 106, 0, 0.15);">
                                <i class="fas fa-bolt text-2xl" style="color: #ff6a00;"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Processing Penalties...</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Calculating and applying penalties to overdue bills</p>

                        <!-- Live neon orange progress bar -->
                        <div class="mx-auto max-w-xs mb-3">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Progress</span>
                                <span class="text-sm font-bold" style="color: #ff6a00;" x-text="livePercent + '%'"></span>
                            </div>
                            <div class="w-full h-3.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden relative">
                                <div class="h-full rounded-full transition-all duration-500 ease-out relative overflow-hidden"
                                    :style="'width: ' + livePercent + '%; background: linear-gradient(90deg, #ff6a00, #ff9500, #ff6a00); background-size: 200% 100%; animation: neonPulse 2s ease-in-out infinite;'"
                                >
                                    <div class="absolute inset-0 rounded-full" style="box-shadow: 0 0 10px rgba(255, 106, 0, 0.6), 0 0 20px rgba(255, 106, 0, 0.3);"></div>
                                </div>
                            </div>
                            <div class="flex justify-between mt-1.5">
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    <span x-text="liveProcessed">0</span> of <span x-text="liveTotal">0</span> bills
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    Batch <span x-text="liveBatch">0</span>
                                </span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Please wait, do not close this window</p>
                    </div>
                </template>

                <!-- Result state with final progress bar -->
                <template x-if="!processing && processResult">
                    <div>
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                            :class="processResult.processed > 0 ? 'bg-green-100 dark:bg-green-900/30' : 'bg-gray-100 dark:bg-gray-700'">
                            <i class="text-2xl" :class="processResult.processed > 0 ? 'fas fa-check text-green-600 dark:text-green-400' : 'fas fa-info-circle text-gray-500 dark:text-gray-400'"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3" x-text="processResult.message"></h3>

                        <!-- Result progress bar -->
                        <div class="mx-auto max-w-xs mb-4">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Processed</span>
                                <span class="text-sm font-bold" style="color: #ff6a00;" x-text="processResultPercent + '%'"></span>
                            </div>
                            <div class="w-full h-3.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden relative">
                                <div class="h-full rounded-full transition-all duration-1000 ease-out relative overflow-hidden"
                                    x-data="{ width: 0 }"
                                    x-init="$nextTick(() => setTimeout(() => width = processResultPercent, 100))"
                                    :style="'width: ' + width + '%; background: linear-gradient(90deg, #ff6a00, #ff9500, #ff6a00); background-size: 200% 100%; animation: neonPulse 2s ease-in-out infinite;'"
                                >
                                    <div class="absolute inset-0 rounded-full" style="box-shadow: 0 0 10px rgba(255, 106, 0, 0.6), 0 0 20px rgba(255, 106, 0, 0.3);"></div>
                                </div>
                            </div>
                            <div class="flex justify-between mt-1.5">
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    <span x-text="processResult.processed">0</span> penalized
                                    <template x-if="processResult.skipped > 0">
                                        <span class="text-yellow-500"> &middot; <span x-text="processResult.skipped">0</span> skipped</span>
                                    </template>
                                </span>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                    <span x-text="processResult.total_overdue">0</span> total
                                </span>
                            </div>
                        </div>

                        <!-- Error details if any -->
                        <template x-if="processResult.errors && processResult.errors.length > 0">
                            <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-left">
                                <p class="text-xs font-medium text-red-700 dark:text-red-300 mb-1"><span x-text="processResult.errors.length"></span> error(s):</p>
                                <template x-for="err in processResult.errors.slice(0, 3)" :key="err.bill_id">
                                    <p class="text-xs text-red-600 dark:text-red-400">Bill #<span x-text="err.bill_id"></span>: <span x-text="err.message"></span></p>
                                </template>
                            </div>
                        </template>

                        <button @click="closeResultModal()" class="px-5 py-2 bg-gray-800 dark:bg-gray-600 hover:bg-gray-900 dark:hover:bg-gray-500 text-white rounded-lg transition-colors text-sm font-medium">
                            Done
                        </button>
                    </div>
                </template>
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
        stats: { total_overdue: 0, with_penalty: 0, without_penalty: 0, penalty_rate: 10, estimated_total: 0 },
        config: { rate_percentage: 10, effective_date: null, updated_by_name: null },
        configForm: { rate_percentage: 10 },
        showModal: false,
        showConfig: false,
        editingConfig: false,
        savingConfig: false,
        processing: false,
        processResult: null,
        liveProcessed: 0,
        liveTotal: 0,
        liveBatch: 0,
        notification: { show: false, type: 'success', message: '' },

        get penaltyPercent() {
            if (this.stats.total_overdue === 0) return 0;
            return Math.round((this.stats.with_penalty / this.stats.total_overdue) * 100);
        },

        get livePercent() {
            if (this.liveTotal === 0) return 0;
            return Math.min(100, Math.round((this.liveProcessed / this.liveTotal) * 100));
        },

        get processResultPercent() {
            if (!this.processResult || this.processResult.total_overdue === 0) return 0;
            return Math.round((this.processResult.processed / this.processResult.total_overdue) * 100);
        },

        init() {
            this.fetchStats();
            this.fetchConfig();
        },

        async fetchStats() {
            try {
                const response = await fetch('{{ route("penalties.summary") }}');
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const data = await response.json();
                if (data.success) {
                    this.stats = data.data;
                }
            } catch (error) {
                console.error('Error fetching penalty stats:', error);
            }
        },

        async fetchConfig() {
            try {
                const response = await fetch('{{ route("penalties.config") }}');
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const data = await response.json();
                if (data.success) {
                    this.config = data.data;
                }
            } catch (error) {
                console.error('Error fetching penalty config:', error);
            }
        },

        startEditConfig() {
            this.configForm.rate_percentage = this.config.rate_percentage;
            this.editingConfig = true;
        },

        cancelEditConfig() {
            this.editingConfig = false;
        },

        async saveConfig() {
            const rate = parseFloat(this.configForm.rate_percentage);
            if (isNaN(rate) || rate < 0.01 || rate > 100) {
                this.showNotification('error', 'Rate must be between 0.01% and 100%');
                return;
            }

            this.savingConfig = true;
            try {
                const response = await fetch('{{ route("penalties.config.update") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ rate_percentage: rate })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => null);
                    throw new Error(errorData?.message || `HTTP ${response.status}`);
                }

                const data = await response.json();
                if (data.success) {
                    this.showNotification('success', data.message);
                    this.editingConfig = false;
                    this.fetchConfig();
                    this.fetchStats();
                } else {
                    this.showNotification('error', data.message || 'Failed to update rate');
                }
            } catch (error) {
                console.error('Error updating penalty config:', error);
                this.showNotification('error', error.message || 'An error occurred while updating the rate');
            } finally {
                this.savingConfig = false;
            }
        },

        async processPenalties() {
            this.processing = true;
            this.processResult = null;
            this.liveProcessed = 0;
            this.liveTotal = this.stats.without_penalty || 0;
            this.liveBatch = 0;

            const batchSize = 50;
            let offset = 0;
            let totalProcessed = 0;
            let totalSkipped = 0;
            let allErrors = [];
            let totalOverdue = 0;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                let hasMore = true;

                while (hasMore) {
                    this.liveBatch++;
                    const response = await fetch('{{ route("penalties.process-batch") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ limit: batchSize, offset })
                    });

                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'Batch processing failed');
                    }

                    totalProcessed += data.batch_processed || 0;
                    totalSkipped += data.batch_skipped || 0;
                    allErrors = allErrors.concat(data.batch_errors || []);
                    totalOverdue = data.total_overdue || totalOverdue;
                    this.liveTotal = data.total_pending || this.liveTotal;

                    this.liveProcessed = totalProcessed + totalSkipped;
                    offset = data.offset;
                    hasMore = data.has_more;
                }

                this.processResult = {
                    message: totalProcessed > 0
                        ? `Successfully created ${totalProcessed} penalty/ies.`
                        : 'No new penalties created.',
                    total_overdue: this.liveTotal,
                    processed: totalProcessed,
                    skipped: totalSkipped,
                    errors: allErrors,
                };
                this.fetchStats();
                if (window.refreshBillingSummary) {
                    window.refreshBillingSummary();
                }
            } catch (error) {
                console.error('Error processing penalties:', error);
                if (totalProcessed > 0) {
                    this.processResult = {
                        message: `Partially completed: ${totalProcessed} penalty/ies created before error.`,
                        total_overdue: this.liveTotal,
                        processed: totalProcessed,
                        skipped: totalSkipped,
                        errors: [...allErrors, { bill_id: '-', message: error.message }],
                    };
                    this.fetchStats();
                } else {
                    this.showNotification('error', 'An error occurred while processing penalties');
                }
            } finally {
                this.processing = false;
            }
        },

        closeResultModal() {
            this.showModal = false;
            this.processResult = null;
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

<style>
@keyframes neonPulse {
    0%, 100% { background-position: 0% 50%; filter: brightness(1); }
    50% { background-position: 100% 50%; filter: brightness(1.2); }
}
@keyframes neonFlow {
    0% { background-position: 0% 50%; }
    100% { background-position: 300% 50%; }
}
</style>
