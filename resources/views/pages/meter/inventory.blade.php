<div x-data="inventoryData()" x-init="init()">
    <!-- Search and Filters -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <x-ui.search-bar placeholder="Search by serial, brand, or consumer..." x-model="searchQuery" />
            </div>
            <div class="sm:w-48">
                <select x-model="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Status</option>
                    <option value="available">Available</option>
                    <option value="installed">Installed</option>
                    <option value="faulty">Faulty</option>
                    <option value="removed">Removed</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter ID</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Serial Number</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Brand</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-for="item in paginatedData" :key="item.mtr_id">
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="item.mtr_id"></td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="item.mtr_serial"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                <span x-text="item.mtr_brand"></span>
                                <template x-if="item.consumer">
                                    <div class="text-xs text-gray-500 dark:text-gray-400" x-text="item.consumer"></div>
                                </template>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="item.statusClass" x-text="item.statusLabel"></span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="openInventoryModal(item.mtr_id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="paginatedData.length === 0">
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>No inventory found</p>
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

@include('components.ui.meter.inventory-modal')
@include('components.ui.meter.add-meter-modal')

<script>
function inventoryData() {
    const statusConfig = {
        available: { label: 'Available', class: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' },
        installed: { label: 'Installed', class: 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200' },
        faulty: { label: 'Faulty', class: 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200' },
        removed: { label: 'Removed', class: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }
    };
    
    return {
        searchQuery: '',
        statusFilter: 'all',
        pageSize: 10,
        currentPage: 1,
        data: [
            { mtr_id: 5001, mtr_serial: 'MTR-XYZ-12345', mtr_brand: 'AquaMeter', status: 'available' },
            { mtr_id: 5002, mtr_serial: 'MTR-ABC-67890', mtr_brand: 'FlowTech', status: 'available' },
            { mtr_id: 5006, mtr_serial: 'MTR-MNO-99887', mtr_brand: 'AquaMeter', status: 'available' },
            { mtr_id: 5009, mtr_serial: 'MTR-VWX-33221', mtr_brand: 'WaterPro', status: 'available' },
            { mtr_id: 5010, mtr_serial: 'MTR-YZA-44332', mtr_brand: 'FlowTech', status: 'available' },
            { mtr_id: 5012, mtr_serial: 'MTR-EFG-66554', mtr_brand: 'AquaMeter', status: 'available' },
            { mtr_id: 5013, mtr_serial: 'MTR-HIJ-77665', mtr_brand: 'FlowTech', status: 'available' },
            { mtr_id: 5014, mtr_serial: 'MTR-KLM-88776', mtr_brand: 'WaterPro', status: 'available' },
            { mtr_id: 5003, mtr_serial: 'MTR-DEF-11223', mtr_brand: 'AquaMeter', status: 'installed', consumer: 'Gelogo, Norben' },
            { mtr_id: 5004, mtr_serial: 'MTR-GHI-44556', mtr_brand: 'WaterPro', status: 'installed', consumer: 'Sayson, Sarah' },
            { mtr_id: 5008, mtr_serial: 'MTR-STU-22110', mtr_brand: 'FlowTech', status: 'installed', consumer: 'Ramos, Angela' },
            { mtr_id: 5005, mtr_serial: 'MTR-JKL-77889', mtr_brand: 'FlowTech', status: 'faulty', consumer: 'Apora, Jose' },
            { mtr_id: 5011, mtr_serial: 'MTR-BCD-55443', mtr_brand: 'AquaMeter', status: 'faulty', consumer: 'Santos, Maria' },
            { mtr_id: 5007, mtr_serial: 'MTR-PQR-55443', mtr_brand: 'WaterPro', status: 'removed', removed_date: '2024-01-10' },
            { mtr_id: 5015, mtr_serial: 'MTR-NOP-99001', mtr_brand: 'AquaMeter', status: 'removed', removed_date: '2023-12-15' }
        ],
        
        init() {
            this.data = this.data.map(item => ({
                ...item,
                statusLabel: statusConfig[item.status].label,
                statusClass: statusConfig[item.status].class
            }));
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('statusFilter', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);
        },
        
        get filteredData() {
            let filtered = this.data;
            if (this.searchQuery) {
                filtered = filtered.filter(m => 
                    m.mtr_serial.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    m.mtr_brand.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (m.consumer && m.consumer.toLowerCase().includes(this.searchQuery.toLowerCase()))
                );
            }
            if (this.statusFilter !== 'all') {
                filtered = filtered.filter(m => m.status === this.statusFilter);
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
        openInventoryModal(id) { if (window.openInventoryModal) window.openInventoryModal(id); }
    }
}
</script>
