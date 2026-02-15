<div x-data="collectionsData()" x-init="init()">
    <!-- Collection Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Today's Collection</p>
                    <p class="text-3xl font-bold mt-2" x-text="stats.today_collection_formatted || '₱ 0.00'"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-calendar-day text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">This Month</p>
                    <p class="text-3xl font-bold mt-2" x-text="stats.month_collection_formatted || '₱ 0.00'"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Transactions</p>
                    <p class="text-3xl font-bold mt-2" x-text="stats.total_transactions || '0'"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-receipt text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Avg Payment</p>
                    <p class="text-3xl font-bold mt-2" x-text="stats.average_payment_formatted || '₱ 0.00'"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
        <x-ui.search-bar placeholder="Search by receipt, consumer, or amount..." x-model="searchQuery" />
        <div class="flex items-center gap-3">
            <select x-model="statusFilter" @change="currentPage = 1; loadData()"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <input type="date" x-model="dateFrom" @change="currentPage = 1; loadData()"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                title="Date from" />
            <input type="date" x-model="dateTo" @change="currentPage = 1; loadData()"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                title="Date to" />
            <button @click="loadData()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center gap-2" :disabled="loading">
                <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                <span>Reload</span>
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Receipt No.</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Cashier</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <!-- Loading State -->
                    <template x-if="loading">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Loading collections...
                            </td>
                        </tr>
                    </template>
                    <!-- Empty State -->
                    <template x-if="!loading && data.length === 0">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p class="font-medium">No Collections Found</p>
                                <p class="text-sm">No payment records match your filters.</p>
                            </td>
                        </tr>
                    </template>
                    <!-- Data Rows -->
                    <template x-if="!loading && data.length > 0">
                        <template x-for="payment in paginatedData" :key="payment.payment_id">
                            <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150"
                                :class="payment.is_cancelled ? 'bg-red-50/30 dark:bg-red-900/10' : ''">
                                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="payment.receipt_no"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="payment.payment_date || '-'"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="payment.consumer_name"></td>
                                <td class="px-4 py-3 text-sm text-right font-bold text-green-600 dark:text-green-400" x-text="payment.amount_formatted"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="payment.cashier"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="payment.is_cancelled
                                            ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                            : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'">
                                        <i class="fas mr-1" :class="payment.is_cancelled ? 'fa-ban' : 'fa-check-circle'"></i>
                                        <span x-text="payment.status"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a :href="payment.receipt_url" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded" title="View Receipt">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <template x-if="!payment.is_cancelled">
                                            <a :href="payment.receipt_url" target="_blank" class="text-gray-600 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 p-2 rounded" title="Print Receipt">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </template>
                                        @can('payments.void')
                                        <template x-if="!payment.is_cancelled">
                                            <button @click="openCancelModal(payment)" class="text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 p-2 rounded" title="Cancel Payment">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </template>
                                        @endcan
                                        <template x-if="payment.is_cancelled">
                                            <span class="text-xs text-red-400 dark:text-red-500 p-2" :title="payment.cancellation_reason || 'No reason'">
                                                <i class="fas fa-info-circle"></i>
                                            </span>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>
                    <!-- No Match State -->
                    <template x-if="!loading && data.length > 0 && paginatedData.length === 0">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-search text-3xl mb-2 opacity-50"></i>
                                <p>No matching records found</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div x-show="!loading && data.length > 0" x-cloak class="flex justify-between items-center mt-4 flex-wrap gap-4">
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
            <select x-model.number="pageSize" @change="currentPage = 1" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
        </div>

        <div class="flex items-center gap-2">
            <button @click="prevPage()" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                <i class="fas fa-chevron-left mr-1"></i>Previous
            </button>
            <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
                Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
            </div>
            <button @click="nextPage()" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                Next<i class="fas fa-chevron-right ml-1"></i>
            </button>
        </div>

        <div class="text-sm text-gray-600 dark:text-gray-400">
            Showing <span class="font-semibold text-gray-900 dark:text-white" x-text="startRecord"></span> to <span class="font-semibold text-gray-900 dark:text-white" x-text="endRecord"></span> of <span class="font-semibold text-gray-900 dark:text-white" x-text="totalRecords"></span> results
        </div>
    </div>

    <!-- Cancel Payment Modal -->
    <div x-show="showCancelModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="fixed inset-0 bg-black/50" @click="showCancelModal = false"></div>

        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md z-10"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform scale-95 opacity-0" x-transition:enter-end="transform scale-100 opacity-100">

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

            <div class="px-6 py-4 space-y-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Receipt #</span>
                        <span class="font-mono font-medium text-gray-900 dark:text-white" x-text="cancelData?.receipt_no"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Consumer</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="cancelData?.consumer_name"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Amount</span>
                        <span class="font-bold text-red-600 dark:text-red-400" x-text="cancelData?.amount_formatted"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Reason for Cancellation <span class="text-red-500">*</span>
                    </label>
                    <textarea x-model="cancelReason" rows="3" maxlength="500"
                        placeholder="Explain why this payment is being cancelled..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-red-500 focus:border-red-500"
                        :class="cancelError ? 'border-red-500' : ''"></textarea>
                    <div class="flex justify-between mt-1">
                        <p x-show="cancelError" class="text-xs text-red-500" x-text="cancelError"></p>
                        <p class="text-xs text-gray-400 ml-auto" x-text="(cancelReason?.length || 0) + '/500'"></p>
                    </div>
                </div>

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

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                <button @click="showCancelModal = false; cancelReason = ''; cancelError = '';"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Keep Payment
                </button>
                <button @click="confirmCancel()" :disabled="cancelLoading"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors flex items-center gap-2">
                    <i x-show="cancelLoading" class="fas fa-spinner fa-spin"></i>
                    <span x-text="cancelLoading ? 'Cancelling...' : 'Cancel Payment'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function collectionsData() {
    return {
        searchQuery: '',
        statusFilter: 'all',
        dateFrom: '',
        dateTo: '',
        pageSize: 10,
        currentPage: 1,
        data: [],
        stats: {},
        loading: false,
        initialized: false,

        // Cancel modal state
        showCancelModal: false,
        cancelData: null,
        cancelReason: '',
        cancelError: '',
        cancelLoading: false,

        init() {
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);

            window.renderCollections = () => {
                if (!this.initialized) {
                    this.loadData();
                    this.loadStats();
                    this.initialized = true;
                }
            };
        },

        async loadData() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    draw: '1',
                    start: '0',
                    length: '9999',
                    'search[value]': '',
                    'order[0][column]': '1',
                    'order[0][dir]': 'desc',
                    status: this.statusFilter,
                });
                if (this.dateFrom) params.set('date_from', this.dateFrom);
                if (this.dateTo) params.set('date_to', this.dateTo);

                const response = await fetch(`{{ route('api.billing.collections') }}?${params.toString()}`);
                const result = await response.json();
                this.data = result.data || [];
            } catch (error) {
                console.error('Error loading collections:', error);
                this.data = [];
                if (window.showToast) showToast('Error', 'Failed to load collections data.', 'error');
            } finally {
                this.loading = false;
            }
        },

        async loadStats() {
            try {
                const response = await fetch('{{ route("api.payments.statistics") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await response.json();
                if (result.success) {
                    this.stats = result.data;
                }
            } catch (error) {
                console.error('Failed to load stats:', error);
            }
        },

        get filteredData() {
            let filtered = this.data;
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(p =>
                    (p.receipt_no?.toLowerCase() || '').includes(query) ||
                    (p.consumer_name?.toLowerCase() || '').includes(query) ||
                    (p.amount_formatted?.toLowerCase() || '').includes(query) ||
                    (p.cashier?.toLowerCase() || '').includes(query)
                );
            }
            return filtered;
        },

        get totalRecords() { return this.filteredData.length; },
        get totalPages() { return Math.ceil(this.totalRecords / this.pageSize) || 1; },
        get startRecord() { return this.totalRecords === 0 ? 0 : ((this.currentPage - 1) * this.pageSize) + 1; },
        get endRecord() { return Math.min(this.currentPage * this.pageSize, this.totalRecords); },
        get paginatedData() {
            const start = (this.currentPage - 1) * this.pageSize;
            return this.filteredData.slice(start, start + this.pageSize);
        },

        prevPage() { if (this.currentPage > 1) this.currentPage--; },
        nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },

        openCancelModal(payment) {
            this.cancelData = payment;
            this.cancelReason = '';
            this.cancelError = '';
            this.showCancelModal = true;
        },

        async confirmCancel() {
            if (!this.cancelReason.trim()) {
                this.cancelError = 'Please provide a reason for cancellation.';
                return;
            }

            this.cancelLoading = true;
            this.cancelError = '';

            try {
                const response = await fetch(`/payment/${this.cancelData.payment_id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ reason: this.cancelReason.trim() }),
                });
                const result = await response.json();

                if (result.success) {
                    this.showCancelModal = false;
                    this.cancelReason = '';
                    this.loadData();
                    this.loadStats();
                    if (window.showToast) showToast('Success', 'Payment cancelled successfully.', 'success');
                } else {
                    this.cancelError = result.message || 'Failed to cancel payment.';
                }
            } catch (error) {
                this.cancelError = 'Network error. Please try again.';
            } finally {
                this.cancelLoading = false;
            }
        }
    }
}
</script>
