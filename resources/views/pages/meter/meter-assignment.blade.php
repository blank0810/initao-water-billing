<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                                <i class="fas fa-link text-indigo-600 dark:text-indigo-400 text-xl"></i>
                            </div>
                            Meter Assignment
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Assign meters to service connections for billing preparation</p>
                    </div>
                    <a href="{{ route('meter.management') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Management
                    </a>
                </div>
            </div>

            <!-- Main Content with Alpine.js -->
            <div x-data="meterAssignmentData()" x-init="init()">

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Meters</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.total_meters">0</p>
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
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="stats.available_meters">0</p>
                            </div>
                            <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Active Assignments</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="stats.active_assignments">0</p>
                            </div>
                            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                                <i class="fas fa-plug text-blue-600 dark:text-blue-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Unassigned Connections</p>
                                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400" x-text="stats.unassigned_connections">0</p>
                            </div>
                            <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                                <i class="fas fa-exclamation-circle text-orange-600 dark:text-orange-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search, Filters, and Add Button -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input type="text" x-model="searchQuery" placeholder="Search by account, customer, meter serial..."
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="sm:w-48">
                            <select x-model="statusFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="active">Active Only</option>
                                <option value="all">All Assignments</option>
                            </select>
                        </div>
                        <button @click="openAssignModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2 whitespace-nowrap transition">
                            <i class="fas fa-plus"></i>
                            <span>Assign Meter</span>
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div x-show="loading" class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-2 text-gray-600 dark:text-gray-400">Loading assignments...</span>
                </div>

                <!-- Table -->
                <div x-show="!loading" class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Account / Customer</th>
                                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter</th>
                                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Installed</th>
                                    <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Install Read</th>
                                    <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="item in paginatedData" :key="item.assignment_id">
                                    <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="item.account_no"></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400" x-text="item.customer_name"></div>
                                            <div class="text-xs text-gray-400 dark:text-gray-500" x-text="item.barangay"></div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-mono font-medium text-gray-900 dark:text-gray-100" x-text="item.meter_serial"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="item.meter_brand"></div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200" x-text="item.account_type"></span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100" x-text="item.installed_at"></td>
                                        <td class="px-4 py-3 text-right text-sm font-mono text-gray-900 dark:text-gray-100">
                                            <span x-text="parseFloat(item.install_read).toFixed(3)"></span> m<sup>3</sup>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="item.status_class" x-text="item.status"></span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button @click="viewAssignment(item.assignment_id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded" title="View Details">
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
                                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                            <p>No meter assignments found</p>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div x-show="!loading && filteredData.length > 0" class="flex justify-between items-center mt-4 flex-wrap gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
                        <select x-model.number="pageSize" @change="currentPage = 1" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
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

                <!-- Assign Meter Modal -->
                <div x-show="showAssignModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div @click.away="showAssignModal = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                <i class="fas fa-link text-blue-600 mr-2"></i>Assign Meter
                            </h3>
                            <button @click="showAssignModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Service Connection Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Connection *</label>
                                <select x-model="assignForm.connection_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select a connection...</option>
                                    <template x-for="conn in unassignedConnections" :key="conn.connection_id">
                                        <option :value="conn.connection_id" x-text="conn.label"></option>
                                    </template>
                                </select>
                                <template x-if="unassignedConnections.length === 0">
                                    <p class="mt-1 text-sm text-orange-600">No unassigned connections available</p>
                                </template>
                                <template x-if="assignErrors.connection_id">
                                    <p class="mt-1 text-sm text-red-600" x-text="assignErrors.connection_id"></p>
                                </template>
                            </div>

                            <!-- Selected Connection Info -->
                            <template x-if="selectedConnection">
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                    <div class="text-sm">
                                        <p class="font-medium text-blue-800 dark:text-blue-200" x-text="selectedConnection.customer_name"></p>
                                        <p class="text-blue-600 dark:text-blue-300">
                                            <span x-text="selectedConnection.account_type"></span> - <span x-text="selectedConnection.barangay"></span>
                                        </p>
                                    </div>
                                </div>
                            </template>

                            <!-- Meter Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meter *</label>
                                <select x-model="assignForm.meter_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select a meter...</option>
                                    <template x-for="meter in availableMeters" :key="meter.mtr_id">
                                        <option :value="meter.mtr_id" x-text="meter.label"></option>
                                    </template>
                                </select>
                                <template x-if="availableMeters.length === 0">
                                    <p class="mt-1 text-sm text-orange-600">No available meters. Add new meters in inventory first.</p>
                                </template>
                                <template x-if="assignErrors.meter_id">
                                    <p class="mt-1 text-sm text-red-600" x-text="assignErrors.meter_id"></p>
                                </template>
                            </div>

                            <!-- Installation Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Installation Date *</label>
                                <input type="date" x-model="assignForm.installed_at" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <template x-if="assignErrors.installed_at">
                                    <p class="mt-1 text-sm text-red-600" x-text="assignErrors.installed_at"></p>
                                </template>
                            </div>

                            <!-- Initial Reading -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Initial Reading (m<sup>3</sup>)</label>
                                <input type="number" x-model="assignForm.install_read" step="0.001" min="0" placeholder="0.000"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter the meter reading at the time of installation</p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button @click="showAssignModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Cancel
                            </button>
                            <button @click="submitAssign()" :disabled="submitting" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50 transition">
                                <span x-show="!submitting"><i class="fas fa-link mr-2"></i>Assign Meter</span>
                                <span x-show="submitting"><i class="fas fa-spinner fa-spin mr-2"></i>Assigning...</span>
                            </button>
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

                        <template x-if="removeAssignment">
                            <div class="mb-6">
                                <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-lg mb-4">
                                    <p class="text-sm text-red-800 dark:text-red-200">
                                        You are about to remove meter <span class="font-bold" x-text="removeAssignment.meter_serial"></span> from account <span class="font-bold" x-text="removeAssignment.account_no"></span>.
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <!-- Removal Date -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Removal Date *</label>
                                        <input type="date" x-model="removeForm.removed_at" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <!-- Final Reading -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Final Reading (m<sup>3</sup>) *</label>
                                        <input type="number" x-model="removeForm.removal_read" step="0.001" :min="removeAssignment.install_read" placeholder="0.000"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Install reading: <span x-text="parseFloat(removeAssignment.install_read).toFixed(3)"></span> m<sup>3</sup>
                                        </p>
                                        <template x-if="removeErrors.removal_read">
                                            <p class="mt-1 text-sm text-red-600" x-text="removeErrors.removal_read"></p>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="flex justify-end gap-3">
                            <button @click="showRemoveModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Cancel
                            </button>
                            <button @click="submitRemove()" :disabled="submitting" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg disabled:opacity-50 transition">
                                <span x-show="!submitting"><i class="fas fa-unlink mr-2"></i>Remove Meter</span>
                                <span x-show="submitting"><i class="fas fa-spinner fa-spin mr-2"></i>Removing...</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- View Assignment Modal -->
                <div x-show="showViewModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div @click.away="showViewModal = false" class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Assignment Details</h3>
                            <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>

                        <template x-if="viewAssignmentData">
                            <div class="space-y-4">
                                <!-- Customer & Account Info -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Account No</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="viewAssignmentData.account_no"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Status</p>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="viewAssignmentData.status_class" x-text="viewAssignmentData.status"></span>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Customer Name</p>
                                    <p class="font-medium text-gray-900 dark:text-white" x-text="viewAssignmentData.customer_name"></p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Account Type</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="viewAssignmentData.account_type"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Location</p>
                                        <p class="font-medium text-gray-900 dark:text-white" x-text="viewAssignmentData.barangay"></p>
                                    </div>
                                </div>

                                <!-- Meter Info -->
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i class="fas fa-tachometer-alt mr-2"></i>Meter Information
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Serial Number</p>
                                            <p class="font-mono font-medium text-gray-900 dark:text-white" x-text="viewAssignmentData.meter_serial"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Brand</p>
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="viewAssignmentData.meter_brand"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assignment Info -->
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                        <i class="fas fa-calendar-alt mr-2"></i>Assignment Details
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Installed Date</p>
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="viewAssignmentData.installed_at"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Initial Reading</p>
                                            <p class="font-mono font-medium text-gray-900 dark:text-white">
                                                <span x-text="parseFloat(viewAssignmentData.install_read).toFixed(3)"></span> m<sup>3</sup>
                                            </p>
                                        </div>
                                    </div>
                                    <template x-if="viewAssignmentData.removed_at">
                                        <div class="grid grid-cols-2 gap-4 mt-3">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Removed Date</p>
                                                <p class="font-medium text-gray-900 dark:text-white" x-text="viewAssignmentData.removed_at"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Final Reading</p>
                                                <p class="font-mono font-medium text-gray-900 dark:text-white">
                                                    <span x-text="parseFloat(viewAssignmentData.removal_read).toFixed(3)"></span> m<sup>3</sup>
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Recent Readings -->
                                <template x-if="viewAssignmentData.recent_readings && viewAssignmentData.recent_readings.length > 0">
                                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                            <i class="fas fa-history mr-2"></i>Recent Readings
                                        </h4>
                                        <div class="space-y-2">
                                            <template x-for="reading in viewAssignmentData.recent_readings" :key="reading.reading_id">
                                                <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded text-sm">
                                                    <span class="text-gray-600 dark:text-gray-400" x-text="reading.reading_date"></span>
                                                    <span class="font-mono text-gray-900 dark:text-white">
                                                        <span x-text="parseFloat(reading.current_reading).toFixed(3)"></span> m<sup>3</sup>
                                                        <span class="text-xs text-gray-500">(+<span x-text="parseFloat(reading.consumption).toFixed(3)"></span>)</span>
                                                    </span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div class="flex justify-end gap-3 mt-6">
                            <button @click="showViewModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Close
                            </button>
                            <button x-show="viewAssignmentData && viewAssignmentData.is_active" @click="showViewModal = false; openRemoveModal(viewAssignmentData)" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                                <i class="fas fa-unlink mr-2"></i>Remove Meter
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
    function meterAssignmentData() {
        return {
            loading: true,
            submitting: false,
            searchQuery: '',
            statusFilter: 'active',
            pageSize: 10,
            currentPage: 1,
            data: [],
            stats: {
                total_meters: 0,
                available_meters: 0,
                active_assignments: 0,
                unassigned_connections: 0
            },

            // Selection data
            availableMeters: [],
            unassignedConnections: [],

            // Modals
            showAssignModal: false,
            showRemoveModal: false,
            showViewModal: false,

            // Forms
            assignForm: {
                connection_id: '',
                meter_id: '',
                installed_at: new Date().toISOString().split('T')[0],
                install_read: 0
            },
            assignErrors: {},

            removeForm: {
                removed_at: new Date().toISOString().split('T')[0],
                removal_read: 0
            },
            removeErrors: {},
            removeAssignment: null,

            viewAssignmentData: null,

            async init() {
                await this.fetchAssignments();
                this.$watch('searchQuery', () => this.currentPage = 1);
                this.$watch('statusFilter', () => {
                    this.currentPage = 1;
                    this.fetchAssignments();
                });
                this.$watch('pageSize', () => this.currentPage = 1);
            },

            async fetchAssignments() {
                this.loading = true;
                try {
                    const showAll = this.statusFilter === 'all' ? '?all=true' : '';
                    const response = await fetch('/meter-assignments' + showAll);
                    const result = await response.json();
                    if (result.success) {
                        this.data = result.data;
                        this.stats = result.stats;
                    }
                } catch (error) {
                    console.error('Error fetching assignments:', error);
                    this.showAlert('Failed to load assignments', 'error');
                } finally {
                    this.loading = false;
                }
            },

            async fetchAvailableMeters() {
                try {
                    const response = await fetch('/meter-assignments/available-meters');
                    const result = await response.json();
                    if (result.success) {
                        this.availableMeters = result.data;
                    }
                } catch (error) {
                    console.error('Error fetching available meters:', error);
                }
            },

            async fetchUnassignedConnections() {
                try {
                    const response = await fetch('/meter-assignments/unassigned-connections');
                    const result = await response.json();
                    if (result.success) {
                        this.unassignedConnections = result.data;
                    }
                } catch (error) {
                    console.error('Error fetching unassigned connections:', error);
                }
            },

            get filteredData() {
                let filtered = this.data;
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(a =>
                        a.account_no.toLowerCase().includes(query) ||
                        a.customer_name.toLowerCase().includes(query) ||
                        a.meter_serial.toLowerCase().includes(query) ||
                        (a.barangay && a.barangay.toLowerCase().includes(query))
                    );
                }
                return filtered;
            },

            get selectedConnection() {
                if (!this.assignForm.connection_id) return null;
                return this.unassignedConnections.find(c => c.connection_id == this.assignForm.connection_id);
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

            // Assign Modal
            async openAssignModal() {
                this.assignForm = {
                    connection_id: '',
                    meter_id: '',
                    installed_at: new Date().toISOString().split('T')[0],
                    install_read: 0
                };
                this.assignErrors = {};
                await Promise.all([
                    this.fetchAvailableMeters(),
                    this.fetchUnassignedConnections()
                ]);
                this.showAssignModal = true;
            },

            async submitAssign() {
                this.assignErrors = {};
                if (!this.assignForm.connection_id) this.assignErrors.connection_id = 'Please select a connection';
                if (!this.assignForm.meter_id) this.assignErrors.meter_id = 'Please select a meter';
                if (!this.assignForm.installed_at) this.assignErrors.installed_at = 'Installation date is required';
                if (Object.keys(this.assignErrors).length > 0) return;

                this.submitting = true;
                try {
                    const response = await fetch('/meter-assignments', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.assignForm)
                    });
                    const result = await response.json();

                    if (result.success) {
                        this.showAlert('Meter assigned successfully', 'success');
                        this.showAssignModal = false;
                        await this.fetchAssignments();
                    } else {
                        this.showAlert(result.message || 'Failed to assign meter', 'error');
                    }
                } catch (error) {
                    console.error('Error assigning meter:', error);
                    this.showAlert('Failed to assign meter', 'error');
                } finally {
                    this.submitting = false;
                }
            },

            // Remove Modal
            openRemoveModal(assignment) {
                this.removeAssignment = assignment;
                this.removeForm = {
                    removed_at: new Date().toISOString().split('T')[0],
                    removal_read: assignment.install_read || 0
                };
                this.removeErrors = {};
                this.showRemoveModal = true;
            },

            async submitRemove() {
                this.removeErrors = {};
                if (parseFloat(this.removeForm.removal_read) < parseFloat(this.removeAssignment.install_read)) {
                    this.removeErrors.removal_read = 'Final reading cannot be less than install reading';
                    return;
                }

                this.submitting = true;
                try {
                    const response = await fetch(`/meter-assignments/${this.removeAssignment.assignment_id}/remove`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.removeForm)
                    });
                    const result = await response.json();

                    if (result.success) {
                        this.showAlert('Meter removed successfully', 'success');
                        this.showRemoveModal = false;
                        await this.fetchAssignments();
                    } else {
                        this.showAlert(result.message || 'Failed to remove meter', 'error');
                    }
                } catch (error) {
                    console.error('Error removing meter:', error);
                    this.showAlert('Failed to remove meter', 'error');
                } finally {
                    this.submitting = false;
                }
            },

            // View Modal
            async viewAssignment(assignmentId) {
                try {
                    const response = await fetch(`/meter-assignments/${assignmentId}`);
                    const result = await response.json();
                    if (result.success) {
                        this.viewAssignmentData = result.data;
                        this.showViewModal = true;
                    } else {
                        this.showAlert('Failed to load assignment details', 'error');
                    }
                } catch (error) {
                    console.error('Error fetching assignment:', error);
                    this.showAlert('Failed to load assignment details', 'error');
                }
            },

            showAlert(message, type) {
                if (window.showAlert) {
                    window.showAlert(message, type);
                } else if (window.showToast) {
                    window.showToast(message, type);
                } else {
                    alert(message);
                }
            }
        }
    }
    </script>
</x-app-layout>
