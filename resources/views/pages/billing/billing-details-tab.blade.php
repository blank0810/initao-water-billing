<div x-data="billingDetailsData()" x-init="init()">
    <!-- Period Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Bills Generated</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="statistics.total_bills">0</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <i class="fas fa-file-invoice text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Total Billed</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="'₱' + formatNumber(statistics.total_billed)">₱0.00</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <i class="fas fa-peso-sign text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Collected</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="'₱' + formatNumber(statistics.total_paid)">₱0.00</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Outstanding</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400" x-text="'₱' + formatNumber(statistics.total_outstanding)">₱0.00</p>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <i class="fas fa-clock text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <x-ui.search-bar placeholder="Search by bill no, consumer, or amount..." x-model="searchQuery" />
            </div>
            <div class="sm:w-56">
                <select x-model="selectedPeriod" @change="loadData()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Periods</option>
                    <template x-for="period in periods" :key="period.per_id">
                        <option :value="period.per_id" x-text="period.per_name" :selected="period.per_id == selectedPeriod"></option>
                    </template>
                </select>
            </div>
            <div class="sm:w-40">
                <select x-model="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="Paid">Paid</option>
                    <option value="Pending">Pending</option>
                    <option value="Overdue">Overdue</option>
                </select>
            </div>
            <div class="sm:w-32">
                <button onclick="openGenerateBillModal()" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                    <i class="fas fa-plus mr-2"></i>Generate
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Bill No.</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Period</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumption</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Due Date</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <!-- Loading State -->
                    <template x-if="loading">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                <p>Loading bills...</p>
                            </td>
                        </tr>
                    </template>
                    <!-- Data Rows -->
                    <template x-if="!loading">
                    <template x-for="bill in paginatedData" :key="bill.bill_id">
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150" :class="bill.status === 'Overdue' ? 'bg-red-50 dark:bg-red-900/10' : ''">
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="'BILL-' + bill.bill_id"></td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="bill.name"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="bill.account_no"></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="bill.period"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="bill.consumption.toFixed(3) + ' m³'"></td>
                            <td class="px-4 py-3 text-sm text-right font-bold" :class="bill.status === 'Overdue' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100'" x-text="'₱' + formatNumber(bill.totalDue)"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="bill.due_date"></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full" :class="bill.statusClass">
                                    <i class="fas mr-1" :class="bill.statusIcon"></i><span x-text="bill.status"></span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="viewBill(bill)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    </template>
                    <!-- Empty State -->
                    <template x-if="!loading && paginatedData.length === 0">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>No bills found for this period</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4 flex-wrap gap-4">
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
function billingDetailsData() {
    const statusClassMap = {
        'Paid': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
        'Pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',
        'Overdue': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
    };
    const statusIconMap = {
        'Paid': 'fa-check-circle',
        'Pending': 'fa-clock',
        'Overdue': 'fa-exclamation-circle'
    };

    return {
        searchQuery: '',
        statusFilter: '',
        selectedPeriod: '',
        pageSize: 10,
        currentPage: 1,
        data: [],
        periods: [],
        statistics: {
            total_bills: 0,
            total_billed: 0,
            total_paid: 0,
            total_outstanding: 0
        },
        loading: false,

        async init() {
            await this.loadPeriods();
            await this.loadData();
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('statusFilter', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);

            // Listen for bill generation events
            document.addEventListener('bill-generated', () => this.loadData());
        },

        async loadPeriods() {
            try {
                const response = await fetch('/water-bills/billing-periods');
                const result = await response.json();
                if (result.success) {
                    this.periods = result.data;
                    // Set active period as default
                    if (result.activePeriodId) {
                        this.selectedPeriod = result.activePeriodId;
                    }
                }
            } catch (error) {
                console.error('Error loading periods:', error);
            }
        },

        async loadData() {
            this.loading = true;
            try {
                let url = '/water-bills/billed-consumers';
                if (this.selectedPeriod) {
                    url += `?period_id=${this.selectedPeriod}`;
                }

                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    this.data = result.data.map(bill => ({
                        ...bill,
                        statusClass: statusClassMap[bill.status] || statusClassMap['Pending'],
                        statusIcon: statusIconMap[bill.status] || statusIconMap['Pending']
                    }));
                    this.statistics = result.statistics || {
                        total_bills: 0,
                        total_billed: 0,
                        total_paid: 0,
                        total_outstanding: 0
                    };
                }
            } catch (error) {
                console.error('Error loading billing data:', error);
            } finally {
                this.loading = false;
            }
        },

        formatNumber(value) {
            const num = parseFloat(value);
            return isNaN(num) ? '0.00' : num.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        get filteredData() {
            let filtered = this.data;
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(b =>
                    (b.bill_id && b.bill_id.toString().includes(query)) ||
                    (b.name && b.name.toLowerCase().includes(query)) ||
                    (b.account_no && b.account_no.toLowerCase().includes(query)) ||
                    (b.totalDue && b.totalDue.toString().includes(query))
                );
            }
            if (this.statusFilter) {
                filtered = filtered.filter(b => b.status === this.statusFilter);
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
        viewBill(bill) {
            if (bill.connection_id) {
                window.location.href = `/billing/consumer/${bill.connection_id}`;
            }
        }
    }
}

window.renderBillingDetails = function() {
    // Trigger refresh for Alpine component
    document.dispatchEvent(new CustomEvent('bill-generated'));
};
window.viewBillDetails = function(id) {
    window.location.href = `/billing/consumer/${id}`;
};
</script>
