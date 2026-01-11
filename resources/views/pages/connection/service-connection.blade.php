<x-app-layout>
    @php
        $connsList = $connections ?? collect();
        $scheduled = $scheduledApplications ?? collect();
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="connectionList(@js($connsList->items()), @js($scheduled))" x-init="init()">
            <!-- Header -->
            <x-ui.page-header
                title="Service Connections"
                subtitle="Manage active water service connections"
                icon="fas fa-plug">
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('connection.service-application.index') }}">
                        <i class="fas fa-file-alt mr-2"></i>Service Applications
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Scheduled Applications Banner -->
            <div class="mb-6" x-show="scheduledApplications.length > 0">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar-check text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Scheduled for Connection</h3>
                                <p class="text-blue-100">
                                    <span x-text="scheduledApplications.length"></span> application(s) ready to be connected
                                </p>
                            </div>
                        </div>
                        <button @click="showScheduledModal = true"
                            class="px-4 py-2 bg-white text-blue-600 rounded-lg font-medium hover:bg-blue-50 transition-colors">
                            <i class="fas fa-eye mr-2"></i>View Scheduled
                        </button>
                    </div>

                    <!-- Mini List of Scheduled -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3" x-show="scheduledApplications.length <= 3">
                        <template x-for="app in scheduledApplications.slice(0, 3)" :key="app.application_id">
                            <div class="bg-white/10 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-sm" x-text="formatCustomerName(app)"></p>
                                        <p class="text-xs text-blue-200" x-text="formatDate(app.scheduled_connection_date)"></p>
                                    </div>
                                    <a :href="'/connection/service-application/' + app.application_id"
                                        class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded-lg font-medium transition-colors inline-block">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:shadow-md transition-shadow"
                    @click="filterByStatus('')"
                    :class="{ 'ring-2 ring-blue-500': statusFilter === '' }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Total Connections</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="stats.total"></p>
                        </div>
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fas fa-plug text-gray-600 dark:text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:shadow-md transition-shadow"
                    @click="filterByStatus('ACTIVE')"
                    :class="{ 'ring-2 ring-green-500': statusFilter === 'ACTIVE' }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Active</p>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400" x-text="stats.active"></p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:shadow-md transition-shadow"
                    @click="filterByStatus('SUSPENDED')"
                    :class="{ 'ring-2 ring-yellow-500': statusFilter === 'SUSPENDED' }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Suspended</p>
                            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400" x-text="stats.suspended"></p>
                        </div>
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-pause-circle text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 cursor-pointer hover:shadow-md transition-shadow"
                    @click="filterByStatus('DISCONNECTED')"
                    :class="{ 'ring-2 ring-red-500': statusFilter === 'DISCONNECTED' }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Disconnected</p>
                            <p class="text-xl font-bold text-red-600 dark:text-red-400" x-text="stats.disconnected"></p>
                        </div>
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-unlink text-red-600 dark:text-red-400"></i>
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
                                placeholder="Search by account #, customer name, address..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="sm:w-48">
                        <select x-model="statusFilter"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="ACTIVE">Active</option>
                            <option value="SUSPENDED">Suspended</option>
                            <option value="DISCONNECTED">Disconnected</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Connections Table -->
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Account #
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Address
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Since
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
                            <template x-for="(conn, index) in paginatedData" :key="conn.connection_id || index">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150 cursor-pointer"
                                    @click="viewConnection(conn.connection_id)">
                                    <td class="px-4 py-3 text-sm font-mono text-teal-600 dark:text-teal-400 whitespace-nowrap">
                                        <span x-text="conn.account_no || 'CONN-' + conn.connection_id"></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="formatCustomerName(conn)"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="conn.customer?.resolution_number || '-'"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-900 dark:text-gray-100" x-text="formatAddress(conn)"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="conn.address?.barangay?.b_name || '-'"></div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap" x-text="conn.account_type?.description || 'Residential'"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap" x-text="formatDate(conn.started_at)"></td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="getStatusClass(conn.status?.description)"
                                            x-text="conn.status?.description || 'ACTIVE'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap" @click.stop>
                                        <div class="flex items-center justify-center gap-2">
                                            <a :href="'/customer/service-connection/' + conn.connection_id"
                                                class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                                title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <template x-if="conn.status?.description === 'ACTIVE'">
                                                <button @click="quickSuspend(conn)"
                                                    class="p-2 text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 rounded-lg transition-colors"
                                                    title="Suspend">
                                                    <i class="fas fa-pause-circle"></i>
                                                </button>
                                            </template>
                                            <template x-if="conn.status?.description === 'SUSPENDED'">
                                                <button @click="quickReconnect(conn)"
                                                    class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg transition-colors"
                                                    title="Reconnect">
                                                    <i class="fas fa-plug"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="paginatedData.length === 0">
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                        <p x-show="searchQuery || statusFilter">No connections found matching your criteria</p>
                                        <p x-show="!searchQuery && !statusFilter">No service connections yet</p>
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

            <!-- Scheduled Applications Modal -->
            <div x-show="showScheduledModal" x-cloak
                class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden"
                    @click.outside="showScheduledModal = false">
                    <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <i class="fas fa-calendar-check text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Scheduled Applications</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ready for connection</p>
                            </div>
                        </div>
                        <button @click="showScheduledModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="p-6 overflow-y-auto max-h-[60vh]">
                        <div class="space-y-3">
                            <template x-for="app in scheduledApplications" :key="app.application_id">
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="formatCustomerName(app)"></p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="formatAddress(app) + ', ' + (app.address?.barangay?.b_desc || '')"></p>
                                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                <i class="fas fa-calendar mr-1"></i>
                                                Scheduled: <span x-text="formatDate(app.scheduled_connection_date)"></span>
                                            </p>
                                        </div>
                                        <a :href="'/connection/service-application/' + app.application_id"
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors inline-block">
                                            <i class="fas fa-eye mr-2"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </template>
                            <div x-show="scheduledApplications.length === 0" class="text-center py-8">
                                <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400">No scheduled applications</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    function connectionList(initialData, scheduledData) {
        return {
            data: initialData || [],
            scheduledApplications: scheduledData || [],
            searchQuery: '',
            statusFilter: '',
            pageSize: 10,
            currentPage: 1,
            showScheduledModal: false,
            stats: {
                total: 0,
                active: 0,
                suspended: 0,
                disconnected: 0
            },

            init() {
                this.calculateStats();
                this.$watch('searchQuery', () => { this.currentPage = 1; });
                this.$watch('statusFilter', () => { this.currentPage = 1; });
            },

            calculateStats() {
                this.stats.total = this.data.length;
                this.stats.active = this.data.filter(c => c.status?.description === 'ACTIVE').length;
                this.stats.suspended = this.data.filter(c => c.status?.description === 'SUSPENDED').length;
                this.stats.disconnected = this.data.filter(c => c.status?.description === 'DISCONNECTED').length;
            },

            filterByStatus(status) {
                this.statusFilter = status;
                this.currentPage = 1;
            },

            get filteredData() {
                let filtered = this.data;

                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(conn =>
                        (conn.account_no || '').toLowerCase().includes(query) ||
                        this.formatCustomerName(conn).toLowerCase().includes(query) ||
                        this.formatAddress(conn).toLowerCase().includes(query)
                    );
                }

                if (this.statusFilter) {
                    filtered = filtered.filter(conn => conn.status?.description === this.statusFilter);
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

            formatCustomerName(item) {
                if (!item.customer) return '-';
                const c = item.customer;
                return [c.fname, c.mname ? c.mname[0] + '.' : '', c.lname].filter(Boolean).join(' ');
            },

            formatAddress(item) {
                if (!item.address) return '-';
                return item.address.purok || item.address.street || '-';
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
                    'ACTIVE': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                    'SUSPENDED': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                    'DISCONNECTED': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                };
                return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
            },

            viewConnection(id) {
                window.location.href = '/customer/service-connection/' + id;
            },

            async quickSuspend(conn) {
                const reason = prompt('Enter suspension reason:');
                if (!reason) return;

                try {
                    const response = await fetch(`/customer/service-connection/${conn.connection_id}/suspend`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ reason })
                    });

                    const data = await response.json();

                    if (data.success) {
                        if (window.showToast) {
                            window.showToast('Connection suspended', 'warning');
                        }
                        location.reload();
                    } else {
                        throw new Error(data.message || 'Suspension failed');
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            },

            async quickReconnect(conn) {
                if (!confirm('Reconnect this connection?')) return;

                try {
                    const response = await fetch(`/customer/service-connection/${conn.connection_id}/reconnect`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        if (window.showToast) {
                            window.showToast('Connection reconnected!', 'success');
                        }
                        location.reload();
                    } else {
                        throw new Error(data.message || 'Reconnection failed');
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            }
        };
    }
    </script>
</x-app-layout>
