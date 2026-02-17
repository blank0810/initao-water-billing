<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="agingTable()" x-init="init()">
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
                        <i class="fas fa-clock text-[#3D90D7]"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Aging of Accounts</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Accounts receivable aging analysis</p>
                    </div>
                </div>
                <x-export-dropdown />
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Current</p>
                    <p class="text-lg font-bold text-[#3D90D7]">₱125,450</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">1-30 Days</p>
                    <p class="text-lg font-bold text-gray-700 dark:text-gray-300">₱45,230</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">31-60 Days</p>
                    <p class="text-lg font-bold text-amber-600">₱32,180</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">61-90 Days</p>
                    <p class="text-lg font-bold text-orange-600">₱18,540</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Over 90 Days</p>
                    <p class="text-lg font-bold text-red-600">₱28,920</p>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <!-- Filter Bar -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <!-- Search & Filters -->
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <input type="text" x-model="searchQuery" @input="filterData()"
                                    placeholder="Search accounts..."
                                    class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#3D90D7] focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <select x-model="filterAge" @change="filterData()"
                                class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#3D90D7] bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">All Ages</option>
                                <option value="current">Current</option>
                                <option value="30">1-30 Days</option>
                                <option value="60">31-60 Days</option>
                                <option value="90">61-90 Days</option>
                                <option value="over90">Over 90 Days</option>
                            </select>
                            <!-- Sorting Controls (moved from bottom) -->
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort by:</label>
                                <select x-model="sortField" @change="sortData()"
                                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="consumer_name">Consumer Name</option>
                                    <option value="account_no">Account No.</option>
                                    <option value="current">Current</option>
                                    <option value="days_30">1-30 Days</option>
                                    <option value="days_60">31-60 Days</option>
                                    <option value="days_90">61-90 Days</option>
                                    <option value="over_90">Over 90 Days</option>
                                    <option value="total">Total</option>
                                </select>
                                <button @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'; sortData()"
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
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer Name</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Account No.</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Current</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">1-30 Days</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">31-60 Days</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wider">61-90 Days</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">Over 90</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider bg-[#3D90D7]/5">Total</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(row, index) in paginatedData" :key="index">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" x-text="(currentPage - 1) * pageSize + index + 1"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="row.consumer_name"></td>
                                    <td class="px-4 py-3 text-sm text-center font-mono text-gray-600 dark:text-gray-400" x-text="row.account_no"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="formatCurrency(row.current)"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="formatCurrency(row.days_30)"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-amber-600 dark:text-amber-400" x-text="formatCurrency(row.days_60)"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-orange-600 dark:text-orange-400" x-text="formatCurrency(row.days_90)"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-red-600 dark:text-red-400 font-semibold" x-text="formatCurrency(row.over_90)"></td>
                                    <td class="px-4 py-3 text-sm text-right font-mono font-semibold text-[#3D90D7] bg-[#3D90D7]/5" x-text="formatCurrency(row.total)"></td>
                                    <td class="px-4 py-3 text-center">
                                        <a :href="'{{ route('reports.aging-accounts') }}?account=' + row.account_no" target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 bg-[#3D90D7] hover:bg-[#3580c0] text-white text-xs font-medium rounded-lg transition-colors"
                                           title="View Report Template">
                                            <i class="fas fa-external-link-alt mr-1.5"></i>View Report
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
                                <td class="px-4 py-3 text-sm text-right font-mono font-bold text-[#3D90D7] bg-[#3D90D7]/10" x-text="formatCurrency(totals.total)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Pagination & Per Page (Below Table) -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <!-- Per Page Control (Left) -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Show</label>
                            <select x-model.number="pageSize" @change="currentPage = 1"
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
                            <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages"
                                class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Results Info (Right) -->
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Showing <span x-text="((currentPage - 1) * pageSize) + 1"></span> to 
                            <span x-text="Math.min(currentPage * pageSize, filteredData.length)"></span> of 
                            <span x-text="filteredData.length"></span> results
                        </p>
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
                
                init() {
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
