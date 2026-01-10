<div x-data="collectionsData()" x-init="init()">
    <!-- Search and Filters -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <x-ui.search-bar placeholder="Search by receipt, consumer, or amount..." x-model="searchQuery" />
            </div>
            <div class="sm:w-48">
                <select x-model="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Status</option>
                    <option value="posted">Posted</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="sm:w-48">
                <input type="date" x-model="dateFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
        </div>
    </div>

<!-- Collection Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Today's Collection</p>
                <p id="todayCollection" class="text-3xl font-bold mt-2">₱0.00</p>
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
                <p id="monthCollection" class="text-3xl font-bold mt-2">₱0.00</p>
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
                <p id="totalTransactions" class="text-3xl font-bold mt-2">0</p>
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
                <p id="avgPayment" class="text-3xl font-bold mt-2">₱0.00</p>
            </div>
            <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
        </div>
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
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Payment Method</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-for="payment in paginatedData" :key="payment.payment_id">
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="payment.receipt_no"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="payment.payment_date"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="payment.consumer_name"></td>
                            <td class="px-4 py-3 text-sm text-right font-bold text-green-600 dark:text-green-400" x-text="'₱' + payment.amount_received.toFixed(2)"></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                                    <i class="fas fa-money-bill mr-1"></i><span x-text="payment.payment_method"></span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="payment.status === 'Posted' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200'">
                                    <i class="fas mr-1" :class="payment.status === 'Posted' ? 'fa-check-circle' : 'fa-clock'"></i><span x-text="payment.status"></span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="viewPayment(payment.payment_id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="paginatedData.length === 0">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>No collections found</p>
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
function collectionsData() {
    return {
        searchQuery: '',
        statusFilter: 'all',
        dateFilter: '',
        pageSize: 10,
        currentPage: 1,
        data: [],
        
        init() {
            this.loadData();
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('statusFilter', () => this.currentPage = 1);
            this.$watch('dateFilter', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);
        },
        
        loadData() {
            if (!window.billingData) {
                setTimeout(() => this.loadData(), 100);
                return;
            }
            const { payments, consumers } = window.billingData;
            this.data = payments.map(p => {
                const consumer = consumers.find(c => c.connection_id === p.payer_id);
                return {
                    ...p,
                    consumer_name: consumer?.name || 'N/A',
                    payment_method: 'Cash',
                    status: p.stat_id === 1 ? 'Posted' : 'Pending'
                };
            });
            this.updateSummaryCards();
        },
        
        updateSummaryCards() {
            const today = new Date().toISOString().split('T')[0];
            const todayPayments = this.data.filter(p => p.payment_date === today);
            const todayTotal = todayPayments.reduce((sum, p) => sum + p.amount_received, 0);
            
            const currentMonth = new Date().getMonth();
            const monthPayments = this.data.filter(p => new Date(p.payment_date).getMonth() === currentMonth);
            const monthTotal = monthPayments.reduce((sum, p) => sum + p.amount_received, 0);
            
            const avgPayment = this.data.length > 0 ? this.data.reduce((sum, p) => sum + p.amount_received, 0) / this.data.length : 0;
            
            document.getElementById('todayCollection').textContent = '₱' + todayTotal.toFixed(2);
            document.getElementById('monthCollection').textContent = '₱' + monthTotal.toFixed(2);
            document.getElementById('totalTransactions').textContent = this.data.length;
            document.getElementById('avgPayment').textContent = '₱' + avgPayment.toFixed(2);
        },
        
        get filteredData() {
            let filtered = this.data;
            if (this.searchQuery) {
                filtered = filtered.filter(p => 
                    p.receipt_no.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    p.consumer_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    p.amount_received.toString().includes(this.searchQuery)
                );
            }
            if (this.statusFilter !== 'all') {
                filtered = filtered.filter(p => p.status.toLowerCase() === this.statusFilter.toLowerCase());
            }
            if (this.dateFilter) {
                filtered = filtered.filter(p => p.payment_date === this.dateFilter);
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
        viewPayment(id) {
            if (window.showToast) showToast(`Viewing Payment #${id}`, 'info');
            else alert(`Viewing Payment #${id}`);
        }
    }
}

window.renderCollections = function() {
    const event = new CustomEvent('reload-collections');
    document.dispatchEvent(event);
};
</script>
