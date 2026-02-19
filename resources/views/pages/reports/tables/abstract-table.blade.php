<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="abstractTable()">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Back Link -->
            <div class="mb-4">
                <a href="{{ route('reports') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-[#3D90D7] transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Reports
                </a>
            </div>

            <!-- Page Header with Title and Export/Print Buttons -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#3D90D7]/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-[#3D90D7]"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Abstract of Collection</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View and print collection abstracts</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-export-dropdown />
                    <a href="{{ route('reports.abstract-collection') }}" target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
                        title="Download Printable">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(61, 144, 215, 0.1);">
                            <i class="fas fa-file-alt" style="color: #3D90D7;"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Records</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="filteredData.length.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Collected</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="'₱' + totalCollected.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Consumers</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="totalConsumers.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Average Collection</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="'₱' + avgCollection.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
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
                                <input type="text" x-model="search"
                                    placeholder="Search by OR number, collector, consumer..."
                                    class="pl-10 pr-4 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7] focus:border-transparent"
                                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <input type="date" x-model="dateFilter"
                                class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7]"
                                style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                            <select x-model="collectorFilter"
                                class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-[#3D90D7]"
                                style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                <option value="">All Collectors</option>
                                <option value="Maria Santos">Maria Santos</option>
                                <option value="Juan Dela Cruz">Juan Dela Cruz</option>
                                <option value="Ana Reyes">Ana Reyes</option>
                            </select>
                            <!-- Sorting Controls -->
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium" style="color: #ffffff;">Sort by:</label>
                                <select x-model="sortField"
                                    class="px-3 py-2 text-sm border rounded-lg"
                                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                                    <option value="date">Date</option>
                                    <option value="orNumber">OR Number</option>
                                    <option value="consumer">Consumer</option>
                                    <option value="collector">Collector</option>
                                    <option value="amount">Amount</option>
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
                            <button @click="resetFilters()" class="px-4 py-2 text-sm border rounded-lg font-medium transition-colors"
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
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">OR Number</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Collector</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="row.id">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400" x-text="(currentPage - 1) * perPage + index + 1"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="row.orNumber"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="row.date"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="row.consumer"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="row.collector"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-green-600 dark:text-green-400 text-right font-medium" x-text="'₱' + row.amount.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <a href="{{ route('reports.abstract-collection') }}" target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-xs font-medium rounded-lg transition-colors"
                                            title="View Report Template">
                                            <i class="fas fa-external-link-alt mr-1.5"></i>View Report
                                        </a>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredData.length === 0">
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-file-alt text-4xl text-gray-400 mb-2"></i>
                                    <p class="mt-2 text-sm">No collection records found</p>
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
    function abstractTable() {
        return {
            search: '',
            dateFilter: '',
            collectorFilter: '',
            perPage: 10,
            currentPage: 1,
            sortField: 'date',
            sortDirection: 'desc',

            exportFilename: 'abstract-of-collection',
            exportColumns: [
                { key: 'orNumber', label: 'OR Number' },
                { key: 'date', label: 'Date' },
                { key: 'consumer', label: 'Consumer' },
                { key: 'collector', label: 'Collector' },
                { key: 'amount', label: 'Amount', format: 'currency' },
            ],

            // Sample data - replace with actual data from backend
            data: [
                { id: 1, orNumber: 'OR-2025-0001', date: '2025-01-15', consumer: 'Juan Dela Cruz', collector: 'Maria Santos', amount: 1250.50 },
                { id: 2, orNumber: 'OR-2025-0002', date: '2025-01-15', consumer: 'Pedro Santos', collector: 'Maria Santos', amount: 875.00 },
                { id: 3, orNumber: 'OR-2025-0003', date: '2025-01-14', consumer: 'Ana Reyes', collector: 'Juan Dela Cruz', amount: 1450.75 },
                { id: 4, orNumber: 'OR-2025-0004', date: '2025-01-14', consumer: 'Jose Garcia', collector: 'Ana Reyes', amount: 980.25 },
                { id: 5, orNumber: 'OR-2025-0005', date: '2025-01-13', consumer: 'Maria Lopez', collector: 'Maria Santos', amount: 1125.00 },
                { id: 6, orNumber: 'OR-2025-0006', date: '2025-01-13', consumer: 'Carlos Martinez', collector: 'Juan Dela Cruz', amount: 2250.50 },
                { id: 7, orNumber: 'OR-2025-0007', date: '2025-01-12', consumer: 'Rosa Fernandez', collector: 'Ana Reyes', amount: 750.00 },
                { id: 8, orNumber: 'OR-2025-0008', date: '2025-01-12', consumer: 'Luis Torres', collector: 'Maria Santos', amount: 1675.25 },
            ],

            get filteredData() {
                let result = this.data.filter(row => {
                    const matchesSearch = this.search === '' ||
                        row.orNumber.toLowerCase().includes(this.search.toLowerCase()) ||
                        row.consumer.toLowerCase().includes(this.search.toLowerCase()) ||
                        row.collector.toLowerCase().includes(this.search.toLowerCase());
                    const matchesDate = this.dateFilter === '' || row.date === this.dateFilter;
                    const matchesCollector = this.collectorFilter === '' || row.collector === this.collectorFilter;
                    return matchesSearch && matchesDate && matchesCollector;
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

            get totalCollected() {
                return this.filteredData.reduce((sum, row) => sum + row.amount, 0);
            },

            get totalConsumers() {
                return new Set(this.filteredData.map(row => row.consumer)).size;
            },

            get avgCollection() {
                if (this.filteredData.length === 0) return 0;
                return this.totalCollected / this.filteredData.length;
            },

            resetFilters() {
                this.search = '';
                this.dateFilter = '';
                this.collectorFilter = '';
                this.currentPage = 1;
            },

            init() {
                this.$watch('search', () => this.currentPage = 1);
                this.$watch('dateFilter', () => this.currentPage = 1);
                this.$watch('collectorFilter', () => this.currentPage = 1);
                this.$watch('perPage', () => this.currentPage = 1);
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
