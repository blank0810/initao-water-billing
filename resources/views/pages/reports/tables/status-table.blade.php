@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="statusTable()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Monthly Status Report</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View and analyze monthly connection status data</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.summary-status') }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200" title="View Report Template">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                View Template
            </a>
            <a href="{{ route('reports') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-white bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Reports
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Connections</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="totalConnections.toLocaleString()"></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Disconnections</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="totalDisconnections.toLocaleString()"></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4" style="border-color: #3D90D7;">
            <div class="flex items-center">
                <div class="p-3 rounded-full" style="background-color: rgba(61, 144, 215, 0.1);">
                    <svg class="w-6 h-6" style="color: #3D90D7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Reconnections</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="totalReconnections.toLocaleString()"></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-amber-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-amber-100 dark:bg-amber-900/30">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Uncollected</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="'₱' + totalUncollected.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar - Dark Mode -->
    <div class="rounded-lg shadow mb-6 p-4" style="background-color: #1f2937; border: 1px solid #374151;">
        <div class="flex flex-wrap items-center gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium mb-1" style="color: #ffffff;">Search</label>
                <input type="text" x-model="search" placeholder="Search by period..."
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:outline-none"
                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;"
                    onfocus="this.style.borderColor='#3D90D7'; this.style.boxShadow='0 0 0 2px rgba(61,144,215,0.2)';"
                    onblur="this.style.borderColor='#4b5563'; this.style.boxShadow='none';">
            </div>

            <!-- Month Filter -->
            <div class="w-40">
                <label class="block text-sm font-medium mb-1" style="color: #ffffff;">Month</label>
                <select x-model="monthFilter"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:outline-none"
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
            </div>

            <!-- Year Filter -->
            <div class="w-32">
                <label class="block text-sm font-medium mb-1" style="color: #ffffff;">Year</label>
                <select x-model="yearFilter"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:outline-none"
                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                    <option value="">All Years</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                </select>
            </div>

            <!-- Sorting Controls (moved from bottom) -->
            <div class="flex items-end gap-2">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: #ffffff;">Sort by</label>
                    <select x-model="sortField" class="px-3 py-2 border rounded-lg text-sm"
                        style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                        <option value="period">Date/Period</option>
                        <option value="connections">Connections</option>
                        <option value="disconnections">Disconnections</option>
                        <option value="reconnections">Reconnections</option>
                        <option value="consumers">Total Consumers</option>
                        <option value="billingAmount">Billing Amount</option>
                        <option value="collectionAmount">Collection Amount</option>
                        <option value="uncollected">Uncollected</option>
                    </select>
                </div>
                <button @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
                    class="px-3 py-2 border rounded-lg text-sm font-medium transition-colors"
                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;"
                    onmouseover="this.style.backgroundColor='#4b5563'"
                    onmouseout="this.style.backgroundColor='#374151'">
                    <span x-show="sortDirection === 'asc'">↑ Asc</span>
                    <span x-show="sortDirection === 'desc'">↓ Desc</span>
                </button>
            </div>

            <!-- Reset -->
            <div class="flex items-end">
                <button @click="resetFilters()" class="px-4 py-2 border rounded-lg text-sm font-medium transition-colors"
                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;"
                    onmouseover="this.style.backgroundColor='#4b5563'"
                    onmouseout="this.style.backgroundColor='#374151'">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date/Period</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Connections</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Disconnections</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reconnections</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Consumers</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Billing Amount</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Collection Amount</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Uncollected/Outstanding</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(row, index) in paginatedData" :key="row.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
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
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-white transition-colors"
                                    style="background-color: #3D90D7;"
                                    onmouseover="this.style.backgroundColor='#2a7bc4'"
                                    onmouseout="this.style.backgroundColor='#3D90D7'"
                                    title="View Report Template">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View Report
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
    </div>

    <!-- Pagination and Per Page Below Table -->
    <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
        <!-- Per Page Control (Left) -->
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Show</label>
            <select x-model="perPage"
                class="px-3 py-1.5 border rounded-lg text-sm"
                style="background-color: #ffffff; border-color: #d1d5db; color: #111827;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">entries</label>
        </div>

        <!-- Pagination Controls (Center) -->
        <div class="flex items-center gap-1">
            <button @click="currentPage = 1" :disabled="currentPage === 1"
                class="px-3 py-1.5 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                style="border-color: #d1d5db;">
                First
            </button>
            <button @click="currentPage--" :disabled="currentPage === 1"
                class="px-3 py-1.5 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                style="border-color: #d1d5db;">
                Prev
            </button>
            <span class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400">
                Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
            </span>
            <button @click="currentPage++" :disabled="currentPage >= totalPages"
                class="px-3 py-1.5 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                style="border-color: #d1d5db;">
                Next
            </button>
            <button @click="currentPage = totalPages" :disabled="currentPage >= totalPages"
                class="px-3 py-1.5 border rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                style="border-color: #d1d5db;">
                Last
            </button>
        </div>

        <!-- Results Info (Right) -->
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Showing <span x-text="Math.min((currentPage - 1) * perPage + 1, filteredData.length)"></span>
            to <span x-text="Math.min(currentPage * perPage, filteredData.length)"></span>
            of <span x-text="filteredData.length"></span> results
        </div>
    </div>
</div>

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
@endsection
