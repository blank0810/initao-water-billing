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

            

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="currentTab = 'connections'"
                                :class="currentTab === 'connections' ? 'border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400' : 'border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                            <i class="fas fa-link mr-2"></i>Connections
                        </button>
                        <button @click="currentTab = 'area'"
                                :class="currentTab === 'area' ? 'border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400' : 'border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                            <i class="fas fa-book mr-2"></i>Area Assignment
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Connections Tab -->
            <div x-show="currentTab === 'connections'">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                    <div class="flex flex-wrap gap-3 items-center">
                        <div class="flex-1 min-w-[250px]">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" x-model="searchQuery" placeholder="Search by account no, customer name..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="w-48">
                            <select x-model="statusFilter" @change="currentPage = 1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">All Status</option>
                                <option value="SCHEDULED">Scheduled</option>
                                <option value="COMPLETED">Completed</option>
                                <option value="PENDING">Pending</option>
                            </select>
                        </div>
                        <button @click="searchQuery = ''; statusFilter = ''; currentPage = 1" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition">
                            <i class="fas fa-times mr-1"></i>Clear
                        </button>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                                <i class="fas fa-download"></i>Export<i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-20">
                                <button @click="window.print()" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-t-lg flex items-center gap-2">
                                    <i class="fas fa-file-excel text-green-600"></i>Export to Excel
                                </button>
                                <button @click="window.print()" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-b-lg flex items-center gap-2">
                                    <i class="fas fa-file-pdf text-red-600"></i>Export to PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Area Assignment Tab -->
            <div x-show="currentTab === 'area'">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                    <div class="flex flex-wrap gap-3 items-center">
                        <div class="flex-1 min-w-[250px]">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" x-model="areaSearchQuery" placeholder="Search by customer, connection id, address..." class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <button @click="areaSearchQuery = ''; areaCurrentPage = 1" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition">
                            <i class="fas fa-times mr-1"></i>Clear
                        </button>
                        <button type="button" onclick="window.showAreaCreateModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                            <i class="fas fa-plus mr-2"></i>Create
                        </button>
                        <button type="button" onclick="window.showAreaAssignModal()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition">
                            <i class="fas fa-map-marker-alt mr-2"></i>Assign
                        </button>
                        <button type="button" onclick="window.showAreaReassignModal()" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm transition">
                            <i class="fas fa-exchange-alt mr-2"></i>Reassign
                        </button>
                    </div>
                </div>
            </div>

            <!-- Connections Table -->
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                <div class="overflow-x-auto" x-show="currentTab === 'connections'">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Account No</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter Reader & Area</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Reading Schedule</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Scheduled Date</th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody id="connectionsTable" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(conn, index) in paginatedData" :key="conn.connection_id || index">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                                    <td class="px-4 py-4 text-sm font-mono text-gray-900 dark:text-gray-100 whitespace-nowrap" x-text="conn.account_no"></td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="conn.customer_name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="conn.customer_code"></div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="conn.meterReader || 'N/A'"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="conn.area || 'N/A'"></div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap" x-text="formatDate(conn.readingSchedule)"></td>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap" x-text="formatDate(conn.scheduled_date)"></td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                              :class="getStatusColor(conn.connection_status)"
                                              x-text="conn.connection_status"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <template x-if="conn.connection_status === 'SCHEDULED'">
                                            <div class="inline-flex items-center gap-2">
                                                <button @click="openAssignMeterModal(conn)" 
                                                        class="w-9 h-9 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors flex items-center justify-center"
                                                        title="Assign Meter">
                                                    <i class="fas fa-tools"></i>
                                                </button>
                                                <button @click="openReturnToApprovalModal(conn)" 
                                                        class="w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center justify-center"
                                                        title="Return to Approval">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </div>
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
                <div class="overflow-x-auto" x-show="currentTab === 'area'">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Connection ID</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Address</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Area</th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Assigned Date</th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="areaTable" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(rec, idx) in areaPaginatedData" :key="rec.connection_id || idx">
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="rec.customer_name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="rec.customer_code"></div>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="rec.connection_id"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="rec.address"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="rec.area"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="formatDate(rec.assigned_date)"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusColor(rec.status)" x-text="rec.status"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex items-center gap-2">
                                            <button @click="openEditArea(rec)" class="w-9 h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button @click="openDeleteArea(rec)" class="w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="areaPaginatedData.length === 0">
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                        <p>No area assignments found</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination Controls -->
            <div x-show="currentTab === 'connections'" class="flex justify-between items-center mt-4 flex-wrap gap-4">
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
            <!-- Area Assignment Pagination Controls -->
            <div x-show="currentTab === 'area'" class="flex justify-between items-center mt-4 flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
                    <select x-model.number="areaPageSize" @change="areaCurrentPage = 1" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <button 
                        @click="areaPrevPage()"
                        :disabled="areaCurrentPage === 1"
                        class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                        <i class="fas fa-chevron-left mr-1"></i>Previous
                    </button>
                    
                    <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
                        Page <span x-text="areaCurrentPage"></span> of <span x-text="areaTotalPages"></span>
                    </div>
                    
                    <button 
                        @click="areaNextPage()"
                        :disabled="areaCurrentPage === areaTotalPages"
                        class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                        Next<i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>
                
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-white" x-text="areaStartRecord"></span> to 
                    <span class="font-semibold text-gray-900 dark:text-white" x-text="areaEndRecord"></span> of 
                    <span class="font-semibold text-gray-900 dark:text-white" x-text="areaTotalRecords"></span> results
                </div>
            </div>
        </div>
    </div>

    <x-ui.connection.assign-meter />
    <x-ui.connection.invoice-modal />
    <x-ui.connection.return-to-approval-modal />

    <script>
    function connectionData() {
        return {
            currentTab: 'connections',
            approvalUrl: '{{ route('approve.customer') }}',
            searchQuery: '',
            statusFilter: '',
            pageSize: 10,
            currentPage: 1,
            data: [],
            selectedConnection: null,
            areaAssignments: [],
            areaSearchQuery: '',
            areaPageSize: 10,
            areaCurrentPage: 1,
            
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
                
                // Seed area assignments from existing connections (address placeholder)
                this.areaAssignments = this.data.map(d => ({
                    customer_name: d.customer_name,
                    customer_code: d.customer_code,
                    connection_id: d.connection_id,
                    address: 'N/A',
                    area: d.area,
                    assigned_date: d.completed_date || d.scheduled_date,
                    status: d.connection_status
                }));
                
                this.$watch('areaSearchQuery', () => { this.areaCurrentPage = 1; });
                this.$watch('areaPageSize', () => { this.areaCurrentPage = 1; });
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
            
            // Area Assignment computed
            get areaFilteredData() {
                if (!this.areaSearchQuery) return this.areaAssignments;
                const q = this.areaSearchQuery.toLowerCase();
                return this.areaAssignments.filter(rec =>
                    (rec.customer_name || '').toLowerCase().includes(q) ||
                    (rec.connection_id || '').toLowerCase().includes(q) ||
                    (rec.address || '').toLowerCase().includes(q) ||
                    (rec.area || '').toLowerCase().includes(q) ||
                    (rec.status || '').toLowerCase().includes(q)
                );
            },
            get areaTotalRecords() {
                return this.areaFilteredData.length;
            },
            get areaTotalPages() {
                return Math.ceil(this.areaTotalRecords / this.areaPageSize) || 1;
            },
            get areaStartRecord() {
                if (this.areaTotalRecords === 0) return 0;
                return ((this.areaCurrentPage - 1) * this.areaPageSize) + 1;
            },
            get areaEndRecord() {
                const end = this.areaCurrentPage * this.areaPageSize;
                return Math.min(end, this.areaTotalRecords);
            },
            get areaPaginatedData() {
                const start = (this.areaCurrentPage - 1) * this.areaPageSize;
                const end = start + this.areaPageSize;
                return this.areaFilteredData.slice(start, end);
            },
            areaPrevPage() {
                if (this.areaCurrentPage > 1) {
                    this.areaCurrentPage--;
                }
            },
            areaNextPage() {
                if (this.areaCurrentPage < this.areaTotalPages) {
                    this.areaCurrentPage++;
                }
            },
            openEditArea(item) {
                const m = document.getElementById('areaEditModal');
                if (!m) return;
                document.getElementById('ae_customer_name').value = item.customer_name || '';
                document.getElementById('ae_customer_code').value = item.customer_code || '';
                document.getElementById('ae_connection_id').value = item.connection_id || '';
                document.getElementById('ae_address').value = item.address || '';
                document.getElementById('ae_area').value = item.area || '';
                document.getElementById('ae_assigned_date').value = item.assigned_date ? new Date(item.assigned_date).toISOString().split('T')[0] : '';
                document.getElementById('ae_status').value = item.status || 'PENDING';
                m.classList.remove('hidden');
            },
            openDeleteArea(item) {
                const m = document.getElementById('areaDeleteModal');
                if (!m) return;
                document.getElementById('ad_connection_id').value = item.connection_id || '';
                m.classList.remove('hidden');
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
            },
            // Return to Approval handled by modal component
        }
    }
    </script>

    <!-- Area Assignment Modals -->
    <x-ui.customer.modals.area-create-modal />
    <x-ui.customer.modals.area-assign-modal />
    <x-ui.customer.modals.area-reassign-modal />
    <x-ui.customer.modals.area-edit-modal />
    <x-ui.customer.modals.area-delete-modal />

</x-app-layout>
