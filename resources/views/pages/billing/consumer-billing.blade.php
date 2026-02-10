<div x-data="consumerBillingData()" x-init="init()">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Billed</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="'₱' + formatNumber(statistics.total_billed)"></p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <i class="fas fa-file-invoice-dollar text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                <span x-text="statistics.total_bills"></span> bills this period
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Collected</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="'₱' + formatNumber(statistics.total_paid)"></p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                <span x-text="statistics.paid_bills"></span> paid bills
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Outstanding</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400" x-text="'₱' + formatNumber(statistics.total_outstanding)"></p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                <span x-text="statistics.unpaid_bills"></span> unpaid bills
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Overdue</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400" x-text="statistics.overdue_bills"></p>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                Bills past due date
            </p>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
        <x-ui.search-bar placeholder="Search by name, account no, or location..." x-model="searchQuery" />
        <div class="flex items-center gap-3">
            <!-- Period Selector -->
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600 dark:text-gray-400">Period:</label>
                <select x-model="selectedPeriodId" @change="onPeriodChange()"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Periods</option>
                    <template x-for="period in periods" :key="period.per_id">
                        <option :value="String(period.per_id)" x-text="period.per_name"></option>
                    </template>
                </select>
            </div>

            <!-- Status Filter -->
            <select x-model="statusFilter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="all">All Status</option>
                <option value="paid">Paid</option>
                <option value="pending">Pending</option>
                <option value="overdue">Overdue</option>
            </select>

            <!-- Reload Button -->
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
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Account No</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Period</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumption</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Due Date</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <!-- Loading State -->
                    <template x-if="loading">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Loading billed consumers...
                            </td>
                        </tr>
                    </template>
                    <!-- Empty State -->
                    <template x-if="!loading && data.length === 0">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-file-invoice text-3xl mb-2 opacity-50"></i>
                                <p class="font-medium">No Bills Found</p>
                                <p class="text-sm">No consumers have been billed for the selected period.</p>
                            </td>
                        </tr>
                    </template>
                    <!-- Data Rows -->
                    <template x-if="!loading && data.length > 0">
                        <template x-for="consumer in paginatedData" :key="consumer.bill_id">
                            <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150" :class="consumer.status === 'Overdue' ? 'bg-red-50 dark:bg-red-900/10' : ''">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="consumer.name"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="consumer.location"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="consumer.account_no"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="consumer.period"></td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100" x-text="formatNumber(consumer.consumption, 3) + ' m³'"></td>
                                <td class="px-4 py-3 text-sm text-right font-bold" :class="consumer.status !== 'Paid' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'" x-text="'₱' + formatNumber(consumer.totalDue)"></td>
                                <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100" x-text="consumer.due_date || 'N/A'"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="{
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': consumer.status === 'Overdue',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': consumer.status === 'Pending',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': consumer.status === 'Paid'
                                    }">
                                        <i class="fas mr-1" :class="{
                                            'fa-exclamation-circle': consumer.status === 'Overdue',
                                            'fa-clock': consumer.status === 'Pending',
                                            'fa-check-circle': consumer.status === 'Paid'
                                        }"></i>
                                        <span x-text="consumer.status"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button @click="window.location.href='/billing/consumer/' + consumer.connection_id" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </template>
                    <!-- No Match State (when search/filter returns empty) -->
                    <template x-if="!loading && data.length > 0 && paginatedData.length === 0">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
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
</div>

<script>
function consumerBillingData() {
    return {
        searchQuery: '',
        statusFilter: 'all',
        pageSize: 10,
        currentPage: 1,
        data: [],
        periods: [],
        selectedPeriodId: '',
        activePeriodId: null,
        loading: false,
        statistics: {
            total_billed: 0,
            total_paid: 0,
            total_outstanding: 0,
            total_adjustments: 0,
            total_bills: 0,
            paid_bills: 0,
            unpaid_bills: 0,
            overdue_bills: 0
        },

        init() {
            this.loadPeriods();
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('statusFilter', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);
        },

        async loadPeriods() {
            try {
                const response = await fetch('/water-bills/billing-periods');
                const result = await response.json();
                if (result.success) {
                    this.periods = result.data;
                    this.activePeriodId = result.activePeriodId;

                    // Set default period to active period
                    if (this.activePeriodId) {
                        this.selectedPeriodId = String(this.activePeriodId);
                    } else {
                        // Fallback: find first non-closed period
                        const activePeriod = this.periods.find(p => !p.is_closed);
                        if (activePeriod) {
                            this.selectedPeriodId = String(activePeriod.per_id);
                        }
                    }
                }
            } catch (error) {
                console.error('Error loading periods:', error);
            }
            // Load data after periods are loaded
            this.loadData();
        },

        onPeriodChange() {
            this.currentPage = 1;
            this.loadData();
        },

        async loadData() {
            this.loading = true;
            try {
                let url = '/water-bills/billed-consumers';
                if (this.selectedPeriodId) {
                    url += `?period_id=${this.selectedPeriodId}`;
                }

                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    this.data = result.data || [];
                    this.statistics = result.statistics || this.statistics;
                } else {
                    console.error('Error:', result.message);
                    this.data = [];
                }
            } catch (error) {
                console.error('Error loading billed consumers:', error);
                this.data = [];
            } finally {
                this.loading = false;
            }
        },

        formatNumber(value, decimals = 2) {
            const num = parseFloat(value);
            return isNaN(num) ? '0.00' : num.toLocaleString('en-PH', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        },

        get filteredData() {
            let filtered = this.data;
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(c =>
                    (c.name?.toLowerCase() || '').includes(query) ||
                    (c.account_no?.toLowerCase() || '').includes(query) ||
                    (c.location?.toLowerCase() || '').includes(query)
                );
            }
            if (this.statusFilter !== 'all') {
                filtered = filtered.filter(c => c.status.toLowerCase() === this.statusFilter.toLowerCase());
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
        nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; }
    }
}

// Expose refresh function for external calls
window.refreshConsumerBilling = function() {
    const component = document.querySelector('[x-data="consumerBillingData()"]');
    if (component && component.__x) {
        component.__x.$data.loadData();
    }
};
</script>
