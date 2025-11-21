<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="connectionData()" x-init="init()">

            <x-ui.page-header
                title="Service Connections"
                subtitle="Manage meter installation and service activation"
                icon="fas fa-plug">
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('customer.list') }}">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Customers
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Breadcrumb Process Guide -->
            <div class="mb-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-2 text-sm">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg">
                            <i class="fas fa-file-alt"></i>
                            <span class="font-medium">1. Application</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg">
                            <i class="fas fa-credit-card"></i>
                            <span class="font-medium">2. Payment</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg">
                            <i class="fas fa-check-circle"></i>
                            <span class="font-medium">3. Approval</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 rounded-lg">
                            <i class="fas fa-plug"></i>
                            <span class="font-medium">4. Connection</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text" 
                               x-model="searchQuery" 
                               placeholder="Search by account no, customer name..." 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="sm:w-48">
                        <select x-model="statusFilter" 
                                @change="currentPage = 1" 
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="SCHEDULED">Scheduled</option>
                            <option value="COMPLETED">Completed</option>
                            <option value="PENDING">Pending</option>
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
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Account No</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter Reader & Area</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Reading Schedule</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Scheduled Date</th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(conn, index) in paginatedData" :key="conn.connection_id || index">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                                    <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100 whitespace-nowrap" x-text="conn.account_no"></td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="conn.customer_name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="conn.customer_code"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            <i class="fas fa-user-tie mr-1 text-blue-600"></i><span x-text="conn.meterReader || 'N/A'"></span>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i><span x-text="conn.area || 'N/A'"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap" x-text="formatDate(conn.readingSchedule)"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap" x-text="formatDate(conn.scheduled_date)"></td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                              :class="getStatusColor(conn.connection_status)"
                                              x-text="conn.connection_status"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <template x-if="conn.connection_status === 'SCHEDULED'">
                                            <button @click="openAssignMeterModal(conn)" 
                                                    class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium transition-colors">
                                                <i class="fas fa-tools mr-2"></i>Complete Connection
                                            </button>
                                        </template>
                                        <template x-if="conn.connection_status === 'COMPLETED'">
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200"
                                                  x-text="'Completed ' + formatDate(conn.completed_date)"></span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="paginatedData.length === 0">
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                        <p>No connections found</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination Controls -->
            <div class="flex justify-between items-center mt-4 flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
                    <select x-model.number="pageSize" @change="currentPage = 1" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <button 
                        @click="prevPage()"
                        :disabled="currentPage === 1"
                        class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                        <i class="fas fa-chevron-left mr-1"></i>Previous
                    </button>
                    
                    <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
                        Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                    </div>
                    
                    <button 
                        @click="nextPage()"
                        :disabled="currentPage === totalPages"
                        class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
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

    <x-ui.connection.assign-meter />
    <x-ui.connection.invoice-modal />

    <script>
    function connectionData() {
        return {
            searchQuery: '',
            statusFilter: '',
            pageSize: 10,
            currentPage: 1,
            data: [],
            selectedConnection: null,
            
            init() {
                // Load connection data
                this.data = [
                    { connection_id: 'CONN-1001', customer_code: 'CUST-2024-009', customer_name: 'David Wilson', account_no: 'ACC-2024-5001', area: 'Zone C', meterReader: 'Mike Johnson', readingSchedule: '2024-02-15', connection_status: 'SCHEDULED', scheduled_date: '2024-01-20', created_at: '2024-01-10' },
                    { connection_id: 'CONN-1002', customer_code: 'CUST-2024-010', customer_name: 'Emma Brown', account_no: 'ACC-2024-5002', area: 'Zone A', meterReader: 'John Smith', readingSchedule: '2024-02-05', connection_status: 'SCHEDULED', scheduled_date: '2024-01-21', created_at: '2024-01-09' },
                    { connection_id: 'CONN-1003', customer_code: 'CUST-2024-011', customer_name: 'Robert Taylor', account_no: 'ACC-2024-5003', area: 'Zone B', meterReader: 'Jane Doe', readingSchedule: '2024-02-10', connection_status: 'COMPLETED', scheduled_date: '2024-01-08', completed_date: '2024-01-08', created_at: '2024-01-05' },
                    { connection_id: 'CONN-1004', customer_code: 'CUST-2024-012', customer_name: 'Sarah Johnson', account_no: 'ACC-2024-5004', area: 'Zone A', meterReader: 'John Smith', readingSchedule: '2024-02-05', connection_status: 'COMPLETED', scheduled_date: '2024-01-05', completed_date: '2024-01-05', created_at: '2024-01-02' }
                ];
                
                this.$watch('searchQuery', () => { this.currentPage = 1; });
                this.$watch('statusFilter', () => { this.currentPage = 1; });
                this.$watch('pageSize', () => { this.currentPage = 1; });
            },
            
            get filteredData() {
                let filtered = this.data;
                
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(conn => 
                        conn.account_no.toLowerCase().includes(query) ||
                        conn.customer_name.toLowerCase().includes(query) ||
                        conn.customer_code.toLowerCase().includes(query)
                    );
                }
                
                if (this.statusFilter) {
                    filtered = filtered.filter(conn => conn.connection_status === this.statusFilter);
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
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            },
            
            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                }
            },
            
            formatDate(dateString) {
                if (!dateString) return '';
                return new Date(dateString).toLocaleDateString();
            },
            
            getStatusColor(status) {
                const colors = {
                    'COMPLETED': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
                    'SCHEDULED': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
                    'PENDING': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200'
                };
                return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
            },
            
            openMeterModal(connection) {
                if (window.openAssignMeterModal) {
                    window.openAssignMeterModal(connection);
                }
            },
            
            completeConnection() {
                if (this.selectedConnection) {
                    this.selectedConnection.connection_status = 'COMPLETED';
                    this.selectedConnection.completed_date = new Date().toISOString();
                    this.selectedConnection = null;
                }
            }
        }
    }
    </script>

</x-app-layout>
