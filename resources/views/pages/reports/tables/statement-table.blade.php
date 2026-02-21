<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="statementTable()" x-init="init()">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('reports') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-[#3D90D7] transition-colors group">
                    <i class="fas fa-chevron-left mr-2 text-gray-600 dark:text-gray-400 group-hover:drop-shadow-[0_0_8px_rgba(59,130,246,0.6)] transition-all"></i>
                    Back to Reports
                </a>
            </div>

            <!-- Page Header with Title -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-[#3D90D7]"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Statement of Account</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View and print consumer statements of account</p>
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
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Consumers</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="filteredData.length.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Zero Balance</p>
                            <p class="text-lg font-bold text-green-600" x-text="zeroBalance.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">With Balance</p>
                            <p class="text-lg font-bold text-red-600" x-text="withBalance.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-coins text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Outstanding</p>
                            <p class="text-lg font-bold text-amber-600" x-text="'₱' + totalBalance.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

                <!-- Filter Bar -->
                <div class="flex items-center justify-between px-6 pt-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                    <!-- Left: Search -->
                    <div class="relative">
                        <input type="text" x-model="search" @input="filterData()"
                            placeholder="Search by account..."
                            class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    
                    <!-- Right: Filters & Sort -->
                    <div class="flex gap-2">
                        <select x-model="balanceFilter" @change="filterData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <option value="">All</option>
                            <option value="zero">Zero Balance</option>
                            <option value="with">With Balance</option>
                        </select>
                        <select x-model="typeFilter" @change="filterData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <option value="">All Types</option>
                            <option value="Residential">Residential</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Government">Government</option>
                        </select>
                        <select x-model="sortField" @change="sortData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <option value="accountNo">Account No.</option>
                            <option value="consumer">Consumer Name</option>
                            <option value="address">Address</option>
                            <option value="type">Type</option>
                            <option value="balance">Balance</option>
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Type</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Balance</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Last Payment</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="row.id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="(currentPage - 1) * pageSize + index + 1"></td>
                                    <td class="px-4 py-3 text-sm font-mono text-[#3D90D7]" x-text="row.accountNo"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="row.consumer"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="row.address"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': row.type === 'Residential',
                                                'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400': row.type === 'Commercial',
                                                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': row.type === 'Government'
                                            }"
                                            x-text="row.type">
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-mono font-medium"
                                        :class="row.balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'"
                                        x-text="'₱' + row.balance.toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="row.lastPayment"></td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('reports.billing-statement') }}" target="_blank"
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
    function statementTable() {
        return {
            search: '',
            balanceFilter: '',
            typeFilter: '',
            pageSize: 10,
            currentPage: 1,
            sortField: 'accountNo',
            sortDirection: 'asc',
            data: [],
            filteredData: [],
            loading: true,
            filtering: false,

            init() {
                setTimeout(() => { this.loading = false; }, 300);
                this.data = [
                    { id: 1, accountNo: 'ACC-001', consumer: 'Juan Dela Cruz', address: 'Purok 1, Barangay Centro', type: 'Residential', balance: 0, lastPayment: '2025-01-15' },
                    { id: 2, accountNo: 'ACC-002', consumer: 'Pedro Santos', address: 'Purok 2, Barangay Norte', type: 'Residential', balance: 1250.50, lastPayment: '2024-12-20' },
                    { id: 3, accountNo: 'ACC-003', consumer: 'Ana Reyes', address: 'Purok 3, Barangay Sur', type: 'Commercial', balance: 0, lastPayment: '2025-01-14' },
                    { id: 4, accountNo: 'ACC-004', consumer: 'Jose Garcia', address: 'Purok 1, Barangay Este', type: 'Residential', balance: 2450.75, lastPayment: '2024-11-30' },
                    { id: 5, accountNo: 'ACC-005', consumer: 'Maria Lopez', address: 'Purok 4, Barangay Oeste', type: 'Commercial', balance: 0, lastPayment: '2025-01-10' },
                    { id: 6, accountNo: 'ACC-006', consumer: 'Municipal Hall', address: 'Barangay Centro', type: 'Government', balance: 5680.00, lastPayment: '2024-10-15' },
                    { id: 7, accountNo: 'ACC-007', consumer: 'Carlos Martinez', address: 'Purok 5, Barangay Centro', type: 'Residential', balance: 875.25, lastPayment: '2024-12-28' },
                    { id: 8, accountNo: 'ACC-008', consumer: 'Rosa Fernandez', address: 'Purok 2, Barangay Norte', type: 'Residential', balance: 0, lastPayment: '2025-01-12' },
                ];
                this.filterData();
            },

            filterData() {
                this.filtering = true;
                let result = [...this.data];
                
                if (this.search) {
                    const query = this.search.toLowerCase();
                    result = result.filter(row => 
                        row.accountNo.toLowerCase().includes(query) ||
                        row.consumer.toLowerCase().includes(query) ||
                        row.address.toLowerCase().includes(query)
                    );
                }
                
                if (this.balanceFilter) {
                    if (this.balanceFilter === 'zero') {
                        result = result.filter(row => row.balance === 0);
                    } else if (this.balanceFilter === 'with') {
                        result = result.filter(row => row.balance > 0);
                    }
                }
                
                if (this.typeFilter) {
                    result = result.filter(row => row.type === this.typeFilter);
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
                return this.filteredData.slice(start, start + this.pageSize);
            },

            get totalPages() {
                return Math.ceil(this.filteredData.length / this.pageSize) || 1;
            },

            get totalBalance() {
                return this.filteredData.reduce((sum, row) => sum + row.balance, 0);
            },

            get zeroBalance() {
                return this.filteredData.filter(row => row.balance === 0).length;
            },

            get withBalance() {
                return this.filteredData.filter(row => row.balance > 0).length;
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
