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
            <div id="content-meters" class="tab-content" x-data="consumerMetersData()" x-init="init()">
                <!-- Summary Cards -->
                <div id="meterSummaryWrapper" class="mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-5 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <i class="fas fa-tachometer-alt text-3xl opacity-80"></i>
                                <span class="text-xs font-semibold bg-blue-400 bg-opacity-30 px-2 py-1 rounded">TOTAL</span>
                            </div>
                            <div class="text-3xl font-bold mb-1" x-text="stats.total_meters">0</div>
                            <div class="text-xs opacity-90">Total Meters</div>
                        </div>

                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-5 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <i class="fas fa-plug text-3xl opacity-80"></i>
                                <span class="text-xs font-semibold bg-green-400 bg-opacity-30 px-2 py-1 rounded">ASSIGNED</span>
                            </div>
                            <div class="text-3xl font-bold mb-1" x-text="stats.active_assignments">0</div>
                            <div class="text-xs opacity-90">Active Assignments</div>
                        </div>

                        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg shadow-lg p-5 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <i class="fas fa-box-open text-3xl opacity-80"></i>
                                <span class="text-xs font-semibold bg-cyan-400 bg-opacity-30 px-2 py-1 rounded">AVAILABLE</span>
                            </div>
                            <div class="text-3xl font-bold mb-1" x-text="stats.available_meters">0</div>
                            <div class="text-xs opacity-90">Available Meters</div>
                        </div>

                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-5 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <i class="fas fa-exclamation-circle text-3xl opacity-80"></i>
                                <span class="text-xs font-semibold bg-orange-400 bg-opacity-30 px-2 py-1 rounded">PENDING</span>
                            </div>
                            <div class="text-3xl font-bold mb-1" x-text="stats.unassigned_connections">0</div>
                            <div class="text-xs opacity-90">Unassigned Connections</div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div id="searchFilterSection" class="mb-6">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input type="text" x-model="searchQuery" placeholder="Search by meter serial, account no, customer name..."
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="sm:w-48">
                            <select x-model="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="all">All Status</option>
                                <option value="active">Active Only</option>
                                <option value="removed">Removed</option>
                            </select>
                        </div>
                        <div class="sm:w-48">
                            <select x-model="typeFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="all">All Types</option>
                                <option value="Residential">Residential</option>
                                <option value="Commercial">Commercial</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div x-show="loading" class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-2 text-gray-600 dark:text-gray-400">Loading consumer meters...</span>
                </div>

                <!-- Main Table Section -->
                <div id="tableSection" x-show="!loading">
                    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter Serial</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Account / Customer</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Location</th>
                                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Installed</th>
                                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Install Reading</th>
                                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <template x-for="item in paginatedData" :key="item.assignment_id">
                                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-mono font-medium text-gray-900 dark:text-gray-100" x-text="item.meter_serial"></div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="item.meter_brand"></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="item.account_no"></div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400" x-text="item.customer_name"></div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300" x-text="item.barangay"></td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200" x-text="item.account_type"></span>
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300" x-text="item.installed_at"></td>
                                            <td class="px-4 py-3 text-right text-sm font-mono text-gray-700 dark:text-gray-300">
                                                <span x-text="parseFloat(item.install_read || 0).toFixed(3)"></span> m<sup>3</sup>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="item.status_class" x-text="item.status"></span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    <button @click="viewDetails(item)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button x-show="item.is_active" @click="openRemoveModal(item)" class="text-red-600 hover:text-red-900 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 p-2 rounded" title="Remove Meter">
                                                        <i class="fas fa-unlink"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="paginatedData.length === 0 && !loading">
                                        <tr>
                                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                                <p>No meter assignments found</p>
                                                <p class="mt-2 text-xs">Click "Assign Meter" to assign meters to service connections</p>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div x-show="filteredData.length > 0" class="flex justify-between items-center mt-4 flex-wrap gap-4">
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

                <!-- Remove Meter Modal -->
                <div x-show="showRemoveModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div @click.away="showRemoveModal = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                <i class="fas fa-unlink text-red-600 mr-2"></i>Remove Meter
                            </h3>
                            <button @click="showRemoveModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>

                        <template x-if="removeItem">
                            <div class="mb-6">
                                <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-lg mb-4">
                                    <p class="text-sm text-red-800 dark:text-red-200">
                                        Remove meter <span class="font-bold" x-text="removeItem.meter_serial"></span> from account <span class="font-bold" x-text="removeItem.account_no"></span>?
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Removal Date *</label>
                                        <input type="date" x-model="removeForm.removed_at" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Final Reading (m<sup>3</sup>) *</label>
                                        <input type="number" x-model="removeForm.removal_read" step="0.001" :min="removeItem.install_read" placeholder="0.000"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500">Install reading: <span x-text="parseFloat(removeItem.install_read || 0).toFixed(3)"></span> m<sup>3</sup></p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="flex justify-end gap-3">
                            <button @click="showRemoveModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button @click="submitRemove()" :disabled="submitting" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg disabled:opacity-50">
                                <span x-show="!submitting"><i class="fas fa-unlink mr-2"></i>Remove</span>
                                <span x-show="submitting"><i class="fas fa-spinner fa-spin mr-2"></i>Removing...</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- View Details Modal -->
                <div x-show="showDetailsModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div @click.away="showDetailsModal = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Assignment Details</h3>
                            <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>

                        <template x-if="detailsItem">
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Account No</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="detailsItem.account_no"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Status</p>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="detailsItem.status_class" x-text="detailsItem.status"></span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Customer</p>
                                    <p class="font-medium text-gray-900 dark:text-white" x-text="detailsItem.customer_name"></p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Account Type</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="detailsItem.account_type"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Location</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="detailsItem.barangay"></p>
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Meter Information</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Serial</p>
                                            <p class="font-mono font-medium text-gray-900 dark:text-white" x-text="detailsItem.meter_serial"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Brand</p>
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="detailsItem.meter_brand"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Assignment Details</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Installed</p>
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="detailsItem.installed_at"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Install Reading</p>
                                            <p class="font-mono font-medium text-gray-900 dark:text-white"><span x-text="parseFloat(detailsItem.install_read || 0).toFixed(3)"></span> m<sup>3</sup></p>
                                        </div>
                                    </div>
                                    <template x-if="detailsItem.removed_at">
                                        <div class="grid grid-cols-2 gap-4 mt-3">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Removed</p>
                                                <p class="font-medium text-gray-900 dark:text-white" x-text="detailsItem.removed_at"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Final Reading</p>
                                                <p class="font-mono font-medium text-gray-900 dark:text-white"><span x-text="parseFloat(detailsItem.removal_read || 0).toFixed(3)"></span> m<sup>3</sup></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="flex justify-end gap-3 mt-6">
                            <button @click="showDetailsModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
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
// Consumer Meters Data - Fetches from API
function consumerMetersData() {
    return {
        loading: true,
        submitting: false,
        searchQuery: '',
        statusFilter: 'all',
        typeFilter: 'all',
        pageSize: 10,
        currentPage: 1,
        data: [],
        stats: {
            total_meters: 0,
            available_meters: 0,
            active_assignments: 0,
            unassigned_connections: 0
        },

        // Modals
        showRemoveModal: false,
        showDetailsModal: false,
        removeItem: null,
        detailsItem: null,
        removeForm: {
            removed_at: new Date().toISOString().split('T')[0],
            removal_read: 0
        },

        async init() {
            await this.fetchData();
            this.$watch('searchQuery', () => this.currentPage = 1);
            this.$watch('statusFilter', () => this.currentPage = 1);
            this.$watch('typeFilter', () => this.currentPage = 1);
            this.$watch('pageSize', () => this.currentPage = 1);

            // Expose refresh function globally
            window.refreshConsumerMeters = () => this.fetchData();
        },

        async fetchData() {
            this.loading = true;
            try {
                const response = await fetch('/meter-assignments?all=true');
                const result = await response.json();
                if (result.success) {
                    this.data = result.data || [];
                    this.stats = result.stats || this.stats;
                }
            } catch (error) {
                console.error('Error fetching meter assignments:', error);
            } finally {
                this.loading = false;
            }
        },

        get filteredData() {
            let filtered = this.data;

            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(item =>
                    (item.meter_serial && item.meter_serial.toLowerCase().includes(query)) ||
                    (item.account_no && item.account_no.toLowerCase().includes(query)) ||
                    (item.customer_name && item.customer_name.toLowerCase().includes(query)) ||
                    (item.barangay && item.barangay.toLowerCase().includes(query))
                );
            }

            if (this.statusFilter === 'active') {
                filtered = filtered.filter(item => item.is_active);
            } else if (this.statusFilter === 'removed') {
                filtered = filtered.filter(item => !item.is_active);
            }

            if (this.typeFilter !== 'all') {
                filtered = filtered.filter(item => item.account_type === this.typeFilter);
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

        viewDetails(item) {
            this.detailsItem = item;
            this.showDetailsModal = true;
        },

        openRemoveModal(item) {
            this.removeItem = item;
            this.removeForm = {
                removed_at: new Date().toISOString().split('T')[0],
                removal_read: item.install_read || 0
            };
            this.showRemoveModal = true;
        },

        async submitRemove() {
            if (!this.removeItem) return;

            if (parseFloat(this.removeForm.removal_read) < parseFloat(this.removeItem.install_read)) {
                if (window.showAlert) {
                    window.showAlert('Final reading cannot be less than install reading', 'error');
                } else {
                    alert('Final reading cannot be less than install reading');
                }
                return;
            }

            this.submitting = true;
            try {
                const response = await fetch(`/meter-assignments/${this.removeItem.assignment_id}/remove`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.removeForm)
                });

                const result = await response.json();

                if (result.success) {
                    this.showRemoveModal = false;
                    if (window.showAlert) {
                        window.showAlert('Meter removed successfully', 'success');
                    } else {
                        alert('Meter removed successfully');
                    }
                    await this.fetchData();
                } else {
                    if (window.showAlert) {
                        window.showAlert(result.message || 'Failed to remove meter', 'error');
                    } else {
                        alert(result.message || 'Failed to remove meter');
                    }
                }
            } catch (error) {
                console.error('Error removing meter:', error);
                if (window.showAlert) {
                    window.showAlert('Failed to remove meter', 'error');
                } else {
                    alert('Failed to remove meter');
                }
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>

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
