<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="billHistoryTable()">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('reports') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-[#3D90D7] transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Reports
                </a>
            </div>

            <!-- Page Header with Title and Actions -->
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
                <div class="flex items-center gap-2">
                    <x-export-dropdown />
                    <a href="{{ route('reports.water-bill-history') }}" target="_blank"
                       class="inline-flex items-center px-3 py-1.5 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-xs font-medium rounded-lg transition-colors"
                       title="View Report Template">
                        <i class="fas fa-print mr-1.5"></i>Print
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-invoice text-[#3D90D7]"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Bills</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="filteredData.length.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Paid Bills</p>
                            <p class="text-lg font-bold text-green-600" x-text="paidBills.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Unpaid Bills</p>
                            <p class="text-lg font-bold text-amber-600" x-text="unpaidBills.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                <!-- Filter Bar - Dark Mode -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700" style="background-color: #1f2937; border-bottom: 1px solid #374151;">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <!-- Search & Filters -->
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <input type="text" x-model="search" placeholder="Search by bill number, consumer, account..."
                                    class="pl-10 pr-4 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7] focus:border-transparent"
                                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <select x-model="statusFilter"
                                class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7]"
                                style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                <option value="">All Status</option>
                                <option value="Paid">Paid</option>
                                <option value="Unpaid">Unpaid</option>
                                <option value="Partial">Partial</option>
                            </select>
                            <select x-model="monthFilter"
                                class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7]"
                                style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
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
                            <!-- Sorting Controls -->
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium" style="color: #ffffff;">Sort by:</label>
                                <select x-model="sortField"
                                    class="px-3 py-2 text-sm border rounded-lg"
                                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                    <option value="period">Period</option>
                                    <option value="billNo">Bill No.</option>
                                    <option value="consumer">Consumer</option>
                                    <option value="amount">Amount</option>
                                    <option value="status">Status</option>
                                </select>
                                <button @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
                                    class="px-2 py-2 text-sm border rounded-lg transition-colors"
                                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;"
                                    onmouseover="this.style.backgroundColor='#4b5563'"
                                    onmouseout="this.style.backgroundColor='#374151'">
                                    <i :class="sortDirection === 'asc' ? 'fas fa-sort-amount-up' : 'fas fa-sort-amount-down'" class="text-gray-400"></i>
                                </button>
                            </div>
                            <!-- Reset -->
                            <button @click="resetFilters()" class="px-3 py-2 text-sm border rounded-lg transition-colors"
                                style="background-color: #374151; border-color: #4b5563; color: #ffffff;"
                                onmouseover="this.style.backgroundColor='#4b5563'"
                                onmouseout="this.style.backgroundColor='#374151'">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Bill No.</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Account No.</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Period</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="row.id">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="(currentPage - 1) * perPage + index + 1"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="row.billNo"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="row.accountNo"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="row.consumer"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="row.period"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 text-right font-medium" x-text="'₱' + row.amount.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
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
                                           class="inline-flex items-center px-3 py-1.5 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-xs font-medium rounded-lg transition-colors"
                                           title="View Report Template">
                                            <i class="fas fa-print mr-1.5"></i>Print
                                        </a>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredData.length === 0">
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-file-invoice fa-3x text-gray-300 dark:text-gray-600 mb-3"></i>
                                    <p class="mt-2 text-sm">No bills found</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination & Per Page (Below Table) -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <!-- Per Page Control (Left) -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Show</label>
                            <select x-model="perPage"
                                class="px-3 py-1.5 text-sm border rounded-lg"
                                style="background-color: #ffffff; border-color: #d1d5db; color: #111827;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">entries</label>
                        </div>

                        <!-- Pagination Controls (Center) -->
                        <div class="flex items-center gap-2">
                            <button @click="currentPage = 1" :disabled="currentPage === 1"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-angle-double-left"></i>
                            </button>
                            <button @click="currentPage--" :disabled="currentPage === 1"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400">
                                Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                            </span>
                            <button @click="currentPage++" :disabled="currentPage >= totalPages"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <button @click="currentPage = totalPages" :disabled="currentPage >= totalPages"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-angle-double-right"></i>
                            </button>
                        </div>

                        <!-- Results Info (Right) -->
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Showing <span x-text="Math.min((currentPage - 1) * perPage + 1, filteredData.length)"></span>
                            to <span x-text="Math.min(currentPage * perPage, filteredData.length)"></span>
                            of <span x-text="filteredData.length"></span> results
                        </p>
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
            perPage: 10,
            currentPage: 1,
            sortField: 'period',
            sortDirection: 'desc',

            exportFilename: 'bill-history',
            exportColumns: [
                { key: 'billNo', label: 'Bill No.' },
                { key: 'accountNo', label: 'Account No.' },
                { key: 'consumer', label: 'Consumer' },
                { key: 'period', label: 'Period' },
                { key: 'amount', label: 'Amount', format: 'currency' },
                { key: 'status', label: 'Status' },
            ],

            // Sample data - replace with actual data from backend
            data: [
                { id: 1, billNo: 'BILL-2025-0001', accountNo: 'ACC-001', consumer: 'Juan Dela Cruz', period: 'January 2025', month: '01', amount: 1250.50, status: 'Paid' },
                { id: 2, billNo: 'BILL-2025-0002', accountNo: 'ACC-002', consumer: 'Pedro Santos', period: 'January 2025', month: '01', amount: 875.00, status: 'Unpaid' },
                { id: 3, billNo: 'BILL-2025-0003', accountNo: 'ACC-003', consumer: 'Ana Reyes', period: 'January 2025', month: '01', amount: 1450.75, status: 'Paid' },
                { id: 4, billNo: 'BILL-2024-0124', accountNo: 'ACC-001', consumer: 'Juan Dela Cruz', period: 'December 2024', month: '12', amount: 1180.25, status: 'Paid' },
                { id: 5, billNo: 'BILL-2024-0125', accountNo: 'ACC-002', consumer: 'Pedro Santos', period: 'December 2024', month: '12', amount: 920.00, status: 'Partial' },
                { id: 6, billNo: 'BILL-2024-0126', accountNo: 'ACC-003', consumer: 'Ana Reyes', period: 'December 2024', month: '12', amount: 1320.50, status: 'Paid' },
                { id: 7, billNo: 'BILL-2024-0101', accountNo: 'ACC-004', consumer: 'Jose Garcia', period: 'November 2024', month: '11', amount: 980.00, status: 'Unpaid' },
                { id: 8, billNo: 'BILL-2024-0102', accountNo: 'ACC-005', consumer: 'Maria Lopez', period: 'November 2024', month: '11', amount: 1125.25, status: 'Paid' },
            ],

            get filteredData() {
                let result = this.data.filter(row => {
                    const matchesSearch = this.search === '' ||
                        row.billNo.toLowerCase().includes(this.search.toLowerCase()) ||
                        row.accountNo.toLowerCase().includes(this.search.toLowerCase()) ||
                        row.consumer.toLowerCase().includes(this.search.toLowerCase());
                    const matchesStatus = this.statusFilter === '' || row.status === this.statusFilter;
                    const matchesMonth = this.monthFilter === '' || row.month === this.monthFilter;
                    return matchesSearch && matchesStatus && matchesMonth;
                });

                // Sort
                result.sort((a, b) => {
                    let aVal = a[this.sortField];
                    let bVal = b[this.sortField];
                    if (typeof aVal === 'string') {
                        aVal = aVal.toLowerCase();
                        bVal = bVal.toLowerCase();
                    }
                    if (this.sortDirection === 'asc') {
                        return aVal > bVal ? 1 : -1;
                    }
                    return aVal < bVal ? 1 : -1;
                });

                return result;
            },

            get paginatedData() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.filteredData.slice(start, start + this.perPage);
            },

            get totalPages() {
                return Math.ceil(this.filteredData.length / this.perPage) || 1;
            },

            get totalAmount() {
                return this.filteredData.reduce((sum, row) => sum + row.amount, 0);
            },

            get paidBills() {
                return this.filteredData.filter(row => row.status === 'Paid').length;
            },

            get unpaidBills() {
                return this.filteredData.filter(row => row.status === 'Unpaid').length;
            },

            resetFilters() {
                this.search = '';
                this.statusFilter = '';
                this.monthFilter = '';
                this.currentPage = 1;
            },

            init() {
                this.$watch('search', () => this.currentPage = 1);
                this.$watch('statusFilter', () => this.currentPage = 1);
                this.$watch('monthFilter', () => this.currentPage = 1);
                this.$watch('perPage', () => this.currentPage = 1);
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
