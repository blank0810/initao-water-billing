<div x-data="billingDetailsData()" x-init="init()">
    <!-- Search and Filters -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <x-ui.search-bar placeholder="Search by reference, consumer, or amount..." x-model="searchQuery" />
            </div>
            <div class="sm:w-48">
                <select x-model="monthFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Months</option>
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
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
                    <template x-for="bill in paginatedData" :key="bill.bill_id">
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150" :class="bill.stat_id === 4 ? 'bg-red-50 dark:bg-red-900/10' : ''">
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="'BILL-' + bill.bill_id"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="bill.consumer_name"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="bill.bill_date"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="bill.consumption.toFixed(3) + ' m³'"></td>
                            <td class="px-4 py-3 text-sm text-right font-bold" :class="bill.stat_id === 4 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100'" x-text="'₱' + bill.total_amount.toFixed(2)"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="bill.due_date"></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full" :class="bill.statusClass">
                                    <i class="fas mr-1" :class="bill.statusIcon"></i><span x-text="bill.statusText"></span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="viewBill(bill.bill_id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="paginatedData.length === 0">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>No bills found</p>
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
    const statusMap = { 1: 'Unpaid', 2: 'Paid', 3: 'Cancelled', 4: 'Overdue', 5: 'Adjusted' };
    const statusClass = { 1: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200', 2: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200', 3: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300', 4: 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200', 5: 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200' };
    const statusIcon = { 1: 'fa-clock', 2: 'fa-check-circle', 3: 'fa-ban', 4: 'fa-exclamation-circle', 5: 'fa-edit' };
    
    return {
        searchQuery: '',
        monthFilter: '',
        pageSize: 10,
        currentPage: 1,
        data: [],
        
        init() {
            this.loadData();
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('monthFilter', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);
        },
        
        loadData() {
            if (!window.billingData) {
                setTimeout(() => this.loadData(), 100);
                return;
            }
            const { waterBillHistory, consumers } = window.billingData;
            this.data = waterBillHistory.map(bill => {
                const consumer = consumers.find(c => c.connection_id === bill.connection_id);
                return {
                    ...bill,
                    consumer_name: consumer?.name || 'N/A',
                    statusText: statusMap[bill.stat_id],
                    statusClass: statusClass[bill.stat_id],
                    statusIcon: statusIcon[bill.stat_id]
                };
            });
        },
        
        get filteredData() {
            let filtered = this.data;
            if (this.searchQuery) {
                filtered = filtered.filter(b => 
                    b.bill_id.toString().includes(this.searchQuery) ||
                    b.consumer_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    b.total_amount.toString().includes(this.searchQuery)
                );
            }
            if (this.monthFilter) {
                filtered = filtered.filter(b => b.bill_date.split('-')[1] === this.monthFilter);
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
        viewBill(id) {
            if (window.showToast) showToast(`Viewing Bill #${id}`, 'info');
            else alert(`Viewing Bill #${id}`);
        }
    }
}

window.renderBillingDetails = function() {
    const event = new CustomEvent('reload-billing-details');
    document.dispatchEvent(event);
};
window.viewBillDetails = function(id) {
    if (window.showToast) showToast(`Viewing Bill #${id}`, 'info');
    else alert(`Viewing Bill #${id}`);
};
</script>
