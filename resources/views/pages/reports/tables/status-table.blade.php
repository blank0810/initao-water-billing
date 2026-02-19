<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="statusTable()">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('reports') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-[#3D90D7] transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Reports
                </a>
            </div>

            <!-- Page Header with Title and Buttons -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-bar text-[#3D90D7]"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Monthly Status Report</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View and analyze monthly connection status data</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-export-dropdown />
                    <a href="{{ route('reports.summary-status') }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-sm font-medium rounded-lg transition-colors"
                       title="View Report Template">
                        <i class="fas fa-file-alt mr-2"></i>View Template
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-plug text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Connections</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="totalConnections.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-ban text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Disconnections</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="totalDisconnections.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(61, 144, 215, 0.1);">
                            <i class="fas fa-sync" style="color: #3D90D7;"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Reconnections</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="totalReconnections.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-coins text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Uncollected</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="'₱' + totalUncollected.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                <!-- Filter Bar -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <!-- Search with icon overlay -->
                            <div class="relative">
                                <input type="text" x-model="search" placeholder="Search by period..."
                                    class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#3D90D7] focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>

                            <!-- Month Filter -->
                            <select x-model="monthFilter"
                                class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#3D90D7] bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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

                            <!-- Year Filter -->
                            <select x-model="yearFilter"
                                class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#3D90D7] bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">All Years</option>
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                            </select>

                            <!-- Sorting Controls -->
                            <div class="flex items-center gap-2">
                                <select x-model="sortField"
                                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="period">Date/Period</option>
                                    <option value="connections">Connections</option>
                                    <option value="disconnections">Disconnections</option>
                                    <option value="reconnections">Reconnections</option>
                                    <option value="consumers">Total Consumers</option>
                                    <option value="billingAmount">Billing Amount</option>
                                    <option value="collectionAmount">Collection Amount</option>
                                    <option value="uncollected">Uncollected</option>
                                </select>
                                <button @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
                                    class="px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg transition-colors bg-white dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600">
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
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date/Period</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Connections</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Disconnections</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Reconnections</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Total Consumers</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Billing Amount</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Collection Amount</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Uncollected/Outstanding</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="row.id">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="(currentPage - 1) * perPage + index + 1"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="row.period"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400" x-text="row.connections"></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400" x-text="row.disconnections"></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background-color: #3D90D7;" x-text="row.reconnections"></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 text-right" x-text="row.consumers.toLocaleString()"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 text-right" x-text="'₱' + row.billingAmount.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-green-600 dark:text-green-400 text-right font-medium" x-text="'₱' + row.collectionAmount.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 dark:text-red-400 text-right font-medium" x-text="'₱' + row.uncollected.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <a href="{{ route('reports.summary-status') }}" target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-xs font-medium rounded-lg transition-colors"
                                           title="View Report Template">
                                            <i class="fas fa-external-link-alt mr-1.5"></i>View Report
                                        </a>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredData.length === 0">
                                <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    <p class="mt-2 text-sm">No status records found</p>
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
                                class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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
    function statusTable() {
        return {
            search: '',
            monthFilter: '',
            yearFilter: '',
            perPage: 10,
            currentPage: 1,
            sortField: 'period',
            sortDirection: 'desc',

            exportFilename: 'monthly-status-report',
            exportColumns: [
                { key: 'period', label: 'Date/Period' },
                { key: 'connections', label: 'Connections', format: 'number' },
                { key: 'disconnections', label: 'Disconnections', format: 'number' },
                { key: 'reconnections', label: 'Reconnections', format: 'number' },
                { key: 'consumers', label: 'Total Consumers', format: 'number' },
                { key: 'billingAmount', label: 'Billing Amount', format: 'currency' },
                { key: 'collectionAmount', label: 'Collection Amount', format: 'currency' },
                { key: 'uncollected', label: 'Uncollected', format: 'currency' },
            ],

            // Sample data - replace with actual data from backend
            data: [
                { id: 1, period: 'January 2025', month: '01', year: '2025', connections: 15, disconnections: 8, reconnections: 5, consumers: 842, billingAmount: 347000.00, collectionAmount: 315000.00, uncollected: 32000.00 },
                { id: 2, period: 'December 2024', month: '12', year: '2024', connections: 12, disconnections: 10, reconnections: 6, consumers: 835, billingAmount: 333000.00, collectionAmount: 307000.00, uncollected: 26000.00 },
                { id: 3, period: 'November 2024', month: '11', year: '2024', connections: 18, disconnections: 5, reconnections: 3, consumers: 833, billingAmount: 318000.00, collectionAmount: 306000.00, uncollected: 12000.00 },
                { id: 4, period: 'October 2024', month: '10', year: '2024', connections: 10, disconnections: 7, reconnections: 4, consumers: 820, billingAmount: 316000.00, collectionAmount: 298000.00, uncollected: 18000.00 },
                { id: 5, period: 'September 2024', month: '09', year: '2024', connections: 14, disconnections: 6, reconnections: 8, consumers: 817, billingAmount: 310000.00, collectionAmount: 295000.00, uncollected: 15000.00 },
                { id: 6, period: 'August 2024', month: '08', year: '2024', connections: 11, disconnections: 9, reconnections: 5, consumers: 809, billingAmount: 312000.00, collectionAmount: 290000.00, uncollected: 22000.00 },
            ],

            get filteredData() {
                let result = this.data.filter(row => {
                    const matchesSearch = this.search === '' ||
                        row.period.toLowerCase().includes(this.search.toLowerCase());
                    const matchesMonth = this.monthFilter === '' || row.month === this.monthFilter;
                    const matchesYear = this.yearFilter === '' || row.year === this.yearFilter;
                    return matchesSearch && matchesMonth && matchesYear;
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

            get totalConnections() {
                return this.filteredData.reduce((sum, row) => sum + row.connections, 0);
            },

            get totalDisconnections() {
                return this.filteredData.reduce((sum, row) => sum + row.disconnections, 0);
            },

            get totalReconnections() {
                return this.filteredData.reduce((sum, row) => sum + row.reconnections, 0);
            },

            get totalUncollected() {
                return this.filteredData.reduce((sum, row) => sum + row.uncollected, 0);
            },

            resetFilters() {
                this.search = '';
                this.monthFilter = '';
                this.yearFilter = '';
                this.currentPage = 1;
            },

            init() {
                this.$watch('search', () => this.currentPage = 1);
                this.$watch('monthFilter', () => this.currentPage = 1);
                this.$watch('yearFilter', () => this.currentPage = 1);
                this.$watch('perPage', () => this.currentPage = 1);
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
