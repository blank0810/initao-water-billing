<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="billingTable()" x-init="init()">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('reports') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-[#3D90D7] transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
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
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Monthly Billing Summary</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View and analyze monthly billing data</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-export-dropdown />
                    <a href="{{ route('reports.billing-summary') }}" target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-sm font-medium rounded-lg transition-colors"
                        title="View Report Template">
                        <i class="fas fa-file-alt mr-2"></i>View Template
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Billed</p>
                    <p class="text-lg font-bold text-[#3D90D7]" x-text="'₱' + totalBilled.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Collected</p>
                    <p class="text-lg font-bold text-green-600" x-text="'₱' + totalCollected.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Outstanding</p>
                    <p class="text-lg font-bold text-red-600" x-text="'₱' + totalOutstanding.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Consumers</p>
                    <p class="text-lg font-bold text-gray-700 dark:text-gray-300" x-text="totalConsumers.toLocaleString()"></p>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                <!-- Filter Bar -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <!-- Search -->
                            <div class="relative">
                                <input type="text" x-model="search" placeholder="Search by schedule or period..."
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

                            <!-- Sort Controls -->
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort by:</label>
                                <select x-model="sortField"
                                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="period">Date/Period</option>
                                    <option value="schedule">Schedule</option>
                                    <option value="consumers">Total Consumers</option>
                                    <option value="volume">Total Volume</option>
                                    <option value="amount">Total Amount</option>
                                    <option value="collected">Paid/Collected</option>
                                    <option value="outstanding">Outstanding</option>
                                </select>
                                <button @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
                                    class="px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg transition-colors bg-white dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <i :class="sortDirection === 'asc' ? 'fas fa-sort-amount-up' : 'fas fa-sort-amount-down'" class="text-gray-400"></i>
                                </button>
                            </div>

                            <!-- Reset -->
                            <button @click="resetFilters()" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg transition-colors bg-white dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600">
                                <i class="fas fa-redo-alt mr-1"></i> Reset
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
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Schedule</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date/Period</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumers</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Volume (m³)</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Total Due</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider">Collected</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">Outstanding</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="row.id">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="(currentPage - 1) * perPage + index + 1"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="row.schedule"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="row.period"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="row.consumers.toLocaleString()"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="row.volume.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="'₱' + row.amount.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="'₱' + row.due.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-green-600 dark:text-green-400 font-semibold" x-text="'₱' + row.collected.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-red-600 dark:text-red-400 font-semibold" x-text="'₱' + row.outstanding.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('reports.billing-summary') }}" target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-xs font-medium rounded-lg transition-colors"
                                            title="View Report Template">
                                            <i class="fas fa-external-link-alt mr-1.5"></i>View Report
                                        </a>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredData.length === 0">
                                <td colspan="10" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                    <p class="text-sm">No billing records found</p>
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
                            <select x-model.number="perPage" @change="currentPage = 1"
                                class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
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
                            Showing <span x-text="Math.min((currentPage - 1) * perPage + 1, filteredData.length)"></span> to
                            <span x-text="Math.min(currentPage * perPage, filteredData.length)"></span> of
                            <span x-text="filteredData.length"></span> results
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
    function billingTable() {
        return {
            search: '',
            monthFilter: '',
            yearFilter: '',
            perPage: 10,
            currentPage: 1,
            sortField: 'period',
            sortDirection: 'desc',

            exportFilename: 'billing-summary',
            exportColumns: [
                { key: 'schedule', label: 'Schedule' },
                { key: 'period', label: 'Date/Period' },
                { key: 'consumers', label: 'Total Consumers', format: 'number' },
                { key: 'volume', label: 'Total Volume (m³)', format: 'number' },
                { key: 'amount', label: 'Total Amount', format: 'currency' },
                { key: 'due', label: 'Total Due', format: 'currency' },
                { key: 'collected', label: 'Paid/Collected', format: 'currency' },
                { key: 'outstanding', label: 'Outstanding', format: 'currency' },
            ],

            // Sample data - replace with actual data from backend
            data: [
                { id: 1, schedule: 'Schedule A', period: 'January 2025', month: '01', year: '2025', consumers: 450, volume: 12500.50, amount: 187500.75, due: 195000.00, collected: 175000.00, outstanding: 20000.00 },
                { id: 2, schedule: 'Schedule B', period: 'January 2025', month: '01', year: '2025', consumers: 380, volume: 9800.25, amount: 147003.75, due: 152000.00, collected: 140000.00, outstanding: 12000.00 },
                { id: 3, schedule: 'Schedule A', period: 'December 2024', month: '12', year: '2024', consumers: 445, volume: 11800.00, amount: 177000.00, due: 185000.00, collected: 172000.00, outstanding: 13000.00 },
                { id: 4, schedule: 'Schedule B', period: 'December 2024', month: '12', year: '2024', consumers: 375, volume: 9500.75, amount: 142511.25, due: 148000.00, collected: 135000.00, outstanding: 13000.00 },
                { id: 5, schedule: 'Schedule A', period: 'November 2024', month: '11', year: '2024', consumers: 440, volume: 11200.50, amount: 168007.50, due: 175000.00, collected: 168000.00, outstanding: 7000.00 },
                { id: 6, schedule: 'Schedule B', period: 'November 2024', month: '11', year: '2024', consumers: 370, volume: 9200.00, amount: 138000.00, due: 143000.00, collected: 138000.00, outstanding: 5000.00 },
            ],

            get filteredData() {
                let result = this.data.filter(row => {
                    const matchesSearch = this.search === '' ||
                        row.schedule.toLowerCase().includes(this.search.toLowerCase()) ||
                        row.period.toLowerCase().includes(this.search.toLowerCase());
                    const matchesMonth = this.monthFilter === '' || row.month === this.monthFilter;
                    const matchesYear = this.yearFilter === '' || row.year === this.yearFilter;
                    return matchesSearch && matchesMonth && matchesYear;
                });

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

            get totalBilled() {
                return this.filteredData.reduce((sum, row) => sum + row.amount, 0);
            },

            get totalCollected() {
                return this.filteredData.reduce((sum, row) => sum + row.collected, 0);
            },

            get totalOutstanding() {
                return this.filteredData.reduce((sum, row) => sum + row.outstanding, 0);
            },

            get totalConsumers() {
                return this.filteredData.reduce((sum, row) => sum + row.consumers, 0);
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
