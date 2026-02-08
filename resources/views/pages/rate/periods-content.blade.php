<div x-data="periodsData()" x-init="init()">
    <!-- Header with Search and Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="relative">
            <input
                type="text"
                x-model="searchQuery"
                @input.debounce.300ms="filterPeriods()"
                placeholder="Search periods..."
                class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        <button
            @click="openCreateModal()"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
        >
            <i class="fas fa-plus mr-2"></i>
            Create Period
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <i class="fas fa-calendar text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.total">0</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Periods</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <i class="fas fa-unlock text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.open">0</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Open</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <i class="fas fa-lock text-gray-600 dark:text-gray-400"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.closed">0</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Closed</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <i class="fas fa-tags text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.with_rates">0</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">With Custom Rates</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Periods Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bills</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rates</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Loading State -->
                    <template x-if="loading">
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-spinner fa-spin text-xl"></i>
                                    <span>Loading periods...</span>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!loading && filteredPeriods.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-calendar-times text-4xl opacity-50"></i>
                                    <p x-text="searchQuery ? 'No periods match your search' : 'No periods found'"></p>
                                    <button x-show="!searchQuery" @click="openCreateModal()" class="mt-2 text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                        <i class="fas fa-plus mr-1"></i> Create your first period
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Period Rows -->
                    <template x-for="period in filteredPeriods" :key="period.per_id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="period.per_name"></span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="period.per_code"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400" x-text="period.date_range"></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200" x-text="period.bills_count"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <template x-if="period.has_custom_rates">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200" x-text="period.rates_count"></span>
                                </template>
                                <template x-if="!period.has_custom_rates">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Default</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <template x-if="period.is_closed">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-lock mr-1 text-xs"></i> Closed
                                    </span>
                                </template>
                                <template x-if="!period.is_closed">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                        <i class="fas fa-unlock mr-1 text-xs"></i> Open
                                    </span>
                                </template>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="viewPeriod(period)" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <template x-if="period.can_edit">
                                        <button @click="openEditModal(period)" class="p-2 text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-200 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors" title="Edit Period">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </template>
                                    <template x-if="!period.is_closed">
                                        <button @click="confirmClosePeriod(period)" class="p-2 text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-200 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-colors" title="Close Period">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    </template>
                                    <template x-if="period.can_reopen">
                                        <button @click="confirmOpenPeriod(period)" class="p-2 text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-200 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors" title="Reopen Period">
                                            <i class="fas fa-unlock"></i>
                                        </button>
                                    </template>
                                    <template x-if="period.can_delete">
                                        <button @click="confirmDeletePeriod(period)" class="p-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-200 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Delete Period">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Period Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @keydown.escape.window="showCreateModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full mx-4" @click.outside="showCreateModal = false">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-plus text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Create New Period</h3>
                </div>
                <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form @submit.prevent="createPeriod()" class="p-6">
                <div class="space-y-4">
                    <!-- Auto-generate Toggle -->
                    <div class="flex items-center gap-2 mb-4">
                        <input type="checkbox" id="autoGenerate" x-model="createForm.autoGenerate" @change="handleAutoGenerate()" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        <label for="autoGenerate" class="text-sm text-gray-700 dark:text-gray-300">Auto-generate from start date</label>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" x-model="createForm.start_date" @change="handleAutoGenerate()" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date <span class="text-red-500">*</span></label>
                        <input type="date" x-model="createForm.end_date" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Period Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Period Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="createForm.per_name" required placeholder="e.g., January 2026" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Period Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Period Code <span class="text-red-500">*</span></label>
                        <input type="text" x-model="createForm.per_code" required placeholder="e.g., 202601" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Grace Period -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Grace Period (days)</label>
                        <input type="number" x-model="createForm.grace_period" min="0" max="365" placeholder="10" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Days after end date before penalties apply</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                    <button type="submit" :disabled="saving" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg transition-colors">
                        <i x-show="saving" class="fas fa-spinner fa-spin mr-2"></i>
                        <span x-text="saving ? 'Creating...' : 'Create Period'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Period Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @keydown.escape.window="showEditModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full mx-4" @click.outside="showEditModal = false">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-edit text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Period</h3>
                </div>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form @submit.prevent="updatePeriod()" class="p-6">
                <div class="space-y-4">
                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" x-model="editForm.start_date" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date <span class="text-red-500">*</span></label>
                        <input type="date" x-model="editForm.end_date" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Period Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Period Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="editForm.per_name" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Period Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Period Code <span class="text-red-500">*</span></label>
                        <input type="text" x-model="editForm.per_code" required class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Grace Period -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Grace Period (days)</label>
                        <input type="number" x-model="editForm.grace_period" min="0" max="365" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Days after end date before penalties apply</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                    <button type="submit" :disabled="saving" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 disabled:bg-amber-400 text-white rounded-lg transition-colors">
                        <i x-show="saving" class="fas fa-spinner fa-spin mr-2"></i>
                        <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Period Modal -->
    <div x-show="showViewModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @keydown.escape.window="showViewModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full mx-4" @click.outside="showViewModal = false">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="viewPeriodData?.per_name || 'Period Details'"></h3>
                </div>
                <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Period Code</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="viewPeriodData?.per_code"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Start Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="viewPeriodData?.start_date"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">End Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="viewPeriodData?.end_date"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Grace Period</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white"><span x-text="viewPeriodData?.grace_period || 10"></span> days</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Bills</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="viewPeriodData?.bills_count || 0"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Meter Readings</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="viewPeriodData?.readings_count || 0"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Custom Rates</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="viewPeriodData?.has_custom_rates ? viewPeriodData.rates_count : 'Using Default'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                        <template x-if="viewPeriodData?.is_closed">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-lock mr-1"></i> Closed
                            </span>
                        </template>
                        <template x-if="!viewPeriodData?.is_closed">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                <i class="fas fa-unlock mr-1"></i> Open
                            </span>
                        </template>
                    </div>
                    <template x-if="viewPeriodData?.closed_at">
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Closed</p>
                            <p class="text-sm text-gray-900 dark:text-white">
                                <span x-text="viewPeriodData?.closed_at"></span>
                                <template x-if="viewPeriodData?.closed_by_name">
                                    <span class="text-gray-500"> by <span x-text="viewPeriodData?.closed_by_name"></span></span>
                                </template>
                            </p>
                        </div>
                    </template>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <button @click="showViewModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Close</button>
            </div>
        </div>
    </div>

    <!-- Confirm Close Modal -->
    <div x-show="showCloseModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @keydown.escape.window="showCloseModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4" @click.outside="showCloseModal = false">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lock text-2xl text-amber-600 dark:text-amber-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Close Period?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Are you sure you want to close <span class="font-medium" x-text="closePeriodData?.per_name"></span>?
                    <br><br>
                    <span class="text-amber-600 dark:text-amber-400">Closed periods cannot be modified.</span>
                </p>
                <div class="flex justify-center gap-3">
                    <button @click="showCloseModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                    <button @click="closePeriod()" :disabled="saving" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 disabled:bg-amber-400 text-white rounded-lg transition-colors">
                        <i x-show="saving" class="fas fa-spinner fa-spin mr-2"></i>
                        <span x-text="saving ? 'Closing...' : 'Close Period'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Open Modal -->
    <div x-show="showOpenModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @keydown.escape.window="showOpenModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4" @click.outside="showOpenModal = false">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-unlock text-2xl text-green-600 dark:text-green-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Reopen Period?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Are you sure you want to reopen <span class="font-medium" x-text="openPeriodData?.per_name"></span>?
                </p>
                <div class="flex justify-center gap-3">
                    <button @click="showOpenModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                    <button @click="reopenPeriod()" :disabled="saving" class="px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white rounded-lg transition-colors">
                        <i x-show="saving" class="fas fa-spinner fa-spin mr-2"></i>
                        <span x-text="saving ? 'Opening...' : 'Reopen Period'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @keydown.escape.window="showDeleteModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4" @click.outside="showDeleteModal = false">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash text-2xl text-red-600 dark:text-red-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Delete Period?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Are you sure you want to delete <span class="font-medium" x-text="deletePeriodData?.per_name"></span>?
                    <br><br>
                    <span class="text-red-600 dark:text-red-400">This action cannot be undone.</span>
                </p>
                <div class="flex justify-center gap-3">
                    <button @click="showDeleteModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                    <button @click="deletePeriod()" :disabled="saving" class="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white rounded-lg transition-colors">
                        <i x-show="saving" class="fas fa-spinner fa-spin mr-2"></i>
                        <span x-text="saving ? 'Deleting...' : 'Delete Period'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div x-show="notification.show" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-4 right-4 z-50"
    >
        <div :class="{
            'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200': notification.type === 'success',
            'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200': notification.type === 'error',
            'bg-amber-50 dark:bg-amber-900/30 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200': notification.type === 'warning'
        }" class="flex items-center gap-3 p-4 rounded-lg border shadow-lg">
            <i :class="{
                'fas fa-check-circle': notification.type === 'success',
                'fas fa-exclamation-circle': notification.type === 'error',
                'fas fa-exclamation-triangle': notification.type === 'warning'
            }"></i>
            <span class="text-sm font-medium" x-text="notification.message"></span>
            <button @click="notification.show = false" class="ml-2 hover:opacity-70">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

<script>
function periodsData() {
    return {
        loading: true,
        saving: false,
        periods: [],
        filteredPeriods: [],
        stats: { total: 0, open: 0, closed: 0, with_rates: 0 },
        searchQuery: '',

        // Modals
        showCreateModal: false,
        showEditModal: false,
        showViewModal: false,
        showCloseModal: false,
        showOpenModal: false,
        showDeleteModal: false,

        // Form Data
        createForm: {
            per_name: '',
            per_code: '',
            start_date: '',
            end_date: '',
            grace_period: 10,
            autoGenerate: true
        },
        editForm: {
            per_id: null,
            per_name: '',
            per_code: '',
            start_date: '',
            end_date: '',
            grace_period: 10
        },
        viewPeriodData: null,
        closePeriodData: null,
        openPeriodData: null,
        deletePeriodData: null,

        // Notification
        notification: {
            show: false,
            type: 'success',
            message: ''
        },

        init() {
            this.fetchPeriods();
        },

        async fetchPeriods() {
            this.loading = true;
            try {
                const response = await fetch('/periods/list');
                const data = await response.json();
                this.periods = data.periods;
                this.stats = data.stats;
                this.filterPeriods();
            } catch (error) {
                console.error('Error fetching periods:', error);
                this.showNotification('error', 'Failed to load periods');
            } finally {
                this.loading = false;
            }
        },

        filterPeriods() {
            if (!this.searchQuery) {
                this.filteredPeriods = this.periods;
            } else {
                const query = this.searchQuery.toLowerCase();
                this.filteredPeriods = this.periods.filter(p =>
                    p.per_name.toLowerCase().includes(query) ||
                    p.per_code.toLowerCase().includes(query)
                );
            }
        },

        openCreateModal() {
            this.createForm = {
                per_name: '',
                per_code: '',
                start_date: '',
                end_date: '',
                grace_period: 10,
                autoGenerate: true
            };
            this.showCreateModal = true;
        },

        handleAutoGenerate() {
            if (this.createForm.autoGenerate && this.createForm.start_date) {
                const date = new Date(this.createForm.start_date);
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                this.createForm.per_name = monthNames[date.getMonth()] + ' ' + date.getFullYear();
                this.createForm.per_code = date.getFullYear().toString() + String(date.getMonth() + 1).padStart(2, '0');

                // Set end date to end of month
                const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                this.createForm.end_date = lastDay.toISOString().split('T')[0];
            }
        },

        async createPeriod() {
            this.saving = true;
            try {
                const response = await fetch('/periods', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.createForm)
                });

                const data = await response.json();

                if (response.ok) {
                    this.showNotification('success', data.message);
                    this.showCreateModal = false;
                    this.fetchPeriods();
                } else {
                    this.showNotification('error', data.message || 'Failed to create period');
                }
            } catch (error) {
                console.error('Error creating period:', error);
                this.showNotification('error', 'An error occurred while creating the period');
            } finally {
                this.saving = false;
            }
        },

        openEditModal(period) {
            this.editForm = {
                per_id: period.per_id,
                per_name: period.per_name,
                per_code: period.per_code,
                start_date: period.start_date,
                end_date: period.end_date,
                grace_period: period.grace_period || 10
            };
            this.showEditModal = true;
        },

        async updatePeriod() {
            this.saving = true;
            try {
                const response = await fetch(`/periods/${this.editForm.per_id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.editForm)
                });

                const data = await response.json();

                if (response.ok) {
                    this.showNotification('success', data.message);
                    this.showEditModal = false;
                    this.fetchPeriods();
                } else {
                    this.showNotification('error', data.message || 'Failed to update period');
                }
            } catch (error) {
                console.error('Error updating period:', error);
                this.showNotification('error', 'An error occurred while updating the period');
            } finally {
                this.saving = false;
            }
        },

        viewPeriod(period) {
            this.viewPeriodData = period;
            this.showViewModal = true;
        },

        confirmClosePeriod(period) {
            this.closePeriodData = period;
            this.showCloseModal = true;
        },

        async closePeriod() {
            this.saving = true;
            try {
                const response = await fetch(`/periods/${this.closePeriodData.per_id}/close`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.showNotification('success', data.message);
                    this.showCloseModal = false;
                    this.fetchPeriods();
                } else {
                    this.showNotification('error', data.message || 'Failed to close period');
                }
            } catch (error) {
                console.error('Error closing period:', error);
                this.showNotification('error', 'An error occurred while closing the period');
            } finally {
                this.saving = false;
            }
        },

        confirmOpenPeriod(period) {
            this.openPeriodData = period;
            this.showOpenModal = true;
        },

        async reopenPeriod() {
            this.saving = true;
            try {
                const response = await fetch(`/periods/${this.openPeriodData.per_id}/open`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.showNotification('success', data.message);
                    this.showOpenModal = false;
                    this.fetchPeriods();
                } else {
                    this.showNotification('error', data.message || 'Failed to reopen period');
                }
            } catch (error) {
                console.error('Error reopening period:', error);
                this.showNotification('error', 'An error occurred while reopening the period');
            } finally {
                this.saving = false;
            }
        },

        confirmDeletePeriod(period) {
            this.deletePeriodData = period;
            this.showDeleteModal = true;
        },

        async deletePeriod() {
            this.saving = true;
            try {
                const response = await fetch(`/periods/${this.deletePeriodData.per_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.showNotification('success', data.message);
                    this.showDeleteModal = false;
                    this.fetchPeriods();
                } else {
                    this.showNotification('error', data.message || 'Failed to delete period');
                }
            } catch (error) {
                console.error('Error deleting period:', error);
                this.showNotification('error', 'An error occurred while deleting the period');
            } finally {
                this.saving = false;
            }
        },

        showNotification(type, message) {
            this.notification = { show: true, type, message };
            setTimeout(() => {
                this.notification.show = false;
            }, 5000);
        }
    };
}
</script>
