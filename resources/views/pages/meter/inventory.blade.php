<div x-data="inventoryData()" x-init="init()">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Meters</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.total">0</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <i class="fas fa-tachometer-alt text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Available</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="stats.available">0</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Installed</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="stats.installed">0</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <i class="fas fa-plug text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Faulty</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400" x-text="stats.faulty">0</p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search, Filters, and Add Button -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <x-ui.search-bar placeholder="Search by serial, brand, or consumer..." x-model="searchQuery" />
            </div>
            <div class="sm:w-48">
                <select x-model="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Status</option>
                    <option value="available">Available</option>
                    <option value="installed">Installed</option>
                    <option value="faulty">Faulty</option>
                    <option value="removed">Removed</option>
                </select>
            </div>
            <button @click="openAddModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus"></i>
                <span>Add Meter</span>
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="ml-2 text-gray-600 dark:text-gray-400">Loading meters...</span>
    </div>

    <!-- Table -->
    <div x-show="!loading" class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter ID</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Serial Number</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Brand</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-for="item in paginatedData" :key="item.mtr_id">
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100" x-text="item.mtr_id"></td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100" x-text="item.mtr_serial"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                <span x-text="item.mtr_brand"></span>
                                <template x-if="item.consumer">
                                    <div class="text-xs text-gray-500 dark:text-gray-400" x-text="item.consumer"></div>
                                </template>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="item.status_class" x-text="item.status_label"></span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="viewMeter(item.mtr_id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="openEditModal(item)" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 p-2 rounded" title="Edit Meter">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button x-show="item.can_delete" @click="confirmDelete(item)" class="text-red-600 hover:text-red-900 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 p-2 rounded" title="Delete Meter">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="paginatedData.length === 0 && !loading">
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>No meters found</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div x-show="!loading" class="flex justify-between items-center mt-4 flex-wrap gap-4">
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

    <!-- Add Meter Modal -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div @click.away="showAddModal = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add New Meter</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meter Serial Number *</label>
                    <input type="text" x-model="addForm.mtr_serial" placeholder="MTR-XXX-XXXXX" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <template x-if="addErrors.mtr_serial">
                        <p class="mt-1 text-sm text-red-600" x-text="addErrors.mtr_serial"></p>
                    </template>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meter Brand *</label>
                    <select x-model="addForm.mtr_brand" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select brand...</option>
                        <option value="AquaMeter">AquaMeter</option>
                        <option value="FlowTech">FlowTech</option>
                        <option value="WaterPro">WaterPro</option>
                        <option value="HydroSense">HydroSense</option>
                    </select>
                    <template x-if="addErrors.mtr_brand">
                        <p class="mt-1 text-sm text-red-600" x-text="addErrors.mtr_brand"></p>
                    </template>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button @click="showAddModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button @click="submitAdd()" :disabled="submitting" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50">
                    <i class="fas fa-check mr-2"></i>Add Meter
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Meter Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div @click.away="showEditModal = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Meter</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meter Serial Number *</label>
                    <input type="text" x-model="editForm.mtr_serial" :disabled="editForm.is_installed" placeholder="MTR-XXX-XXXXX" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="editForm.is_installed">
                        <p class="mt-1 text-xs text-yellow-600">Serial cannot be changed while meter is installed</p>
                    </template>
                    <template x-if="editErrors.mtr_serial">
                        <p class="mt-1 text-sm text-red-600" x-text="editErrors.mtr_serial"></p>
                    </template>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meter Brand *</label>
                    <select x-model="editForm.mtr_brand" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select brand...</option>
                        <option value="AquaMeter">AquaMeter</option>
                        <option value="FlowTech">FlowTech</option>
                        <option value="WaterPro">WaterPro</option>
                        <option value="HydroSense">HydroSense</option>
                    </select>
                    <template x-if="editErrors.mtr_brand">
                        <p class="mt-1 text-sm text-red-600" x-text="editErrors.mtr_brand"></p>
                    </template>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button @click="showEditModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button @click="submitEdit()" :disabled="submitting" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- View Meter Modal -->
    <div x-show="showViewModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div @click.away="showViewModal = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-lg w-full mx-4">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Meter Details</h3>
                <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <template x-if="viewMeterData">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Meter ID</p>
                            <p class="font-medium text-gray-900 dark:text-white" x-text="viewMeterData.mtr_id"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="viewMeterData.status_class" x-text="viewMeterData.status_label"></span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Serial Number</p>
                            <p class="font-medium font-mono text-gray-900 dark:text-white" x-text="viewMeterData.mtr_serial"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Brand</p>
                            <p class="font-medium text-gray-900 dark:text-white" x-text="viewMeterData.mtr_brand"></p>
                        </div>
                    </div>

                    <template x-if="viewMeterData.consumer">
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Assigned To</p>
                            <p class="font-medium text-gray-900 dark:text-white" x-text="viewMeterData.consumer"></p>
                        </div>
                    </template>

                    <template x-if="viewMeterData.assignment_history && viewMeterData.assignment_history.length > 0">
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assignment History</p>
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                <template x-for="assignment in viewMeterData.assignment_history" :key="assignment.assignment_id">
                                    <div class="text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                        <p class="font-medium" x-text="assignment.consumer"></p>
                                        <p class="text-gray-500 dark:text-gray-400">
                                            <span x-text="assignment.installed_at"></span>
                                            <template x-if="assignment.removed_at">
                                                <span> - <span x-text="assignment.removed_at"></span></span>
                                            </template>
                                            <template x-if="assignment.is_active">
                                                <span class="text-green-600"> (Current)</span>
                                            </template>
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div class="flex justify-end gap-3 mt-6">
                <button @click="showViewModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Close
                </button>
                <button @click="showViewModal = false; openEditModal(viewMeterData)" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg">
                    <i class="fas fa-edit mr-2"></i>Edit
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div @click.away="showDeleteModal = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Delete Meter</h3>
                <button @click="showDeleteModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <p class="text-center text-gray-700 dark:text-gray-300">
                    Are you sure you want to delete meter <span class="font-semibold" x-text="deleteMeterData?.mtr_serial"></span>?
                </p>
                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-2">This action cannot be undone.</p>
            </div>

            <div class="flex justify-end gap-3">
                <button @click="showDeleteModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button @click="submitDelete()" :disabled="submitting" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg disabled:opacity-50">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function inventoryData() {
    return {
        loading: true,
        submitting: false,
        searchQuery: '',
        statusFilter: 'all',
        pageSize: 10,
        currentPage: 1,
        data: [],
        stats: { total: 0, available: 0, installed: 0, faulty: 0, removed: 0 },

        // Modals
        showAddModal: false,
        showEditModal: false,
        showViewModal: false,
        showDeleteModal: false,

        // Forms
        addForm: { mtr_serial: '', mtr_brand: '' },
        addErrors: {},
        editForm: { mtr_id: null, mtr_serial: '', mtr_brand: '', is_installed: false },
        editErrors: {},
        viewMeterData: null,
        deleteMeterData: null,

        async init() {
            await this.fetchMeters();
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('statusFilter', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);
        },

        async fetchMeters() {
            this.loading = true;
            try {
                const response = await fetch('/meters/list');
                const result = await response.json();
                if (result.success) {
                    this.data = result.data;
                    this.stats = result.stats;
                }
            } catch (error) {
                console.error('Error fetching meters:', error);
                this.showAlert('Failed to load meters', 'error');
            } finally {
                this.loading = false;
            }
        },

        get filteredData() {
            let filtered = this.data;
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(m =>
                    m.mtr_serial.toLowerCase().includes(query) ||
                    m.mtr_brand.toLowerCase().includes(query) ||
                    (m.consumer && m.consumer.toLowerCase().includes(query))
                );
            }
            if (this.statusFilter !== 'all') {
                filtered = filtered.filter(m => m.status === this.statusFilter);
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

        // Add Meter
        openAddModal() {
            this.addForm = { mtr_serial: '', mtr_brand: '' };
            this.addErrors = {};
            this.showAddModal = true;
        },

        async submitAdd() {
            this.addErrors = {};
            if (!this.addForm.mtr_serial) this.addErrors.mtr_serial = 'Serial number is required';
            if (!this.addForm.mtr_brand) this.addErrors.mtr_brand = 'Brand is required';
            if (Object.keys(this.addErrors).length > 0) return;

            this.submitting = true;
            try {
                const response = await fetch('/meters', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.addForm)
                });
                const result = await response.json();

                if (result.success) {
                    this.showAlert('Meter added successfully', 'success');
                    this.showAddModal = false;
                    await this.fetchMeters();
                } else {
                    this.showAlert(result.message || 'Failed to add meter', 'error');
                }
            } catch (error) {
                console.error('Error adding meter:', error);
                this.showAlert('Failed to add meter', 'error');
            } finally {
                this.submitting = false;
            }
        },

        // Edit Meter
        openEditModal(meter) {
            this.editForm = {
                mtr_id: meter.mtr_id,
                mtr_serial: meter.mtr_serial,
                mtr_brand: meter.mtr_brand,
                is_installed: meter.is_installed
            };
            this.editErrors = {};
            this.showEditModal = true;
        },

        async submitEdit() {
            this.editErrors = {};
            if (!this.editForm.mtr_serial) this.editErrors.mtr_serial = 'Serial number is required';
            if (!this.editForm.mtr_brand) this.editErrors.mtr_brand = 'Brand is required';
            if (Object.keys(this.editErrors).length > 0) return;

            this.submitting = true;
            try {
                const response = await fetch(`/meters/${this.editForm.mtr_id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        mtr_serial: this.editForm.mtr_serial,
                        mtr_brand: this.editForm.mtr_brand
                    })
                });
                const result = await response.json();

                if (result.success) {
                    this.showAlert('Meter updated successfully', 'success');
                    this.showEditModal = false;
                    await this.fetchMeters();
                } else {
                    this.showAlert(result.message || 'Failed to update meter', 'error');
                }
            } catch (error) {
                console.error('Error updating meter:', error);
                this.showAlert('Failed to update meter', 'error');
            } finally {
                this.submitting = false;
            }
        },

        // View Meter
        async viewMeter(meterId) {
            try {
                const response = await fetch(`/meters/${meterId}`);
                const result = await response.json();
                if (result.success) {
                    this.viewMeterData = result.data;
                    this.showViewModal = true;
                } else {
                    this.showAlert('Failed to load meter details', 'error');
                }
            } catch (error) {
                console.error('Error fetching meter:', error);
                this.showAlert('Failed to load meter details', 'error');
            }
        },

        // Delete Meter
        confirmDelete(meter) {
            this.deleteMeterData = meter;
            this.showDeleteModal = true;
        },

        async submitDelete() {
            this.submitting = true;
            try {
                const response = await fetch(`/meters/${this.deleteMeterData.mtr_id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const result = await response.json();

                if (result.success) {
                    this.showAlert('Meter deleted successfully', 'success');
                    this.showDeleteModal = false;
                    await this.fetchMeters();
                } else {
                    this.showAlert(result.message || 'Failed to delete meter', 'error');
                }
            } catch (error) {
                console.error('Error deleting meter:', error);
                this.showAlert('Failed to delete meter', 'error');
            } finally {
                this.submitting = false;
            }
        },

        showAlert(message, type) {
            if (window.showAlert) {
                window.showAlert(message, type);
            } else {
                alert(message);
            }
        }
    }
}
</script>
