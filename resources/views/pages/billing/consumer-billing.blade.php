<div x-data="consumerBillingData()" x-init="init()">
    <!-- Search and Filters -->
    <div class="flex justify-between items-center mb-4">
        <x-ui.search-bar placeholder="Search by name, account no, or location..." x-model="searchQuery" />
        <select x-model="statusFilter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="all">All Status</option>
            <option value="current">Current</option>
            <option value="pending">Pending</option>
            <option value="overdue">Overdue</option>
        </select>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Account No</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Location</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Total Due</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Unpaid Bills</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-for="consumer in paginatedData" :key="consumer.connection_id">
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150" :class="consumer.status === 'Overdue' ? 'bg-red-50 dark:bg-red-900/10' : ''">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="consumer.name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="consumer.meter_serial"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="consumer.account_no"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="consumer.location"></td>
                            <td class="px-4 py-3 text-sm text-right font-bold" :class="consumer.totalDue > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'" x-text="'₱' + consumer.totalDue.toFixed(2)"></td>
                            <td class="px-4 py-3 text-center">
                                <template x-if="consumer.unpaidCount > 0">
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-red-500 rounded-full" x-text="consumer.unpaidCount"></span>
                                </template>
                                <template x-if="consumer.unpaidCount === 0">
                                    <span class="text-gray-400">—</span>
                                </template>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="{
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': consumer.status === 'Overdue',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': consumer.status === 'Pending',
                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': consumer.status === 'Current'
                                }">
                                    <i class="fas mr-1" :class="{
                                        'fa-exclamation-circle': consumer.status === 'Overdue',
                                        'fa-clock': consumer.status === 'Pending',
                                        'fa-check-circle': consumer.status === 'Current'
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
                    <template x-if="paginatedData.length === 0">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>No consumers found</p>
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
function consumerBillingData() {
    return {
        searchQuery: '',
        statusFilter: 'all',
        pageSize: 10,
        currentPage: 1,
        data: [],
        
        init() {
            this.loadData();
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('statusFilter', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);
        },
        
        loadData() {
            if (!window.billingData) {
                setTimeout(() => this.loadData(), 100);
                return;
            }
            const { consumers, waterBillHistory } = window.billingData;
            const STATUSES = window.billing?.STATUSES || { ACTIVE: 1, PAID: 2, OVERDUE: 4 };
            
            this.data = consumers.map(consumer => {
                const bills = waterBillHistory.filter(b => b.connection_id === consumer.connection_id && (b.stat_id === STATUSES.ACTIVE || b.stat_id === STATUSES.OVERDUE));
                const totalDue = bills.reduce((sum, b) => sum + b.total_amount, 0);
                const unpaidCount = bills.length;
                const hasOverdue = bills.some(b => b.stat_id === STATUSES.OVERDUE || new Date(b.due_date) < new Date());
                return {
                    ...consumer,
                    totalDue,
                    unpaidCount,
                    status: hasOverdue ? 'Overdue' : unpaidCount > 0 ? 'Pending' : 'Current'
                };
            });
        },
        
        get filteredData() {
            let filtered = this.data;
            if (this.searchQuery) {
                filtered = filtered.filter(c => 
                    c.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    c.account_no.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    c.location.toLowerCase().includes(this.searchQuery.toLowerCase())
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
</script>
