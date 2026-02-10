<div x-data="adjustmentsData()" x-init="init()">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <!-- Period Filter -->
            <select x-model="periodFilter" @change="loadData()" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                <option value="">All Periods</option>
                <template x-for="period in periods" :key="period.per_id">
                    <option :value="period.per_id" x-text="period.per_name"></option>
                </template>
            </select>

            <!-- Area Filter -->
            <select x-model="areaFilter" @change="loadData()" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                <option value="">All Areas</option>
                <template x-for="area in areas" :key="area.a_id">
                    <option :value="area.a_id" x-text="area.a_desc"></option>
                </template>
            </select>

            <!-- Type Filter -->
            <select x-model="typeFilter" @change="currentPage = 1" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                <option value="">All Types</option>
                <template x-for="type in adjustmentTypes" :key="type.bill_adjustment_type_id">
                    <option :value="type.bill_adjustment_type_id" x-text="type.name"></option>
                </template>
            </select>

            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <input type="text" x-model="searchQuery" placeholder="Search by bill ID, consumer, remarks..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button @click="loadData()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm" title="Refresh">
                <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
            </button>
            <button onclick="billing.printTable('adjustmentsTableWrapper')" class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm" title="Print">
                <i class="fas fa-print"></i>
            </button>
            <button onclick="billing.exportToPDF('adjustmentsTableWrapper', 'Adjustments')" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" title="Download PDF">
                <i class="fas fa-download"></i>
            </button>
            <button onclick="billing.exportToExcel('adjustmentsTableWrapper', 'adjustments')" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm" title="Export to Excel">
                <i class="fas fa-file-export"></i>
            </button>
            <x-ui.button variant="primary" icon="fas fa-plus" onclick="openAddAdjustmentModal()">Add Adjustment</x-ui.button>
        </div>
    </div>

    <!-- Data Table -->
    <x-ui.card>
        <div id="adjustmentsTableWrapper" class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Bill ID</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Period</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Area</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Remarks</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <!-- Loading State -->
                        <tr x-show="loading">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Loading adjustments...
                            </td>
                        </tr>
                        <!-- Empty State -->
                        <tr x-show="!loading && paginatedData.length === 0">
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>No adjustments found</p>
                            </td>
                        </tr>
                        <!-- Data Rows -->
                        <template x-for="adj in paginatedData" :key="adj.bill_adjustment_id">
                            <tr x-show="!loading" class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="adj.bill_id"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    <div x-text="adj.consumer_name"></div>
                                    <div class="text-xs text-gray-500" x-text="adj.account_no"></div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="adj.period_name"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="adj.area_desc"></td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="adj.direction === 'debit' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'" x-text="adj.type_name"></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-medium" :class="adj.direction === 'debit' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                                    <span class="inline-flex items-center">
                                        <i class="fas mr-1 text-xs" :class="adj.direction === 'debit' ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                                        <span x-text="(adj.direction === 'debit' ? '+' : '-') + '₱' + Math.abs(adj.amount).toFixed(2)"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate" x-text="adj.remarks" :title="adj.remarks"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="adj.created_at"></td>
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
    </x-ui.card>
</div>

<script>
function adjustmentsData() {
    return {
        searchQuery: '',
        periodFilter: '',
        areaFilter: '',
        typeFilter: '',
        pageSize: 10,
        currentPage: 1,
        data: [],
        periods: [],
        areas: [],
        adjustmentTypes: [],
        loading: true,

        async init() {
            // Load filter options
            await Promise.all([
                this.loadPeriods(),
                this.loadAreas(),
                this.loadAdjustmentTypes()
            ]);

            // Load initial data
            await this.loadData();

            // Watch for search changes
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('typeFilter', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);

            // Listen for adjustment created event
            document.addEventListener('adjustment-created', () => this.loadData());
        },

        async loadPeriods() {
            try {
                const response = await fetch('/water-bills/billing-periods');
                const result = await response.json();
                if (result.success) {
                    this.periods = result.data;
                }
            } catch (error) {
                console.error('Failed to load periods:', error);
            }
        },

        async loadAreas() {
            try {
                const response = await fetch('/areas/list');
                const result = await response.json();
                if (result.success && result.data) {
                    this.areas = result.data;
                }
            } catch (error) {
                console.error('Failed to load areas:', error);
            }
        },

        async loadAdjustmentTypes() {
            try {
                const response = await fetch('/bill-adjustments/types');
                const result = await response.json();
                if (result.success && result.data) {
                    this.adjustmentTypes = result.data;
                }
            } catch (error) {
                console.error('Failed to load adjustment types:', error);
            }
        },

        async loadData() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.periodFilter) params.append('period_id', this.periodFilter);
                if (this.areaFilter) params.append('area_id', this.areaFilter);

                let url = '/bill-adjustments';
                if (params.toString()) url += '?' + params.toString();

                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    this.data = result.data;
                } else {
                    console.error('Failed to load adjustments:', result.message);
                    this.data = [];
                }
            } catch (error) {
                console.error('Failed to load adjustments:', error);
                this.data = [];
            } finally {
                this.loading = false;
                this.currentPage = 1;
            }
        },

        get filteredData() {
            let filtered = this.data;

            // Apply search filter (client-side for responsiveness)
            if (this.searchQuery) {
                const search = this.searchQuery.toLowerCase();
                filtered = filtered.filter(adj =>
                    adj.bill_id.toString().includes(search) ||
                    (adj.remarks || '').toLowerCase().includes(search) ||
                    (adj.consumer_name || '').toLowerCase().includes(search) ||
                    (adj.account_no || '').toLowerCase().includes(search)
                );
            }

            // Apply type filter (client-side)
            if (this.typeFilter) {
                filtered = filtered.filter(adj => adj.bill_adjustment_type_id == this.typeFilter);
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

window.renderAdjustments = function() {
    document.dispatchEvent(new CustomEvent('adjustment-created'));
};
</script>
