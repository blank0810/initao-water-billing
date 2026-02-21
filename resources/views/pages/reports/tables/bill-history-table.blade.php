<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="billHistoryTable()" x-init="init()">
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
                        <i class="fas fa-history text-[#3D90D7]"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Bill History</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View and print consumer bill history</p>
                    </div>
                </div>
                <x-export-dropdown />
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" x-show="!loading" x-transition>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-invoice text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Bills</p>
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
                            <p class="text-xs text-gray-500 dark:text-gray-400">Paid Bills</p>
                            <p class="text-lg font-bold text-green-600" x-text="paidBills.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Unpaid Bills</p>
                            <p class="text-lg font-bold text-amber-600" x-text="unpaidBills.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-coins text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Amount</p>
                            <p class="text-lg font-bold text-red-600" x-text="'₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
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
                            placeholder="Search account..."
                            class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    
                    <!-- Right: Filters & Sort -->
                    <div class="flex gap-2">
                        <select x-model="statusFilter" @change="filterData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <option value="">All Status</option>
                            <option value="Paid">Paid</option>
                            <option value="Unpaid">Unpaid</option>
                            <option value="Partial">Partial</option>
                        </select>
                        <select x-model="monthFilter" @change="filterData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
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
                        <select x-model="sortField" @change="sortData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <option value="period">Period</option>
                            <option value="billNo">Bill No.</option>
                            <option value="consumer">Consumer</option>
                            <option value="amount">Amount</option>
                            <option value="status">Status</option>
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Bill No.</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Account No.</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Consumer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Period</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Amount</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="row.id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="(currentPage - 1) * pageSize + index + 1"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="row.billNo"></td>
                                    <td class="px-4 py-3 text-sm font-mono text-gray-600 dark:text-gray-400" x-text="row.accountNo"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="row.consumer"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="row.period"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="'₱' + row.amount.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': row.status === 'Paid',
                                                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': row.status === 'Unpaid',
                                                'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400': row.status === 'Partial'
                                            }"
                                            x-text="row.status">
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('reports.water-bill-history') }}" target="_blank"
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
    function billHistoryTable() {
        return {
            search: '',
            statusFilter: '',
            monthFilter: '',
            pageSize: 10,
            currentPage: 1,
            sortField: 'period',
            sortDirection: 'desc',
            data: [],
            filteredData: [],
            loading: true,
            filtering: false,

            init() {
                setTimeout(() => { this.loading = false; }, 300);
                this.data = [
                    { id: 1, billNo: 'BILL-2025-0001', accountNo: 'ACC-001', consumer: 'Juan Dela Cruz', period: 'January 2025', month: '01', amount: 1250.50, status: 'Paid' },
                    { id: 2, billNo: 'BILL-2025-0002', accountNo: 'ACC-002', consumer: 'Pedro Santos', period: 'January 2025', month: '01', amount: 875.00, status: 'Unpaid' },
                    { id: 3, billNo: 'BILL-2025-0003', accountNo: 'ACC-003', consumer: 'Ana Reyes', period: 'January 2025', month: '01', amount: 1450.75, status: 'Paid' },
                    { id: 4, billNo: 'BILL-2024-0124', accountNo: 'ACC-001', consumer: 'Juan Dela Cruz', period: 'December 2024', month: '12', amount: 1180.25, status: 'Paid' },
                    { id: 5, billNo: 'BILL-2024-0125', accountNo: 'ACC-002', consumer: 'Pedro Santos', period: 'December 2024', month: '12', amount: 920.00, status: 'Partial' },
                    { id: 6, billNo: 'BILL-2024-0126', accountNo: 'ACC-003', consumer: 'Ana Reyes', period: 'December 2024', month: '12', amount: 1320.50, status: 'Paid' },
                    { id: 7, billNo: 'BILL-2024-0101', accountNo: 'ACC-004', consumer: 'Jose Garcia', period: 'November 2024', month: '11', amount: 980.00, status: 'Unpaid' },
                    { id: 8, billNo: 'BILL-2024-0102', accountNo: 'ACC-005', consumer: 'Maria Lopez', period: 'November 2024', month: '11', amount: 1125.25, status: 'Paid' },
                ];
                this.filterData();
            },

            filterData() {
                this.filtering = true;
                let result = [...this.data];
                
                if (this.search) {
                    const query = this.search.toLowerCase();
                    result = result.filter(row => 
                        row.billNo.toLowerCase().includes(query) ||
                        row.accountNo.toLowerCase().includes(query) ||
                        row.consumer.toLowerCase().includes(query)
                    );
                }
                
                if (this.statusFilter) {
                    result = result.filter(row => row.status === this.statusFilter);
                }
                
                if (this.monthFilter) {
                    result = result.filter(row => row.month === this.monthFilter);
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

            get totalAmount() {
                return this.filteredData.reduce((sum, row) => sum + row.amount, 0);
            },

            get paidBills() {
                return this.filteredData.filter(row => row.status === 'Paid').length;
            },

            get unpaidBills() {
                return this.filteredData.filter(row => row.status === 'Unpaid').length;
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
