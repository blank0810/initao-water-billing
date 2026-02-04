<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('reports') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-[#3D90D7] transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Reports
                </a>
            </div>

            <!-- Page Header with Title and Export Button -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-[#3D90D7]"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Account Masterlist</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Complete consumer master list</p>
                    </div>
                </div>
                <!-- Export/Download Button -->
                <div class="flex items-center gap-2">
                    <button onclick="exportToExcel()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </button>
                    <button onclick="exportToPDF()" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Accounts</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">2,458</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Active</p>
                            <p class="text-lg font-bold text-green-600">2,180</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-pause-circle text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Inactive</p>
                            <p class="text-lg font-bold text-gray-600">145</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Disconnected</p>
                            <p class="text-lg font-bold text-red-600">133</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden" x-data="masterlistTable()" x-init="init()">
                
                <!-- Filter Bar - Dark Mode -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700" style="background-color: #1f2937; border-bottom: 1px solid #374151;">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <!-- Search & Filters -->
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <input type="text" x-model="searchQuery" @input="filterData()"
                                    placeholder="Search by name or account..."
                                    class="pl-10 pr-4 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7] focus:border-transparent"
                                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <select x-model="filterType" @change="filterData()"
                                class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7]"
                                style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                <option value="">All Types</option>
                                <option value="Residential">Residential</option>
                                <option value="Commercial">Commercial</option>
                                <option value="Industrial">Industrial</option>
                            </select>
                            <!-- Date Filter -->
                            <input type="date" x-model="filterDate" @change="filterData()"
                                class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7]"
                                style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                            <select x-model="filterStatus" @change="filterData()"
                                class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7]"
                                style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                <option value="">All Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Disconnected">Disconnected</option>
                            </select>
                            <!-- Sorting Controls (moved from bottom) -->
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium" style="color: #ffffff;">Sort by:</label>
                                <select x-model="sortField" @change="sortData()"
                                    class="px-3 py-2 text-sm border rounded-lg"
                                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                    <option value="consumer_name">Consumer Name</option>
                                    <option value="account_no">Account No.</option>
                                    <option value="address">Address</option>
                                    <option value="type">Type</option>
                                    <option value="status">Status</option>
                                </select>
                                <button @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'; sortData()"
                                    class="px-2 py-2 text-sm border rounded-lg transition-colors"
                                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;"
                                    onmouseover="this.style.backgroundColor='#4b5563'"
                                    onmouseout="this.style.backgroundColor='#374151'">
                                    <i :class="sortDirection === 'asc' ? 'fas fa-sort-amount-up' : 'fas fa-sort-amount-down'" class="text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Account No.</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Address</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter No.</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="index">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="(currentPage - 1) * pageSize + index + 1"></td>
                                    <td class="px-4 py-3 text-sm font-mono text-[#3D90D7]" x-text="row.account_no"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="row.consumer_name"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="row.address"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span :class="{
                                            'bg-blue-100 text-blue-800': row.type === 'Residential',
                                            'bg-purple-100 text-purple-800': row.type === 'Commercial',
                                            'bg-orange-100 text-orange-800': row.type === 'Industrial'
                                        }" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" x-text="row.type"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span :class="{
                                            'bg-green-100 text-green-800': row.status === 'Active',
                                            'bg-gray-100 text-gray-800': row.status === 'Inactive',
                                            'bg-red-100 text-red-800': row.status === 'Disconnected'
                                        }" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" x-text="row.status"></span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center font-mono text-gray-600 dark:text-gray-400" x-text="row.meter_no"></td>
                                    <td class="px-4 py-3 text-center">
                                        <a :href="'{{ route('reports.consumer-master-list') }}?account=' + row.account_no" target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-xs font-medium rounded-lg transition-colors"
                                           title="View Report Template">
                                            <i class="fas fa-external-link-alt mr-1.5"></i>View Report
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination & Per Page (Below Table) -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <!-- Per Page Control (Left) -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Show</label>
                            <select x-model.number="pageSize" @change="currentPage = 1"
                                class="px-3 py-1.5 text-sm border rounded-lg"
                                style="background-color: #ffffff; border-color: #d1d5db; color: #111827;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">entries</label>
                        </div>

                        <!-- Pagination Controls (Center) -->
                        <div class="flex items-center gap-2">
                            <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400">Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span></span>
                            <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Results Info (Right) -->
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Showing <span x-text="((currentPage - 1) * pageSize) + 1"></span> to 
                            <span x-text="Math.min(currentPage * pageSize, filteredData.length)"></span> of 
                            <span x-text="filteredData.length"></span> results
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function masterlistTable() {
            return {
                searchQuery: '',
                filterType: '',
                filterStatus: '',
                filterDate: '',
                sortField: 'consumer_name',
                sortDirection: 'asc',
                currentPage: 1,
                pageSize: 10,
                data: [],
                filteredData: [],
                
                init() {
                    this.data = [
                        { account_no: 'ACC-2026-001', consumer_name: 'Juan Dela Cruz', address: 'Poblacion, Initao', type: 'Residential', status: 'Active', meter_no: 'MTR-001' },
                        { account_no: 'ACC-2026-002', consumer_name: 'Maria Santos', address: 'Cogon, Initao', type: 'Residential', status: 'Active', meter_no: 'MTR-002' },
                        { account_no: 'ACC-2026-003', consumer_name: 'Pedro Reyes', address: 'Baybay, Initao', type: 'Residential', status: 'Active', meter_no: 'MTR-003' },
                        { account_no: 'ACC-2026-004', consumer_name: 'Ana Garcia Trading', address: 'Poblacion, Initao', type: 'Commercial', status: 'Active', meter_no: 'MTR-004' },
                        { account_no: 'ACC-2026-005', consumer_name: 'Jose Rizal', address: 'Cogon, Initao', type: 'Residential', status: 'Inactive', meter_no: 'MTR-005' },
                        { account_no: 'ACC-2026-006', consumer_name: 'Rosa Mendoza', address: 'Baybay, Initao', type: 'Residential', status: 'Active', meter_no: 'MTR-006' },
                        { account_no: 'ACC-2026-007', consumer_name: 'Tan Industries', address: 'Poblacion, Initao', type: 'Industrial', status: 'Active', meter_no: 'MTR-007' },
                        { account_no: 'ACC-2026-008', consumer_name: 'Linda Cruz', address: 'Cogon, Initao', type: 'Residential', status: 'Disconnected', meter_no: 'MTR-008' },
                        { account_no: 'ACC-2026-009', consumer_name: 'Roberto Gomez', address: 'Baybay, Initao', type: 'Residential', status: 'Active', meter_no: 'MTR-009' },
                        { account_no: 'ACC-2026-010', consumer_name: 'Teresa Aquino Store', address: 'Poblacion, Initao', type: 'Commercial', status: 'Active', meter_no: 'MTR-010' },
                        { account_no: 'ACC-2026-011', consumer_name: 'Manuel Reyes', address: 'Cogon, Initao', type: 'Residential', status: 'Active', meter_no: 'MTR-011' },
                        { account_no: 'ACC-2026-012', consumer_name: 'Elena Santos', address: 'Baybay, Initao', type: 'Residential', status: 'Inactive', meter_no: 'MTR-012' },
                    ];
                    this.filterData();
                },
                
                filterData() {
                    let result = [...this.data];
                    
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        result = result.filter(row => 
                            row.consumer_name.toLowerCase().includes(query) ||
                            row.account_no.toLowerCase().includes(query)
                        );
                    }
                    
                    if (this.filterType) {
                        result = result.filter(row => row.type === this.filterType);
                    }
                    
                    if (this.filterStatus) {
                        result = result.filter(row => row.status === this.filterStatus);
                    }
                    
                    this.filteredData = result;
                    this.sortData();
                },
                
                sortData() {
                    this.filteredData.sort((a, b) => {
                        let aVal = a[this.sortField];
                        let bVal = b[this.sortField];
                        
                        if (typeof aVal === 'string') {
                            aVal = aVal.toLowerCase();
                            bVal = bVal.toLowerCase();
                        }
                        
                        if (this.sortDirection === 'asc') {
                            return aVal > bVal ? 1 : -1;
                        } else {
                            return aVal < bVal ? 1 : -1;
                        }
                    });
                },
                
                get paginatedData() {
                    const start = (this.currentPage - 1) * this.pageSize;
                    const end = start + this.pageSize;
                    return this.filteredData.slice(start, end);
                },
                
                get totalPages() {
                    return Math.ceil(this.filteredData.length / this.pageSize);
                }
            };
        }
        
        function exportToExcel() {
            alert('Exporting to Excel...');
            // Implement actual export functionality
        }
        
        function exportToPDF() {
            alert('Exporting to PDF...');
            // Implement actual export functionality
        }
    </script>
    @endpush
</x-app-layout>
