<div x-data="allocationsData()" x-init="init()">
    <div class="mb-4 flex gap-2 justify-end">
        <button onclick="billing.exportToExcel('allocationsTableWrapper', 'allocations')" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm" title="Export to Excel">
            <i class="fas fa-file-excel"></i>
        </button>
        <button onclick="billing.exportToPDF('allocationsTableWrapper', 'Payment Allocations')" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" title="Export to PDF">
            <i class="fas fa-file-pdf"></i>
        </button>
        <button onclick="billing.printTable('allocationsTableWrapper')" class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm" title="Print">
            <i class="fas fa-print"></i>
        </button>
    </div>

    <x-ui.card>
        <div id="allocationsTableWrapper" class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Receipt No</th>
                            <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Target Type</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Target ID</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount Applied</th>
                            <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="alloc in paginatedData" :key="alloc.payment_allocation_id">
                            <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="alloc.receipt_no"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="alloc.target_type === 'BILL' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'">
                                        <i class="fas mr-1" :class="alloc.target_type === 'BILL' ? 'fa-file-invoice' : 'fa-dollar-sign'"></i>
                                        <span x-text="alloc.target_type"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="alloc.target_id"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="alloc.consumer_name"></td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-gray-100" x-text="'₱' + alloc.amount_applied.toFixed(2)"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="alloc.isFullyApplied ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'" x-text="alloc.isFullyApplied ? 'Fully Applied' : 'Partially Applied'"></span>
                                </td>
                            </tr>
                        </template>
                        <template x-if="paginatedData.length === 0">
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                    <p>No allocations found</p>
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
    </x-ui.card>
</div>

<script>
function allocationsData() {
    return {
        pageSize: 10,
        currentPage: 1,
        data: [],
        
        init() {
            this.loadData();
            this.$watch('pageSize', () => this.currentPage = 1);
        },
        
        loadData() {
            if (!window.billingData) {
                setTimeout(() => this.loadData(), 100);
                return;
            }
            const { paymentAllocations, payments, consumers, waterBillHistory } = window.billingData;
            const TARGET_TYPES = window.billing?.TARGET_TYPES || { BILL: 'BILL', CHARGE: 'CHARGE' };
            
            this.data = paymentAllocations.map(alloc => {
                const payment = payments.find(p => p.payment_id === alloc.payment_id);
                const consumer = consumers.find(c => c.connection_id === alloc.connection_id);
                const bill = waterBillHistory.find(b => b.bill_id === alloc.target_id);
                const isFullyApplied = bill && alloc.amount_applied >= bill.total_amount;
                
                return {
                    ...alloc,
                    receipt_no: payment?.receipt_no || 'N/A',
                    consumer_name: consumer?.name || 'N/A',
                    isFullyApplied
                };
            });
        },
        
        get filteredData() { return this.data; },
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

window.renderAllocations = function() {
    const event = new CustomEvent('reload-allocations');
    document.dispatchEvent(event);
};
</script>
