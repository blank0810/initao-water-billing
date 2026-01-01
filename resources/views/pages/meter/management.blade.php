<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <x-ui.page-header 
                title="Meter Management" 
                subtitle="Monitor water meters, readings, and consumption data"
            >
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('meter.overall-data') }}" icon="fas fa-chart-bar">
                        Overall Data
                    </x-ui.button>
                    <x-ui.button variant="primary" icon="fas fa-link" onclick="openAssignMeterModal()">
                        Assign Meter
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="showTab('meters')" id="tab-meters" class="tab-btn border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                            <i class="fas fa-tachometer-alt mr-2"></i>Consumer Meters
                        </button>
                        <button onclick="showTab('inventory')" id="tab-inventory" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                            <i class="fas fa-boxes mr-2"></i>Meter Inventory
                        </button>
                        <button onclick="showTab('readings')" id="tab-readings" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                            <i class="fas fa-chart-line mr-2"></i>Readings
                        </button>
                        <button onclick="showTab('readers')" id="tab-readers" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                            <i class="fas fa-users mr-2"></i>Meter Readers
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Meters Tab -->
            <div id="content-meters" class="tab-content">
                <!-- Summary Cards -->
                <div id="meterSummaryWrapper" class="mb-8">
                    @include('components.ui.meter.info-cards')
                </div>

                <!-- Search and Filters -->
                <div id="searchFilterSection" class="mb-6">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <x-ui.search-bar placeholder="Search by meter no, consumer name, address..." />
                        </div>
                        <div class="sm:w-48">
                            <select id="statusFilterDropdown" onchange="filterByStatus(this.value)" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="all">All Status</option>
                            </select>
                        </div>
                        <div class="sm:w-48">
                            <select id="zoneFilterDropdown" onchange="filterByZone(this.value)" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="all">All Zones</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Main Table Section -->
                <div id="tableSection" x-data="meterManagementData()" x-init="init()">
                    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter No.</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Address</th>
                                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Current Reading</th>
                                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumption</th>
                                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <template x-for="meter in paginatedData" :key="meter.id">
                                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="meter.meterNo"></td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="meter.name"></td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300" x-text="meter.address"></td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="meter.statusClass">
                                                    <i class="fas mr-1" :class="meter.statusIcon"></i><span x-text="meter.statusName"></span>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300" x-text="meter.currentReading.toFixed(2) + ' m³'"></td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300" x-text="meter.consumption.toFixed(2) + ' m³'"></td>
                                            <td class="px-4 py-3 text-center">
                                                <button @click="selectConsumer(meter.id)" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">View Details</button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="paginatedData.length === 0">
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                                <p>No meters found</p>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

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
                            <button @click="prevPage()" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                                <i class="fas fa-chevron-left mr-1"></i>Previous
                            </button>
                            <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
                                Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                            </div>
                            <button @click="nextPage()" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                                Next<i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        </div>
                        
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Showing <span class="font-semibold text-gray-900 dark:text-white" x-text="startRecord"></span> to <span class="font-semibold text-gray-900 dark:text-white" x-text="endRecord"></span> of <span class="font-semibold text-gray-900 dark:text-white" x-text="totalRecords"></span> results
                        </div>
                    </div>
                </div>

<script>
function meterManagementData() {
    return {
        searchQuery: '',
        statusFilter: 'all',
        zoneFilter: 'all',
        pageSize: 10,
        currentPage: 1,
        data: [],
        
        init() {
            this.loadData();
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('statusFilter', () => this.currentPage = 1);
            this.$watch('zoneFilter', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);
        },
        
        loadData() {
            if (!window._meterModule) {
                setTimeout(() => this.loadData(), 100);
                return;
            }
            const { consumers, meterStatuses } = window._meterModule;
            this.data = consumers.map(c => {
                const status = meterStatuses.find(s => s.id === c.statusId) || meterStatuses[0];
                const consumption = c.currentReading - c.previousReading;
                return {
                    ...c,
                    consumption,
                    statusName: status.name,
                    statusClass: `bg-${status.color}-100 text-${status.color}-800 dark:bg-${status.color}-900 dark:text-${status.color}-200`,
                    statusIcon: status.icon
                };
            });
        },
        
        get filteredData() {
            let filtered = this.data;
            if (this.searchQuery) {
                filtered = filtered.filter(m => 
                    m.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    m.meterNo.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    m.address.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    m.id.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
            if (this.statusFilter !== 'all') {
                filtered = filtered.filter(m => m.statusId === parseInt(this.statusFilter));
            }
            if (this.zoneFilter !== 'all') {
                filtered = filtered.filter(m => m.zone === this.zoneFilter);
            }
            return filtered;
        },
        
        get totalRecords() { return this.filteredData.length; },
        get totalPages() { return Math.ceil(this.totalRecords / this.pageSize) || 1; },
        get startRecord() { return this.totalRecords === 0 ? 0 : ((this.currentPage - 1) * this.pageSize) + 1; },
        get endRecord() { return Math.min(this.currentPage * this.pageSize, this.totalRecords); },
        get paginatedData() {
            const start = (this.currentPage - 1) * this.pageSize;
            return this.filteredData.slice(start, start + this.pageSize);
        },
        
        prevPage() { if (this.currentPage > 1) this.currentPage--; },
        nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
        selectConsumer(id) { if (window.selectConsumer) window.selectConsumer(id); }
    }
}
</script>
            </div>

            <!-- Meter Inventory Tab -->
            <div id="content-inventory" class="tab-content hidden">
                @include('pages.meter.inventory')
            </div>

            <!-- Meter Readings Tab -->
            <div id="content-readings" class="tab-content hidden">
                @include('pages.meter.readings')
            </div>

            <!-- Meter Readers Tab -->
            <div id="content-readers" class="tab-content hidden">
                @include('pages.meter.readers')
            </div>

            <!-- Meter Details Section -->
            <div id="meterDetailsSection" class="hidden">
                @include('pages.meter.meter-consumer-details')
            </div>

            <!-- Assign Meter Modal -->
            @include('components.ui.meter.assign-meter-modal')

            <!-- Old Meter Details Section (backup) -->
            <div id="meterDetailsSection_old" class="hidden mt-6">
                <x-ui.card>
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Meter Details</h3>
                        <x-ui.button variant="outline" icon="fas fa-arrow-left" id="backToListBtn">
                            Back to List
                        </x-ui.button>
                    </div>

                    <!-- Top Row: 3 Cards -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-5 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-user text-blue-600 dark:text-blue-400 mr-2"></i>
                                <h4 class="font-medium text-gray-900 dark:text-white">Consumer Profile</h4>
                            </div>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Name:</span>
                                    <span id="consumer_name" class="font-medium text-gray-900 dark:text-white">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Meter No:</span>
                                    <span id="consumer_meterno" class="font-medium text-gray-900 dark:text-white font-mono">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Location:</span>
                                    <span id="consumer_location" class="font-medium text-gray-900 dark:text-white">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Installed:</span>
                                    <span id="consumer_installed" class="font-medium text-gray-900 dark:text-white">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-5 border border-green-200 dark:border-green-800">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-tachometer-alt text-green-600 dark:text-green-400 mr-2"></i>
                                <h4 class="font-medium text-gray-900 dark:text-white">Meter Information</h4>
                            </div>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                    <span id="meter_info_status" class="font-medium text-gray-900 dark:text-white">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Installed:</span>
                                    <span id="meter_info_installed" class="font-medium text-gray-900 dark:text-white">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Serial:</span>
                                    <span id="meter_info_serial" class="font-medium text-gray-900 dark:text-white font-mono">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Model:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">Standard</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-5 border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 mr-2"></i>
                                <h4 class="font-medium text-gray-900 dark:text-white">Last Reading</h4>
                            </div>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Reading:</span>
                                    <span id="last_reading_value" class="font-medium text-purple-600 dark:text-purple-400 text-lg">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Date:</span>
                                    <span id="last_reading_date" class="font-medium text-gray-900 dark:text-white">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Row: 2 Cards -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <x-ui.card title="Recent Activity" class="h-64">
                            <div id="meterActivity_list" class="space-y-3 max-h-48 overflow-y-auto">
                                <!-- Activity items populated by JS -->
                            </div>
                        </x-ui.card>

                        <x-ui.card title="Consumption Summary">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-droplet text-blue-500 mr-2"></i>
                                        <span class="text-gray-600 dark:text-gray-400 text-sm">Total Consumption</span>
                                    </div>
                                    <span id="summary_total_consumption" class="font-semibold text-gray-900 dark:text-white">-</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-day text-green-500 mr-2"></i>
                                        <span class="text-gray-600 dark:text-gray-400 text-sm">Avg Daily</span>
                                    </div>
                                    <span id="summary_avg_daily" class="font-semibold text-gray-900 dark:text-white">-</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
                                        <span class="text-gray-600 dark:text-gray-400 text-sm">Last Month</span>
                                    </div>
                                    <span id="summary_last_month" class="font-semibold text-gray-900 dark:text-white">-</span>
                                </div>
                            </div>
                        </x-ui.card>
                    </div>
                </x-ui.card>
            </div>

        </div>
    </div>

</x-app-layout>

@vite(['resources/js/meter.js'])

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });
    
    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    document.getElementById('tab-' + tab).classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
}

window.showTab = showTab;

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        if (typeof renderConsumerMainTable === 'function') {
            renderConsumerMainTable();
        }
    }, 100);
});
</script>

<script style="display:none">
// Meter data
const meterData = [
    { id: 'M001', meter_no: 'MTR-001', consumer: 'Juan Dela Cruz', location: 'Area A', status: 'Active', last_read: 1250, read_date: '2024-01-15' },
    { id: 'M002', meter_no: 'MTR-002', consumer: 'Maria Santos', location: 'Area B', status: 'Active', last_read: 980, read_date: '2024-01-14' },
    { id: 'M003', meter_no: 'MTR-003', consumer: 'Pedro Garcia', location: 'Area C', status: 'Maintenance', last_read: 1420, read_date: '2024-01-12' },
    { id: 'M004', meter_no: 'MTR-004', consumer: 'Ana Rodriguez', location: 'Area A', status: 'Active', last_read: 750, read_date: '2024-01-13' },
    { id: 'M005', meter_no: 'MTR-005', consumer: 'Carlos Lopez', location: 'Area D', status: 'Inactive', last_read: 1100, read_date: '2024-01-10' },
    { id: 'M006', meter_no: 'MTR-006', consumer: 'Lisa Chen', location: 'Area B', status: 'Active', last_read: 890, read_date: '2024-01-14' },
    { id: 'M007', meter_no: 'MTR-007', consumer: 'Mark Johnson', location: 'Area C', status: 'Active', last_read: 1350, read_date: '2024-01-15' },
    { id: 'M008', meter_no: 'MTR-008', consumer: 'Sofia Martinez', location: 'Area A', status: 'Active', last_read: 670, read_date: '2024-01-13' }
];

function renderMeterTable() {
    const tableBody = document.getElementById('meterTable');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    meterData.forEach(meter => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors';
        tr.onclick = () => showMeterDetails(meter);
        
        const statusClass = meter.status === 'Active' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' :
                           meter.status === 'Maintenance' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200' :
                           'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200';
        
        tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">${meter.id}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">${meter.meter_no}</td>
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-tachometer-alt text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${meter.consumer}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400"><i class="fas fa-map-marker-alt mr-1"></i>${meter.location}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                    <i class="fas fa-${meter.status === 'Active' ? 'check-circle' : meter.status === 'Maintenance' ? 'wrench' : 'times-circle'} mr-1"></i>
                    ${meter.status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-bold text-gray-900 dark:text-gray-100">${meter.last_read} m³</div>
                <div class="text-xs text-gray-500 dark:text-gray-400"><i class="fas fa-calendar mr-1"></i>${meter.read_date}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    renderMeterTable();
    
    const backBtn = document.getElementById('backToListBtn');
    if (backBtn) {
        backBtn.addEventListener('click', showMeterTable);
    }
    
    // Search functionality
    const searchInput = document.getElementById('meterSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterMeters);
    }
});

function filterMeters() {
    const searchQuery = document.getElementById('meterSearchInput')?.value.toLowerCase() || '';
    const statusFilter = document.getElementById('meterStatusFilter')?.value || '';
    
    const filtered = meterData.filter(m => {
        const matchesSearch = m.meter_no.toLowerCase().includes(searchQuery) ||
                             m.consumer.toLowerCase().includes(searchQuery) ||
                             m.location.toLowerCase().includes(searchQuery) ||
                             m.id.toLowerCase().includes(searchQuery);
        const matchesStatus = !statusFilter || m.status === statusFilter;
        return matchesSearch && matchesStatus;
    });
    
    const tbody = document.getElementById('meterTable');
    tbody.innerHTML = filtered.map(meter => {
        const statusClass = meter.status === 'Active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                           meter.status === 'Maintenance' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                           'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
        return `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" onclick="showMeterDetails(${JSON.stringify(meter).replace(/"/g, '&quot;')})"> 
                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">${meter.id}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">${meter.meter_no}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-tachometer-alt text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${meter.consumer}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400"><i class="fas fa-map-marker-alt mr-1"></i>${meter.location}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                        <i class="fas fa-${meter.status === 'Active' ? 'check-circle' : meter.status === 'Maintenance' ? 'wrench' : 'times-circle'} mr-1"></i>
                        ${meter.status}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-bold text-gray-900 dark:text-gray-100">${meter.last_read} m³</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400"><i class="fas fa-calendar mr-1"></i>${meter.read_date}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function showMeterTable() {
    document.getElementById('tableSection').classList.remove('hidden');
    document.getElementById('meterDetailsSection').classList.add('hidden');
    
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';
    
    // Show meter summary cards when returning to main table
    const summaryWrapper = document.getElementById('meterSummaryWrapper');
    if (summaryWrapper) summaryWrapper.classList.remove('hidden');
}

function showMeterDetails(meter) {
    document.getElementById('tableSection').classList.add('hidden');
    document.getElementById('meterDetailsSection').classList.remove('hidden');
    
    // Hide summary cards
    const summaryWrapper = document.getElementById('meterSummaryWrapper');
    if (summaryWrapper) summaryWrapper.classList.add('hidden');
    
    // Update meter details
    document.getElementById('consumer_name').textContent = meter.consumer;
    document.getElementById('consumer_meterno').textContent = meter.meter_no;
    document.getElementById('consumer_location').textContent = meter.location;
    document.getElementById('consumer_installed').textContent = '2023-06-15';
    if (document.getElementById('consumer_brand')) {
        document.getElementById('consumer_brand').textContent = 'AquaMeter';
    }
    if (document.getElementById('consumer_meter_id')) {
        document.getElementById('consumer_meter_id').textContent = meter.id;
    }
    document.getElementById('meter_info_status').textContent = meter.status;
    document.getElementById('meter_info_installed').textContent = '2023-06-15';
    document.getElementById('meter_info_serial').textContent = 'SN' + meter.id;
    document.getElementById('last_reading_value').textContent = meter.last_read + ' m³';
    document.getElementById('last_reading_date').textContent = meter.read_date;
    
    // Populate activity list
    const activityList = document.getElementById('meterActivity_list');
    if (activityList) {
        activityList.innerHTML = `
            <div class="flex items-center py-2 border-b border-gray-200 dark:border-gray-700 last:border-0">
                <i class="fas fa-circle text-blue-400 text-xs mr-3"></i>
                <span class="text-gray-900 dark:text-white text-sm">Reading recorded: ${meter.last_read} m³</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 ml-auto">${meter.read_date}</span>
            </div>
            <div class="flex items-center py-2 border-b border-gray-200 dark:border-gray-700 last:border-0">
                <i class="fas fa-circle text-green-400 text-xs mr-3"></i>
                <span class="text-gray-900 dark:text-white text-sm">Meter status: ${meter.status}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 ml-auto">Today</span>
            </div>
        `;
    }
    
    // Update summary
    document.getElementById('summary_total_consumption').textContent = meter.last_read + ' m³';
    document.getElementById('summary_avg_daily').textContent = Math.round(meter.last_read / 30) + ' m³';
    document.getElementById('summary_last_month').textContent = Math.round(meter.last_read * 0.8) + ' m³';
}

function printMeterTable() {
    window.print();
}

function exportMetersPDF() {
    console.log('Exporting meters to PDF...');
    alert('PDF export functionality - Coming soon!');
}

function exportMetersExcel() {
    console.log('Exporting meters to Excel...');
    alert('Excel export functionality - Coming soon!');
}

window.showMeterTable = showMeterTable;
window.showMeterDetails = showMeterDetails;
window.renderMeterTable = renderMeterTable;
window.filterMeters = filterMeters;
window.printMeterTable = printMeterTable;
window.exportMetersPDF = exportMetersPDF;
window.exportMetersExcel = exportMetersExcel;

function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });
    
    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    document.getElementById('tab-' + tab).classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
}

</script>
