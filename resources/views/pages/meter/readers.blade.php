<div x-data="readersData()" x-init="init()">
    <!-- Search Bar -->
    <div class="mb-6">
        <x-ui.search-bar placeholder="Search by name, ID, or area..." x-model="searchQuery" />
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Reader ID</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Area</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-for="reader in paginatedData" :key="reader.mr_id">
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="reader.mr_id"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="reader.mr_name"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="reader.area"></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="reader.statusClass" x-text="reader.statusLabel"></span>
                            </td>
                        </tr>
                    </template>
                    <template x-if="paginatedData.length === 0">
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>No readers found</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

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
function readersData() {
    return {
        searchQuery: '',
        pageSize: 10,
        currentPage: 1,
        data: [
            { mr_id: 10, mr_name: 'Alex Reader', stat_id: 1, area: 'Brgy. 1-3' },
            { mr_id: 11, mr_name: 'Maria Santos', stat_id: 1, area: 'Brgy. 4-6' },
            { mr_id: 12, mr_name: 'John Cruz', stat_id: 1, area: 'Brgy. 7-9' }
        ],
        
        init() {
            this.data = this.data.map(reader => ({
                ...reader,
                statusLabel: reader.stat_id === 1 ? 'Active' : 'Inactive',
                statusClass: reader.stat_id === 1 ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
            }));
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);
        },
        
        get filteredData() {
            if (!this.searchQuery) return this.data;
            return this.data.filter(r => 
                r.mr_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                r.mr_id.toString().includes(this.searchQuery) ||
                r.area.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
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
