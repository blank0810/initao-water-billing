<div x-data="periodRatesData()" x-init="init()" class="space-y-6">
    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            <!-- Period Selector -->
            <div class="w-full sm:w-64">
                <select x-model="selectedPeriodId" @change="loadRates()"
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="default">Default Rates (Base)</option>
                    <template x-for="period in periods" :key="period.per_id">
                        <option :value="period.per_id"
                            :selected="period.per_id == selectedPeriodId"
                            x-text="period.per_name + (period.is_closed ? ' (Closed)' : '') + (period.is_active ? ' (Current)' : '')"></option>
                    </template>
                </select>
            </div>
            <!-- Class Filter -->
            <div class="w-full sm:w-48">
                <select x-model="selectedClassId" @change="filterRates()"
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Classes</option>
                    <template x-for="(name, id) in accountTypes" :key="id">
                        <option :value="id" :selected="id == selectedClassId" x-text="name"></option>
                    </template>
                </select>
            </div>
        </div>

        <div class="flex gap-2">
            <template x-if="selectedPeriodId !== 'default' && !hasCustomRates">
                <button @click="copySource = 'default'; applyIncrease = false; increasePercent = 5; showCreateModal = true"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-copy"></i>
                    <span>Copy Rates from Period</span>
                </button>
            </template>
            <template x-if="!selectedPeriodClosed">
                <button @click="openAddRateModal()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-plus"></i>
                    <span>Add Rate Tier</span>
                </button>
            </template>
            <button @click="showUploadModal = true"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-upload"></i>
                <span>Upload CSV</span>
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div x-show="selectedPeriodId === 'default' || hasCustomRates" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Selected Period</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-white mt-1" x-text="selectedPeriodName"></div>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Total Rate Tiers</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-white mt-1" x-text="rates.length + ' Tiers'"></div>
                </div>
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                    <i class="fas fa-layer-group text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Rate Source</div>
                    <div class="mt-1">
                        <span x-show="!hasCustomRates" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            <i class="fas fa-database mr-1"></i> Default
                        </span>
                        <span x-show="hasCustomRates" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                            <i class="fas fa-edit mr-1"></i> Custom
                        </span>
                    </div>
                </div>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Classes</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-white mt-1" x-text="Object.keys(accountTypes).length + ' Types'"></div>
                </div>
                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- No Rates Informational Card -->
    <template x-if="selectedPeriodId !== 'default' && !hasCustomRates">
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-8 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/40 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-2xl text-amber-500 dark:text-amber-400"></i>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Rates Available</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                This period has no rate tiers configured yet. You can set up rates by duplicating from an existing period or by adding individual rate tiers.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button @click="copySource = 'default'; applyIncrease = false; increasePercent = 5; showCreateModal = true"
                    class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 text-sm font-medium">
                    <i class="fas fa-copy"></i>
                    <span>Duplicate Rates from Period</span>
                </button>
                <button @click="openAddRateModal()"
                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center justify-center gap-2 text-sm font-medium">
                    <i class="fas fa-plus"></i>
                    <span>Add Rate Tier Manually</span>
                </button>
            </div>
        </div>
    </template>

    <!-- Rates Table -->
    <div x-show="selectedPeriodId === 'default' || hasCustomRates" class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                Water Rate Tiers
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Class</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tier</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Range (cu.m)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Base Rate</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Rate/cu.m</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="rate in filteredRates" :key="rate.wr_id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                                    :class="getClassBadgeColor(rate.class_id)">
                                    <span x-text="rate.class_name"></span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-600 text-sm font-bold text-gray-700 dark:text-gray-200"
                                    x-text="rate.range_id"></span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-white">
                                <span x-text="rate.range_min + ' - ' + (rate.range_max >= 999 ? '∞' : rate.range_max)"></span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white">
                                <span x-text="'₱ ' + parseFloat(rate.rate_val).toFixed(2)"></span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                <span x-text="parseFloat(rate.rate_inc) > 0 ? '₱ ' + parseFloat(rate.rate_inc).toFixed(2) : '-'"></span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span x-show="rate.period_id === null" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300">
                                    Default
                                </span>
                                <span x-show="rate.period_id !== null" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                                    Custom
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <template x-if="selectedPeriodId !== 'default' && !hasCustomRates">
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">Create period rates first</span>
                                </template>
                                <template x-if="selectedPeriodId === 'default' || hasCustomRates">
                                    <button @click="editRate(rate)"
                                        :disabled="selectedPeriodClosed"
                                        class="px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredRates.length === 0">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>No rates found</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Period Rates Modal -->
    <div x-show="showCreateModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="showCreateModal = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-auto z-10">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create Period Rates</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Copy rates to <strong x-text="selectedPeriodName"></strong>
                    </p>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <!-- Source Selector -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Copy rates from</label>
                        <select x-model="copySource"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="default">Default Rates (Base)</option>
                            <template x-for="period in periodsWithRates" :key="period.per_id">
                                <option :value="period.per_id" x-text="period.per_name + ' (' + period.rate_count + ' tiers)'"></option>
                            </template>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Select the source rates to copy from
                        </p>
                    </div>

                    <!-- Percentage Adjustment -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="applyIncrease" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Apply percentage adjustment</span>
                        </label>
                    </div>

                    <div x-show="applyIncrease" x-transition>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Percentage (%)</label>
                        <input type="number" x-model="increasePercent" step="0.1"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                            placeholder="e.g., 5 for 5% increase, -3 for 3% decrease">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Use negative values for decrease</p>
                    </div>

                    <!-- Info Box -->
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            <i class="fas fa-info-circle mr-1"></i>
                            This will create a full copy of rates for this period, which you can then customize individually.
                        </p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                    <button @click="showCreateModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        Cancel
                    </button>
                    <button @click="createPeriodRates()" :disabled="creating"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition disabled:opacity-50">
                        <span x-show="!creating">Create Rates</span>
                        <span x-show="creating"><i class="fas fa-spinner fa-spin mr-1"></i>Creating...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Rate Modal -->
    <div x-show="showEditModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="showEditModal = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-auto z-10">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Rate Tier</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="editingRate.class_name + ' - Tier ' + editingRate.range_id"></p>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Range Min (cu.m)</label>
                            <input type="number" x-model="editingRate.range_min" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Range Max (cu.m)</label>
                            <input type="number" x-model="editingRate.range_max" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Base Rate (₱)</label>
                            <input type="number" x-model="editingRate.rate_val" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rate per cu.m (₱)</label>
                            <input type="number" x-model="editingRate.rate_inc" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Base Rate:</strong> Fixed charge for this tier.<br>
                            <strong>Rate per cu.m:</strong> Additional charge per cubic meter above the minimum.
                        </p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                    <button @click="showEditModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        Cancel
                    </button>
                    <button @click="saveRate()" :disabled="saving"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition disabled:opacity-50">
                        <span x-show="!saving">Save Changes</span>
                        <span x-show="saving"><i class="fas fa-spinner fa-spin mr-1"></i>Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload CSV Modal -->
    <div x-show="showUploadModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="showUploadModal = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-auto z-10">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Water Rates</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Period</label>
                        <select x-model="uploadPeriodId"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="default">Default Rates (Base)</option>
                            <template x-for="period in periods" :key="period.per_id">
                                <option :value="period.per_id" x-text="period.per_name"></option>
                            </template>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CSV File</label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer"
                            @click="$refs.fileInput.click()"
                            @dragover.prevent="dragover = true"
                            @dragleave.prevent="dragover = false"
                            @drop.prevent="handleFileDrop($event)"
                            :class="{'border-blue-500 bg-blue-50 dark:bg-blue-900/20': dragover}">
                            <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept=".csv" class="hidden">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 dark:text-gray-500 mb-2"></i>
                            <p class="text-sm text-gray-600 dark:text-gray-400" x-show="!uploadFile">
                                Drag & drop CSV file here or click to browse
                            </p>
                            <p class="text-sm text-blue-600 dark:text-blue-400" x-show="uploadFile" x-text="uploadFile?.name"></p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <a href="{{ route('rate.template') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            <i class="fas fa-download mr-1"></i>Download CSV Template
                        </a>
                    </div>

                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mb-1">Required CSV columns:</p>
                        <ul class="text-xs text-gray-500 dark:text-gray-400 list-disc list-inside">
                            <li><code>class_id</code> - Account type ID</li>
                            <li><code>range_id</code> - Tier level (1, 2, 3, 4)</li>
                            <li><code>range_min</code> - Minimum consumption</li>
                            <li><code>range_max</code> - Maximum consumption</li>
                            <li><code>rate_val</code> - Base rate value</li>
                            <li><code>rate_inc</code> - Rate increment per cu.m</li>
                        </ul>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                    <button @click="showUploadModal = false; uploadFile = null" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        Cancel
                    </button>
                    <button @click="uploadRates()" :disabled="uploading || !uploadFile"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition disabled:opacity-50">
                        <span x-show="!uploading">Upload & Apply</span>
                        <span x-show="uploading"><i class="fas fa-spinner fa-spin mr-1"></i>Uploading...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Rate Tier Modal -->
    <div x-show="showAddRateModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="showAddRateModal = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-auto z-10">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Rate Tier</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Adding to: <strong x-text="selectedPeriodId === 'default' ? 'Default Rates' : selectedPeriodName"></strong>
                    </p>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Type</label>
                            <select x-model="newRate.class_id" @change="autoSetTierNumber()"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Account Type</option>
                                <template x-for="(name, id) in accountTypes" :key="id">
                                    <option :value="id" x-text="name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tier Number</label>
                            <input type="number" x-model="newRate.range_id" min="1" step="1" readonly
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Auto-assigned based on account type</p>
                        </div>
                        <div></div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Range Min (cu.m)</label>
                            <input type="number" x-model="newRate.range_min" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Range Max (cu.m)</label>
                            <input type="number" x-model="newRate.range_max" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                placeholder="10">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Base Rate (₱)</label>
                            <input type="number" x-model="newRate.rate_val" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                placeholder="150.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rate per cu.m (₱)</label>
                            <input type="number" x-model="newRate.rate_inc" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                                placeholder="15.00">
                        </div>
                    </div>

                    <template x-if="addRateError">
                        <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-lg">
                            <p class="text-xs text-red-700 dark:text-red-300">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <span x-text="addRateError"></span>
                            </p>
                        </div>
                    </template>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                    <button @click="showAddRateModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        Cancel
                    </button>
                    <button @click="addRateTier()" :disabled="addingRate"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition disabled:opacity-50">
                        <span x-show="!addingRate">Add Rate Tier</span>
                        <span x-show="addingRate"><i class="fas fa-spinner fa-spin mr-1"></i>Adding...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function periodRatesData() {
    return {
        periods: [],
        rates: [],
        accountTypes: {},
        selectedPeriodId: null, // Will be set to active period on init
        selectedClassId: null, // Will be set to Residential on init
        hasCustomRates: false,
        activePeriodId: null,
        defaultAccountTypeId: null,
        initialized: false,

        // Modals
        showCreateModal: false,
        showEditModal: false,
        showUploadModal: false,
        showAddRateModal: false,

        // Create modal
        copySource: 'default',
        applyIncrease: false,
        increasePercent: 5,
        creating: false,

        // Edit modal
        editingRate: {},
        saving: false,

        // Add rate modal
        newRate: {},
        addingRate: false,
        addRateError: '',

        // Upload modal
        uploadPeriodId: 'default',
        uploadFile: null,
        uploading: false,
        dragover: false,

        get selectedPeriodName() {
            if (this.selectedPeriodId === 'default' || this.selectedPeriodId === null) return 'Default Rates';
            const period = this.periods.find(p => p.per_id == this.selectedPeriodId);
            return period ? period.per_name : 'Unknown Period';
        },

        get selectedPeriodClosed() {
            if (this.selectedPeriodId === 'default' || this.selectedPeriodId === null) return false;
            const period = this.periods.find(p => p.per_id == this.selectedPeriodId);
            return period ? period.is_closed : false;
        },

        get periodsWithRates() {
            return this.periods.filter(p => p.has_custom_rates && p.per_id != this.selectedPeriodId);
        },

        get filteredRates() {
            if (this.selectedClassId === 'all' || this.selectedClassId === null) {
                return this.rates;
            }
            return this.rates.filter(r => r.class_id == this.selectedClassId);
        },

        async init() {
            await this.loadPeriods();
            await this.loadRates();
            this.initialized = true;
        },

        async loadPeriods() {
            try {
                const response = await fetch('/rate/periods');
                const data = await response.json();
                this.periods = data.periods || [];
                this.activePeriodId = data.activePeriodId || null;

                // Set the default selected period to the active period on initial load
                if (!this.initialized && this.activePeriodId) {
                    this.selectedPeriodId = this.activePeriodId;
                } else if (!this.initialized) {
                    this.selectedPeriodId = 'default';
                }
            } catch (error) {
                console.error('Failed to load periods:', error);
                if (!this.initialized) {
                    this.selectedPeriodId = 'default';
                }
            }
        },

        async loadRates() {
            try {
                const periodParam = (this.selectedPeriodId === 'default' || this.selectedPeriodId === null) ? 'default' : this.selectedPeriodId;
                const response = await fetch(`/rate/periods/${periodParam}/rates`);
                const data = await response.json();
                this.rates = data.rates || [];
                this.hasCustomRates = data.hasCustomRates || false;
                this.accountTypes = data.accountTypes || {};
                this.defaultAccountTypeId = data.defaultAccountTypeId || null;

                // Set the default selected class to All Classes on initial load
                if (!this.initialized) {
                    this.selectedClassId = 'all';
                }
            } catch (error) {
                console.error('Failed to load rates:', error);
                if (!this.initialized) {
                    this.selectedClassId = 'all';
                }
            }
        },

        filterRates() {
            // Trigger reactivity - filteredRates is a computed property
        },

        getClassBadgeColor(classId) {
            const colors = {
                1: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                2: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                3: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                4: 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
                5: 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
                6: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            };
            return colors[classId] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
        },

        editRate(rate) {
            this.editingRate = { ...rate };
            this.showEditModal = true;
        },

        async saveRate() {
            this.saving = true;
            try {
                const response = await fetch(`/rate/rates/${this.editingRate.wr_id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        rate_val: this.editingRate.rate_val,
                        rate_inc: this.editingRate.rate_inc,
                        range_min: this.editingRate.range_min,
                        range_max: this.editingRate.range_max,
                        period_id: this.selectedPeriodId === 'default' ? null : this.selectedPeriodId
                    })
                });

                if (response.ok) {
                    this.showEditModal = false;
                    await this.loadRates();
                    this.showNotification('Rate updated successfully', 'success');
                } else {
                    const error = await response.json();
                    this.showNotification(error.message || 'Failed to update rate', 'error');
                }
            } catch (error) {
                console.error('Failed to save rate:', error);
                this.showNotification('Failed to update rate', 'error');
            }
            this.saving = false;
        },

        async createPeriodRates() {
            this.creating = true;
            try {
                const payload = {
                    apply_increase: this.applyIncrease,
                    increase_percent: this.increasePercent,
                };
                if (this.copySource !== 'default') {
                    payload.source_period_id = this.copySource;
                }

                const response = await fetch(`/rate/periods/${this.selectedPeriodId}/rates/copy`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    this.showCreateModal = false;
                    await this.loadRates();
                    this.showNotification('Period rates created successfully', 'success');
                } else {
                    const error = await response.json();
                    this.showNotification(error.message || 'Failed to create rates', 'error');
                }
            } catch (error) {
                console.error('Failed to create period rates:', error);
                this.showNotification('Failed to create rates', 'error');
            }
            this.creating = false;
        },

        handleFileSelect(event) {
            this.uploadFile = event.target.files[0];
        },

        handleFileDrop(event) {
            this.dragover = false;
            const files = event.dataTransfer.files;
            if (files.length > 0 && files[0].name.endsWith('.csv')) {
                this.uploadFile = files[0];
            }
        },

        async uploadRates() {
            if (!this.uploadFile) return;

            this.uploading = true;
            try {
                const formData = new FormData();
                formData.append('file', this.uploadFile);
                formData.append('period_id', this.uploadPeriodId === 'default' ? '' : this.uploadPeriodId);

                const response = await fetch('/rate/rates/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                if (response.ok) {
                    const result = await response.json();
                    this.showUploadModal = false;
                    this.uploadFile = null;
                    this.selectedPeriodId = this.uploadPeriodId;
                    await this.loadRates();
                    this.showNotification(`Successfully imported ${result.count} rates`, 'success');
                } else {
                    const error = await response.json();
                    this.showNotification(error.message || 'Failed to upload rates', 'error');
                }
            } catch (error) {
                console.error('Failed to upload rates:', error);
                this.showNotification('Failed to upload rates', 'error');
            }
            this.uploading = false;
        },

        openAddRateModal() {
            this.newRate = {
                class_id: '',
                range_id: '',
                range_min: '',
                range_max: '',
                rate_val: '',
                rate_inc: '',
            };
            this.addRateError = '';
            this.showAddRateModal = true;
        },

        autoSetTierNumber() {
            if (!this.newRate.class_id) {
                this.newRate.range_id = '';
                return;
            }

            if (this.selectedPeriodId !== 'default' && !this.hasCustomRates) {
                this.newRate.range_id = 1;
                return;
            }

            const classRates = this.rates.filter(r => r.class_id == this.newRate.class_id);
            const maxTier = classRates.reduce((max, r) => Math.max(max, parseInt(r.range_id) || 0), 0);
            this.newRate.range_id = maxTier + 1;
        },

        async addRateTier() {
            this.addingRate = true;
            this.addRateError = '';

            try {
                const periodId = (this.selectedPeriodId === 'default' || this.selectedPeriodId === null)
                    ? null
                    : this.selectedPeriodId;

                const response = await fetch('/rate/rates', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        ...this.newRate,
                        period_id: periodId,
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.showAddRateModal = false;
                    await this.loadRates();
                    this.showNotification('Rate tier added successfully', 'success');
                } else {
                    this.addRateError = data.message || 'Failed to add rate tier';
                }
            } catch (error) {
                console.error('Failed to add rate tier:', error);
                this.addRateError = 'Failed to add rate tier';
            }
            this.addingRate = false;
        },

        showNotification(message, type = 'info') {
            if (window.showToast) {
                window.showToast(message, type);
            } else {
                alert(message);
            }
        }
    };
}
</script>
