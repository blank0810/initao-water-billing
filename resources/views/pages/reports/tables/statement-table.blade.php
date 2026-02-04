@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="statementTable()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Statement of Account</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View and print consumer statements of account</p>
        </div>
        <a href="{{ route('reports') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-white bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Reports
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4" style="border-color: #3D90D7;">
            <div class="flex items-center">
                <div class="p-3 rounded-full" style="background-color: rgba(61, 144, 215, 0.1);">
                    <svg class="w-6 h-6" style="color: #3D90D7;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Consumers</p>
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
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Zero Balance</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="zeroBalance.toLocaleString()"></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">With Balance</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="withBalance.toLocaleString()"></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-amber-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-amber-100 dark:bg-amber-900/30">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Outstanding</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="'₱' + totalBalance.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
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
                <input type="text" x-model="search" placeholder="Search by account number, consumer name..."
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:outline-none"
                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;"
                    onfocus="this.style.borderColor='#3D90D7'; this.style.boxShadow='0 0 0 2px rgba(61,144,215,0.2)';"
                    onblur="this.style.borderColor='#4b5563'; this.style.boxShadow='none';">
            </div>

            <!-- Balance Filter -->
            <div class="w-40">
                <label class="block text-sm font-medium mb-1" style="color: #ffffff;">Balance</label>
                <select x-model="balanceFilter"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:outline-none"
                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                    <option value="">All</option>
                    <option value="zero">Zero Balance</option>
                    <option value="with">With Balance</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div class="w-40">
                <label class="block text-sm font-medium mb-1" style="color: #ffffff;">Account Type</label>
                <select x-model="typeFilter"
                    class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:outline-none"
                    style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                    <option value="">All Types</option>
                    <option value="Residential">Residential</option>
                    <option value="Commercial">Commercial</option>
                    <option value="Government">Government</option>
                </select>
            </div>

            <!-- Sorting Controls (moved from bottom) -->
            <div class="flex items-end gap-2">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: #ffffff;">Sort by</label>
                    <select x-model="sortField" class="px-3 py-2 border rounded-lg text-sm"
                        style="background-color: #374151; border-color: #4b5563; color: #ffffff;">
                        <option value="accountNo">Account No.</option>
                        <option value="consumer">Consumer Name</option>
                        <option value="address">Address</option>
                        <option value="type">Type</option>
                        <option value="balance">Balance</option>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Account No.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumer Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Balance</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Last Payment</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(row, index) in paginatedData" :key="row.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="(currentPage - 1) * perPage + index + 1"></td>
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
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <p class="mt-2 text-sm">No statements found</p>
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
function statementTable() {
    return {
        search: '',
        balanceFilter: '',
        typeFilter: '',
        perPage: 10,
        currentPage: 1,
        sortField: 'accountNo',
        sortDirection: 'asc',
        
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
@endsection
