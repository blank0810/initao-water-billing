<div x-data="adjustmentsData()" x-init="init()">
    <div class="mb-4 flex items-center justify-between">
        <div class="flex-1 max-w-md">
            <input type="text" x-model="searchQuery" placeholder="Search adjustments..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
        </div>
        <div class="flex items-center gap-2">
            <select x-model="typeFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                <option value="">All Types</option>
                <option value="1">Penalty</option>
                <option value="2">Discount</option>
                <option value="3">Senior Citizen Discount</option>
                <option value="4">Late Fee</option>
                <option value="5">Surcharge</option>
                <option value="6">Waiver</option>
                <option value="7">Correction</option>
            </select>
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

    <x-ui.card>
        <div id="adjustmentsTableWrapper" class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Bill ID</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Remarks</th>
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="adj in paginatedData" :key="adj.bill_adjustment_id">
                            <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="adj.bill_id"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="adj.consumer_name"></td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="adj.direction === '+' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'" x-text="adj.type_name"></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-medium" :class="adj.direction === '+' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                                    <span class="inline-flex items-center">
                                        <i class="fas mr-1 text-xs" :class="adj.direction === '+' ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                                        <span x-text="adj.direction + '₱' + Math.abs(adj.amount).toFixed(2)"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="adj.remarks"></td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="adj.created_at"></td>
                            </tr>
                        </template>
                        <template x-if="paginatedData.length === 0">
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                    <p>No adjustments found</p>
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
function adjustmentsData() {
    return {
        searchQuery: '',
        typeFilter: '',
        pageSize: 10,
        currentPage: 1,
        data: [],
        
        init() {
            this.loadData();
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('typeFilter', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);
        },
        
        loadData() {
            if (!window.billingData) {
                setTimeout(() => this.loadData(), 100);
                return;
            }
            const { billAdjustments, billAdjustmentTypes, waterBillHistory, consumers } = window.billingData;
            this.data = billAdjustments.map(adj => {
                const type = billAdjustmentTypes.find(t => t.bill_adjustment_type_id === adj.bill_adjustment_type_id);
                const bill = waterBillHistory.find(b => b.bill_id === adj.bill_id);
                const consumer = consumers.find(c => c.connection_id === bill?.connection_id);
                return {
                    ...adj,
                    type_name: type?.name || 'N/A',
                    direction: type?.direction || '+',
                    consumer_name: consumer?.name || 'N/A'
                };
            });
        },
        
        get filteredData() {
            let filtered = this.data;
            if (this.searchQuery) {
                filtered = filtered.filter(adj => 
                    adj.bill_id.toString().includes(this.searchQuery) ||
                    adj.remarks.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    adj.consumer_name.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
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
    const event = new CustomEvent('reload-adjustments');
    document.dispatchEvent(event);
};
</script>
