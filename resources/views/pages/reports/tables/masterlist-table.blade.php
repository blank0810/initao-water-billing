<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="masterlistTable()" x-init="init()">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('reports') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-[#3D90D7] transition-colors group">
                    <i class="fas fa-chevron-left mr-2 text-gray-600 dark:text-gray-400 group-hover:drop-shadow-[0_0_8px_rgba(59,130,246,0.6)] transition-all"></i>
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
                <x-export-dropdown />
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" x-show="!loading" x-transition>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Accounts</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">2,458</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Active</p>
                            <p class="text-lg font-bold text-green-600">2,180</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
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
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
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
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
                
                <!-- Filter Bar -->
                <div class="flex items-center justify-between px-6 pt-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @input="filterData()"
                            placeholder="Search account or name..."
                            class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <div class="flex gap-2">
                        <select x-model="filterType" @change="filterData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <option value="">All Types</option>
                            <option value="Residential">Residential</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Industrial">Industrial</option>
                        </select>
                        <select x-model="filterStatus" @change="filterData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <option value="">All Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Disconnected">Disconnected</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto relative">
                    <!-- Loading Overlay -->
                    <div x-show="filtering" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm z-10 flex items-center justify-center">
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Filtering...</span>
                        </div>
                    </div>
                    <table class="min-w-full">
                        <thead class="bg-white dark:bg-gray-900">
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Account No.</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Consumer Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Address</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Type</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Meter No.</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="index">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
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
                                           class="inline-flex items-center px-3 py-1.5 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-xs font-medium rounded-lg transition-colors">
                                            <i class="fas fa-external-link-alt mr-1.5"></i>View
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''"
                            class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                            Previous
                        </button>
                        <span class="text-sm text-gray-700 dark:text-gray-400">
                            Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                        </span>
                        <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages"
                            :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''"
                            class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                            Next
                        </button>
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
                exportFilename: 'consumer-master-list',
                exportColumns: [
                    { key: 'account_no', label: 'Account No.' },
                    { key: 'consumer_name', label: 'Consumer Name' },
                    { key: 'address', label: 'Address' },
                    { key: 'type', label: 'Type' },
                    { key: 'status', label: 'Status' },
                    { key: 'meter_no', label: 'Meter No.' },
                ],
                data: [],
                filteredData: [],
                loading: true,
                filtering: false,

                init() {
                    setTimeout(() => { this.loading = false; }, 300);
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
                    this.filtering = true;
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
                    setTimeout(() => { this.filtering = false; }, 100);
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
        
    </script>
    @endpush
</x-app-layout>
