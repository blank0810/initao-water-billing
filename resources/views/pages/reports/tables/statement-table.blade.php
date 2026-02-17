<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="statementTable()">
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
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-[#3D90D7]"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Consumers</p>
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
                            <p class="text-xs text-gray-500 dark:text-gray-400">Zero Balance</p>
                            <p class="text-lg font-bold text-green-600" x-text="zeroBalance.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">With Balance</p>
                            <p class="text-lg font-bold text-red-600" x-text="withBalance.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                <!-- Filter Bar - Dark Mode -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700" style="background-color: #1f2937; border-bottom: 1px solid #374151;">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <!-- Search & Filters -->
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <input type="text" x-model="search" placeholder="Search by account, consumer..."
                                    class="pl-10 pr-4 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7] focus:border-transparent"
                                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <select x-model="balanceFilter"
                                class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7]"
                                style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                <option value="">All</option>
                                <option value="zero">Zero Balance</option>
                                <option value="with">With Balance</option>
                            </select>
                            <select x-model="typeFilter"
                                class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7]"
                                style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                <option value="">All Types</option>
                                <option value="Residential">Residential</option>
                                <option value="Commercial">Commercial</option>
                                <option value="Government">Government</option>
                            </select>
                            <!-- Sorting Controls -->
                            <div class="flex items-center gap-2">
                                <select x-model="sortField"
                                    class="px-3 py-2 text-sm border rounded-lg"
                                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                    <option value="accountNo">Account No.</option>
                                    <option value="consumer">Consumer Name</option>
                                    <option value="address">Address</option>
                                    <option value="type">Type</option>
                                    <option value="balance">Balance</option>
                                </select>
                                <button @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
                                    class="px-2 py-2 text-sm border rounded-lg transition-colors"
                                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;"
                                    onmouseover="this.style.backgroundColor='#4b5563'"
                                    onmouseout="this.style.backgroundColor='#374151'">
                                    <i :class="sortDirection === 'asc' ? 'fas fa-sort-amount-up' : 'fas fa-sort-amount-down'" class="text-gray-400"></i>
                                </button>
                            </div>
                            <button @click="resetFilters()"
                                class="px-4 py-2 text-sm border rounded-lg font-medium transition-colors"
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
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Account No.</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Address</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Balance</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Last Payment</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="row.id">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400" x-text="(currentPage - 1) * perPage + index + 1"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="row.accountNo"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="row.consumer"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="row.address"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': row.type === 'Residential',
                                                'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400': row.type === 'Commercial',
                                                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': row.type === 'Government'
                                            }"
                                            x-text="row.type">
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium"
                                        :class="row.balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'"
                                        x-text="'₱' + row.balance.toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="row.lastPayment"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <a href="{{ route('reports.billing-statement') }}" target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-xs font-medium rounded-lg transition-colors"
                                           title="View Report Template">
                                            <i class="fas fa-external-link-alt mr-1.5"></i>View Report
                                        </a>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredData.length === 0">
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p class="mt-2 text-sm">No statements found</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination & Per Page (Inside Card Bottom) -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <!-- Per Page Control (Left) -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Show</label>
                            <select x-model.number="perPage"
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
                            <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage >= totalPages"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-chevron-right"></i>
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
    function statementTable() {
        return {
            search: '',
            balanceFilter: '',
            typeFilter: '',
            perPage: 10,
            currentPage: 1,
            sortField: 'accountNo',
            sortDirection: 'asc',

            exportFilename: 'billing-statement',
            exportColumns: [
                { key: 'accountNo', label: 'Account No.' },
                { key: 'consumer', label: 'Consumer Name' },
                { key: 'address', label: 'Address' },
                { key: 'type', label: 'Type' },
                { key: 'balance', label: 'Balance', format: 'currency' },
                { key: 'lastPayment', label: 'Last Payment' },
            ],

            // Sample data - replace with actual data from backend
            data: [
                { id: 1, accountNo: 'ACC-001', consumer: 'Juan Dela Cruz', address: 'Purok 1, Barangay Centro', type: 'Residential', balance: 0, lastPayment: '2025-01-15' },
                { id: 2, accountNo: 'ACC-002', consumer: 'Pedro Santos', address: 'Purok 2, Barangay Norte', type: 'Residential', balance: 1250.50, lastPayment: '2024-12-20' },
                { id: 3, accountNo: 'ACC-003', consumer: 'Ana Reyes', address: 'Purok 3, Barangay Sur', type: 'Commercial', balance: 0, lastPayment: '2025-01-14' },
                { id: 4, accountNo: 'ACC-004', consumer: 'Jose Garcia', address: 'Purok 1, Barangay Este', type: 'Residential', balance: 2450.75, lastPayment: '2024-11-30' },
                { id: 5, accountNo: 'ACC-005', consumer: 'Maria Lopez', address: 'Purok 4, Barangay Oeste', type: 'Commercial', balance: 0, lastPayment: '2025-01-10' },
                { id: 6, accountNo: 'ACC-006', consumer: 'Municipal Hall', address: 'Barangay Centro', type: 'Government', balance: 5680.00, lastPayment: '2024-10-15' },
                { id: 7, accountNo: 'ACC-007', consumer: 'Carlos Martinez', address: 'Purok 5, Barangay Centro', type: 'Residential', balance: 875.25, lastPayment: '2024-12-28' },
                { id: 8, accountNo: 'ACC-008', consumer: 'Rosa Fernandez', address: 'Purok 2, Barangay Norte', type: 'Residential', balance: 0, lastPayment: '2025-01-12' },
            ],

            get filteredData() {
                let result = this.data.filter(row => {
                    const matchesSearch = this.search === '' ||
                        row.accountNo.toLowerCase().includes(this.search.toLowerCase()) ||
                        row.consumer.toLowerCase().includes(this.search.toLowerCase()) ||
                        row.address.toLowerCase().includes(this.search.toLowerCase());
                    const matchesBalance = this.balanceFilter === '' ||
                        (this.balanceFilter === 'zero' && row.balance === 0) ||
                        (this.balanceFilter === 'with' && row.balance > 0);
                    const matchesType = this.typeFilter === '' || row.type === this.typeFilter;
                    return matchesSearch && matchesBalance && matchesType;
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

            get totalBalance() {
                return this.filteredData.reduce((sum, row) => sum + row.balance, 0);
            },

            get zeroBalance() {
                return this.filteredData.filter(row => row.balance === 0).length;
            },

            get withBalance() {
                return this.filteredData.filter(row => row.balance > 0).length;
            },

            resetFilters() {
                this.search = '';
                this.balanceFilter = '';
                this.typeFilter = '';
                this.currentPage = 1;
            },

            init() {
                this.$watch('search', () => this.currentPage = 1);
                this.$watch('balanceFilter', () => this.currentPage = 1);
                this.$watch('typeFilter', () => this.currentPage = 1);
                this.$watch('perPage', () => this.currentPage = 1);
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
