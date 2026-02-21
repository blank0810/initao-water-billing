<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="abstractTable()" x-init="init()">
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
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Abstract of Collection</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View and print collection abstracts</p>
                    </div>
                </div>
                <x-export-dropdown />
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" x-show="!loading" x-transition>
                <template x-if="loading">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <template x-for="i in 4" :key="i">
                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4 animate-pulse">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                                    <div class="flex-1">
                                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-20 mb-2"></div>
                                        <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Records</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="filteredData.length.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Collected</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="'₱' + totalCollected.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Consumers</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="totalConsumers.toLocaleString()"></p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-lg flex items-center justify-center">
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
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

                <!-- Filter Bar -->
                <div class="flex items-center justify-between px-6 pt-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                    <!-- Left: Search -->
                    <div class="relative">
                        <input type="text" x-model="search" @input="filterData()"
                            placeholder="Search consumer..."
                            class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    
                    <!-- Right: Filters & Sort -->
                    <div class="flex gap-2">
                        <input type="date" x-model="dateFilter" @change="filterData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        <select x-model="collectorFilter" @change="filterData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <option value="">All Collectors</option>
                            <option value="Maria Santos">Maria Santos</option>
                            <option value="Juan Dela Cruz">Juan Dela Cruz</option>
                            <option value="Ana Reyes">Ana Reyes</option>
                        </select>
                        <select x-model="sortField" @change="sortData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <option value="date">Date</option>
                            <option value="orNumber">OR Number</option>
                            <option value="consumer">Consumer</option>
                            <option value="collector">Collector</option>
                            <option value="amount">Amount</option>
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">OR Number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Consumer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Collector</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Amount</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="row.id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="(currentPage - 1) * pageSize + index + 1"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="row.orNumber"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="row.date"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="row.consumer"></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300" x-text="row.collector"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-green-600 dark:text-green-400 font-medium" x-text="'₱' + row.amount.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('reports.abstract-collection') }}" target="_blank"
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
    function abstractTable() {
        return {
            search: '',
            dateFilter: '',
            collectorFilter: '',
            pageSize: 10,
            currentPage: 1,
            sortField: 'date',
            sortDirection: 'desc',
            data: [],
            filteredData: [],
            loading: true,
            filtering: false,

            init() {
                setTimeout(() => { this.loading = false; }, 300);
                this.data = [
                    { id: 1, orNumber: 'OR-2025-0001', date: '2025-01-15', consumer: 'Juan Dela Cruz', collector: 'Maria Santos', amount: 1250.50 },
                    { id: 2, orNumber: 'OR-2025-0002', date: '2025-01-15', consumer: 'Pedro Santos', collector: 'Maria Santos', amount: 875.00 },
                    { id: 3, orNumber: 'OR-2025-0003', date: '2025-01-14', consumer: 'Ana Reyes', collector: 'Juan Dela Cruz', amount: 1450.75 },
                    { id: 4, orNumber: 'OR-2025-0004', date: '2025-01-14', consumer: 'Jose Garcia', collector: 'Ana Reyes', amount: 980.25 },
                    { id: 5, orNumber: 'OR-2025-0005', date: '2025-01-13', consumer: 'Maria Lopez', collector: 'Maria Santos', amount: 1125.00 },
                    { id: 6, orNumber: 'OR-2025-0006', date: '2025-01-13', consumer: 'Carlos Martinez', collector: 'Juan Dela Cruz', amount: 2250.50 },
                    { id: 7, orNumber: 'OR-2025-0007', date: '2025-01-12', consumer: 'Rosa Fernandez', collector: 'Ana Reyes', amount: 750.00 },
                    { id: 8, orNumber: 'OR-2025-0008', date: '2025-01-12', consumer: 'Luis Torres', collector: 'Maria Santos', amount: 1675.25 },
                ];
                this.filterData();
            },

            filterData() {
                this.filtering = true;
                let result = [...this.data];
                
                if (this.search) {
                    const query = this.search.toLowerCase();
                    result = result.filter(row => 
                        row.orNumber.toLowerCase().includes(query) ||
                        row.consumer.toLowerCase().includes(query) ||
                        row.collector.toLowerCase().includes(query)
                    );
                }
                
                if (this.dateFilter) {
                    result = result.filter(row => row.date === this.dateFilter);
                }
                
                if (this.collectorFilter) {
                    result = result.filter(row => row.collector === this.collectorFilter);
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

            get totalCollected() {
                return this.filteredData.reduce((sum, row) => sum + row.amount, 0);
            },

            get totalConsumers() {
                return new Set(this.filteredData.map(row => row.consumer)).size;
            },

            get avgCollection() {
                if (this.filteredData.length === 0) return 0;
                return this.totalCollected / this.filteredData.length;
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
