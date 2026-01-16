<x-app-layout>
    @php
        $appsList = $applications ?? collect();
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="applicationList(@js($appsList->items()))" x-init="init()">
            <!-- Header -->
            <x-ui.page-header
                title="Service Applications"
                subtitle="Manage customer service applications and verification"
                icon="fas fa-file-alt">
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('customer.list') }}">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Customers
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Stats Row -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:shadow-md transition-shadow"
                    @click="filterByStatus('')"
                    :class="{ 'ring-2 ring-blue-500': statusFilter === '' }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Total</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="stats.total"></p>
                        </div>
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fas fa-layer-group text-gray-600 dark:text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:shadow-md transition-shadow"
                    @click="filterByStatus('PENDING')"
                    :class="{ 'ring-2 ring-yellow-500': statusFilter === 'PENDING' }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Pending</p>
                            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400" x-text="stats.pending"></p>
                        </div>
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:shadow-md transition-shadow"
                    @click="filterByStatus('VERIFIED')"
                    :class="{ 'ring-2 ring-blue-500': statusFilter === 'VERIFIED' }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Verified</p>
                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400" x-text="stats.verified"></p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:shadow-md transition-shadow"
                    @click="filterByStatus('PAID')"
                    :class="{ 'ring-2 ring-green-500': statusFilter === 'PAID' }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Paid</p>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400" x-text="stats.paid"></p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-green-600 dark:text-green-400"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:shadow-md transition-shadow"
                    @click="filterByStatus('SCHEDULED')"
                    :class="{ 'ring-2 ring-purple-500': statusFilter === 'SCHEDULED' }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Scheduled</p>
                            <p class="text-xl font-bold text-purple-600 dark:text-purple-400" x-text="stats.scheduled"></p>
                        </div>
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-check text-purple-600 dark:text-purple-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters Toolbar -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text"
                                x-model="searchQuery"
                                placeholder="Search by application #, customer name, address..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="sm:w-48">
                        <select x-model="statusFilter"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="PENDING">Pending</option>
                            <option value="VERIFIED">Verified</option>
                            <option value="PAID">Paid</option>
                            <option value="SCHEDULED">Scheduled</option>
                            <option value="CONNECTED">Connected</option>
                            <option value="REJECTED">Rejected</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Applications Table -->
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Application #
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Address
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Submitted
                                </th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(app, index) in paginatedData" :key="app.application_id || index">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150 cursor-pointer"
                                    @click="viewApplication(app.application_id)">
                                    <td class="px-4 py-3 text-sm font-mono text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                        <span x-text="app.application_number || 'APP-' + app.application_id"></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="formatCustomerName(app)"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="app.customer?.resolution_no || '-'"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900 dark:text-gray-100" x-text="formatAddress(app)"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="app.address?.barangay?.b_desc || '-'"></div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap" x-text="formatDate(app.submitted_at || app.created_at)"></td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="getStatusClass(app.status?.stat_desc)"
                                            x-text="app.status?.stat_desc || 'PENDING'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap" @click.stop>
                                        <div class="flex items-center justify-center gap-2">
                                            <a :href="'/connection/service-application/' + app.application_id"
                                                class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                                title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button @click="openPrintModal(app)"
                                                class="p-2 text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/30 rounded-lg transition-colors"
                                                title="Print Forms">
                                                <i class="fas fa-print"></i>
                                            </button>
                                            <template x-if="app.status?.stat_desc === 'PENDING'">
                                                <button @click="quickVerify(app)"
                                                    class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg transition-colors"
                                                    title="Verify">
                                                    <i class="fas fa-clipboard-check"></i>
                                                </button>
                                            </template>
                                            <template x-if="app.status?.stat_desc === 'PAID'">
                                                <button @click="quickSchedule(app)"
                                                    class="p-2 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/30 rounded-lg transition-colors"
                                                    title="Schedule">
                                                    <i class="fas fa-calendar-check"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="paginatedData.length === 0">
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                        <p x-show="searchQuery || statusFilter">No applications found matching your criteria</p>
                                        <p x-show="!searchQuery && !statusFilter">No service applications yet</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4 flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
                    <select x-model.number="pageSize" @change="currentPage = 1"
                        class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="prevPage()" :disabled="currentPage === 1"
                        class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-left mr-1"></i>Previous
                    </button>
                    <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
                        Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                    </div>
                    <button @click="nextPage()" :disabled="currentPage === totalPages"
                        class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">
                        Next<i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-white" x-text="startRecord"></span> to
                    <span class="font-semibold text-gray-900 dark:text-white" x-text="endRecord"></span> of
                    <span class="font-semibold text-gray-900 dark:text-white" x-text="totalRecords"></span> results
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modal Components -->
    <x-ui.connection.verify-modal />
    <x-ui.connection.schedule-modal />
    <x-ui.connection.print-modal />

    <script>
    function applicationList(initialData) {
        return {
            data: initialData || [],
            searchQuery: '',
            statusFilter: '',
            pageSize: 10,
            currentPage: 1,
            stats: {
                total: 0,
                pending: 0,
                verified: 0,
                paid: 0,
                scheduled: 0
            },

            init() {
                this.calculateStats();
                this.$watch('searchQuery', () => { this.currentPage = 1; });
                this.$watch('statusFilter', () => { this.currentPage = 1; });
            },

            calculateStats() {
                this.stats.total = this.data.length;
                this.stats.pending = this.data.filter(a => a.status?.stat_desc === 'PENDING').length;
                this.stats.verified = this.data.filter(a => a.status?.stat_desc === 'VERIFIED').length;
                this.stats.paid = this.data.filter(a => a.status?.stat_desc === 'PAID').length;
                this.stats.scheduled = this.data.filter(a => a.status?.stat_desc === 'SCHEDULED').length;
            },

            filterByStatus(status) {
                this.statusFilter = status;
                this.currentPage = 1;
            },

            get filteredData() {
                let filtered = this.data;

                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(app =>
                        (app.application_number || '').toLowerCase().includes(query) ||
                        this.formatCustomerName(app).toLowerCase().includes(query) ||
                        this.formatAddress(app).toLowerCase().includes(query)
                    );
                }

                if (this.statusFilter) {
                    filtered = filtered.filter(app => app.status?.stat_desc === this.statusFilter);
                }

                return filtered;
            },

            get totalRecords() {
                return this.filteredData.length;
            },

            get totalPages() {
                return Math.ceil(this.totalRecords / this.pageSize) || 1;
            },

            get startRecord() {
                if (this.totalRecords === 0) return 0;
                return ((this.currentPage - 1) * this.pageSize) + 1;
            },

            get endRecord() {
                const end = this.currentPage * this.pageSize;
                return Math.min(end, this.totalRecords);
            },

            get paginatedData() {
                const start = (this.currentPage - 1) * this.pageSize;
                const end = start + this.pageSize;
                return this.filteredData.slice(start, end);
            },

            prevPage() {
                if (this.currentPage > 1) this.currentPage--;
            },

            nextPage() {
                if (this.currentPage < this.totalPages) this.currentPage++;
            },

            formatCustomerName(app) {
                if (!app.customer) return '-';
                const c = app.customer;
                return [c.cust_first_name, c.cust_middle_name ? c.cust_middle_name[0] + '.' : '', c.cust_last_name].filter(Boolean).join(' ');
            },

            formatAddress(app) {
                if (!app.address) return '-';
                return app.address.purok?.p_desc || '-';
            },

            formatDate(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('en-PH', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            },

            getStatusClass(status) {
                const classes = {
                    'PENDING': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                    'VERIFIED': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                    'PAID': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                    'SCHEDULED': 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                    'CONNECTED': 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400',
                    'REJECTED': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                    'CANCELLED': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400'
                };
                return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
            },

            viewApplication(id) {
                window.location.href = '/connection/service-application/' + id;
            },

            quickVerify(app) {
                openVerifyModal(
                    app.application_id,
                    app.application_number || 'APP-' + app.application_id,
                    this.formatCustomerName(app),
                    this.formatAddress(app) + ', ' + (app.address?.barangay?.b_desc || '')
                );
            },

            quickSchedule(app) {
                openScheduleModal(
                    app.application_id,
                    app.application_number || 'APP-' + app.application_id,
                    this.formatCustomerName(app),
                    this.formatAddress(app) + ', ' + (app.address?.barangay?.b_desc || '')
                );
            },

            openPrintModal(app) {
                openPrintModal(app);
            }
        };
    }
    </script>

    @vite(['resources/js/utils/print-form.js'])
</x-app-layout>
