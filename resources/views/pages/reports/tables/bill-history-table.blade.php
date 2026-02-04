@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="billHistoryTable()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bill History</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View and print consumer bill history</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.water-bill-history') }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200" title="View Report Template">
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4" style="border-color: #3D90D7;">
            <div class="flex items-center">
                <div class="p-3 rounded-full" style="background-color: rgba(61, 144, 215, 0.1);">
                    <svg class="w-6 h-6" style="color: #3D90D7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Bills</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="filteredData.length.toLocaleString()"></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Paid Bills</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="paidBills.toLocaleString()"></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-amber-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-amber-100 dark:bg-amber-900/30">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unpaid Bills</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="unpaidBills.toLocaleString()"></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="'₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
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
                <input type="text" x-model="search" placeholder="Search by bill number, consumer, account..."
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:outline-none"
                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;"
                    onfocus="this.style.borderColor='#3D90D7'; this.style.boxShadow='0 0 0 2px rgba(61,144,215,0.2)';"
                    onblur="this.style.borderColor='#4b5563'; this.style.boxShadow='none';">
            </div>

            <!-- Status Filter -->
            <div class="w-40">
                <label class="block text-sm font-medium mb-1" style="color: #ffffff;">Status</label>
                <select x-model="statusFilter"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:outline-none"
                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                    <option value="">All Status</option>
                    <option value="Paid">Paid</option>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Partial">Partial</option>
                </select>
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

            <!-- Sorting Controls (moved from bottom) -->
            <div class="flex items-end gap-2">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: #ffffff;">Sort by</label>
                    <select x-model="sortField" class="px-3 py-2 border rounded-lg text-sm"
                        style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                        <option value="period">Period</option>
                        <option value="billNo">Bill No.</option>
                        <option value="consumer">Consumer</option>
                        <option value="amount">Amount</option>
                        <option value="status">Status</option>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bill No.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Account No.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(row, index) in paginatedData" :key="row.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="(currentPage - 1) * perPage + index + 1"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="row.billNo"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="row.accountNo"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="row.consumer"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="row.period"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 text-right font-medium" x-text="'₱' + row.amount.toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
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
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <a href="{{ route('reports.water-bill-history') }}" target="_blank" 
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-white transition-colors"
                                    style="background-color: #3D90D7;"
                                    onmouseover="this.style.backgroundColor='#2a7bc4'"
                                    onmouseout="this.style.backgroundColor='#3D90D7'"
                                    title="View Report Template">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Print
                                </a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredData.length === 0">
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="mt-2 text-sm">No bills found</p>
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
function billHistoryTable() {
    return {
        search: '',
        statusFilter: '',
        monthFilter: '',
        perPage: 10,
        currentPage: 1,
        sortField: 'period',
        sortDirection: 'desc',
        
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
@endsection
