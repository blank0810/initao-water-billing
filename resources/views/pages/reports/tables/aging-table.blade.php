<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="agingTable()" x-init="init()">
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
                        <i class="fas fa-clock text-[#3D90D7]"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Aging of Accounts</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Accounts receivable aging analysis</p>
                    </div>
                </div>
                <button class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6" x-show="!loading" x-transition>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Current</p>
                            <p class="text-lg font-bold text-[#3D90D7]">₱125,450</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">1-30 Days</p>
                            <p class="text-lg font-bold text-gray-700 dark:text-gray-300">₱45,230</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">31-60 Days</p>
                            <p class="text-lg font-bold text-amber-600">₱32,180</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">61-90 Days</p>
                            <p class="text-lg font-bold text-orange-600">₱18,540</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Over 90 Days</p>
                            <p class="text-lg font-bold text-red-600">₱28,920</p>
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
                        <input type="text" x-model="searchQuery" @input="filterData()"
                            placeholder="Search accounts..."
                            class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    
                    <!-- Right: Filters & Sort -->
                    <div class="flex gap-2">
                        <select x-model="filterAge" @change="filterData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <option value="">All Ages</option>
                            <option value="current">Current</option>
                            <option value="30">1-30 Days</option>
                            <option value="60">31-60 Days</option>
                            <option value="90">61-90 Days</option>
                            <option value="over90">Over 90 Days</option>
                        </select>
                        <select x-model="sortField" @change="sortData()"
                            class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <option value="consumer_name">Name</option>
                            <option value="account_no">Account</option>
                            <option value="total">Total</option>
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Consumer Name</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Account No.</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Current</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">1-30 Days</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-amber-600 dark:text-amber-400">31-60 Days</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-orange-600 dark:text-orange-400">61-90 Days</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-red-600 dark:text-red-400">Over 90</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Total</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="index">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="(currentPage - 1) * pageSize + index + 1"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="row.consumer_name"></td>
                                    <td class="px-4 py-3 text-sm text-center font-mono text-gray-600 dark:text-gray-400" x-text="row.account_no"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="formatCurrency(row.current)"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="formatCurrency(row.days_30)"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-amber-600 dark:text-amber-400" x-text="formatCurrency(row.days_60)"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-orange-600 dark:text-orange-400" x-text="formatCurrency(row.days_90)"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-red-600 dark:text-red-400 font-semibold" x-text="formatCurrency(row.over_90)"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono font-semibold text-[#3D90D7]" x-text="formatCurrency(row.total)"></td>
                                    <td class="px-4 py-3 text-center">
                                        <a :href="'{{ route('reports.aging-accounts') }}?account=' + row.account_no" target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-xs font-medium rounded-lg transition-colors">
                                            <i class="fas fa-external-link-alt mr-1.5"></i>View
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-gray-700 font-semibold">
                                <td colspan="3" class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-200">TOTALS:</td>
                                <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="formatCurrency(totals.current)"></td>
                                <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="formatCurrency(totals.days_30)"></td>
                                <td class="px-4 py-3 text-sm text-right font-mono text-amber-600 dark:text-amber-400" x-text="formatCurrency(totals.days_60)"></td>
                                <td class="px-4 py-3 text-sm text-right font-mono text-orange-600 dark:text-orange-400" x-text="formatCurrency(totals.days_90)"></td>
                                <td class="px-4 py-3 text-sm text-right font-mono text-red-600 dark:text-red-400" x-text="formatCurrency(totals.over_90)"></td>
                                <td class="px-4 py-3 text-sm text-right font-mono font-bold text-[#3D90D7]" x-text="formatCurrency(totals.total)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
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
        function agingTable() {
            return {
                searchQuery: '',
                filterAge: '',
                sortField: 'consumer_name',
                sortDirection: 'asc',
                currentPage: 1,
                pageSize: 10,
                exportFilename: 'aging-of-accounts',
                exportColumns: [
                    { key: 'consumer_name', label: 'Consumer Name' },
                    { key: 'account_no', label: 'Account No.' },
                    { key: 'current', label: 'Current', format: 'currency' },
                    { key: 'days_30', label: '1-30 Days', format: 'currency' },
                    { key: 'days_60', label: '31-60 Days', format: 'currency' },
                    { key: 'days_90', label: '61-90 Days', format: 'currency' },
                    { key: 'over_90', label: 'Over 90 Days', format: 'currency' },
                    { key: 'total', label: 'Total', format: 'currency' },
                ],
                data: [],
                filteredData: [],
                totals: { current: 0, days_30: 0, days_60: 0, days_90: 0, over_90: 0, total: 0 },
                loading: true,
                filtering: false,
                
                init() {
                    setTimeout(() => { this.loading = false; }, 300);
                    // Sample data
                    this.data = [
                        { consumer_name: 'Juan Dela Cruz', account_no: 'ACC-2026-001', current: 450, days_30: 0, days_60: 0, days_90: 0, over_90: 0, total: 450 },
                        { consumer_name: 'Maria Santos', account_no: 'ACC-2026-002', current: 0, days_30: 875, days_60: 0, days_90: 0, over_90: 0, total: 875 },
                        { consumer_name: 'Pedro Reyes', account_no: 'ACC-2026-003', current: 300, days_30: 200, days_60: 150, days_90: 0, over_90: 0, total: 650 },
                        { consumer_name: 'Ana Garcia', account_no: 'ACC-2026-004', current: 0, days_30: 0, days_60: 1105, days_90: 0, days_90: 0, over_90: 0, total: 1105 },
                        { consumer_name: 'Jose Rizal', account_no: 'ACC-2026-005', current: 0, days_30: 0, days_60: 0, days_90: 345, over_90: 0, total: 345 },
                        { consumer_name: 'Rosa Mendoza', account_no: 'ACC-2026-006', current: 510, days_30: 0, days_60: 0, days_90: 0, over_90: 0, total: 510 },
                        { consumer_name: 'Carlos Tan', account_no: 'ACC-2026-007', current: 0, days_30: 0, days_60: 0, days_90: 0, over_90: 855, total: 855 },
                        { consumer_name: 'Linda Cruz', account_no: 'ACC-2026-008', current: 225, days_30: 0, days_60: 0, days_90: 0, over_90: 0, total: 225 },
                        { consumer_name: 'Roberto Gomez', account_no: 'ACC-2026-009', current: 0, days_30: 725, days_60: 0, days_90: 0, over_90: 0, total: 725 },
                        { consumer_name: 'Teresa Aquino', account_no: 'ACC-2026-010', current: 690, days_30: 0, days_60: 0, days_90: 0, over_90: 0, total: 690 },
                        { consumer_name: 'Manuel Reyes', account_no: 'ACC-2026-011', current: 0, days_30: 0, days_60: 450, days_90: 320, over_90: 0, total: 770 },
                        { consumer_name: 'Elena Santos', account_no: 'ACC-2026-012', current: 0, days_30: 0, days_60: 0, days_90: 0, over_90: 1250, total: 1250 },
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
                    
                    if (this.filterAge) {
                        result = result.filter(row => {
                            switch(this.filterAge) {
                                case 'current': return row.current > 0;
                                case '30': return row.days_30 > 0;
                                case '60': return row.days_60 > 0;
                                case '90': return row.days_90 > 0;
                                case 'over90': return row.over_90 > 0;
                                default: return true;
                            }
                        });
                    }
                    
                    this.filteredData = result;
                    this.calculateTotals();
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
                
                calculateTotals() {
                    this.totals = this.filteredData.reduce((acc, row) => {
                        acc.current += row.current;
                        acc.days_30 += row.days_30;
                        acc.days_60 += row.days_60;
                        acc.days_90 += row.days_90;
                        acc.over_90 += row.over_90;
                        acc.total += row.total;
                        return acc;
                    }, { current: 0, days_30: 0, days_60: 0, days_90: 0, over_90: 0, total: 0 });
                },
                
                get paginatedData() {
                    const start = (this.currentPage - 1) * this.pageSize;
                    const end = start + this.pageSize;
                    return this.filteredData.slice(start, end);
                },
                
                get totalPages() {
                    return Math.ceil(this.filteredData.length / this.pageSize);
                },
                
                formatCurrency(value) {
                    return value > 0 ? '₱' + value.toLocaleString('en-PH', { minimumFractionDigits: 2 }) : '-';
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
