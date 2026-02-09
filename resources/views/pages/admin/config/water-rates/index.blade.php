<x-app-layout>
    <div x-data="waterRateManager()" class="p-6">
        <!-- Page Header -->
        <x-ui.admin.config.shared.page-header
            title="Manage Water Rates"
            subtitle="Configure tiered water rate pricing by account type"
            :can-create="true"
            create-label="Add Rate Tier"
        />

        <!-- Filters -->
        <div class="flex items-center gap-4 mb-4">
            <!-- Period Filter -->
            <div class="w-64">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Period
                </label>
                <select
                    x-model="periodFilter"
                    @change="fetchItems()"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">Default Rates</option>
                    <template x-for="period in periods" :key="period.per_id">
                        <option :value="period.per_id" x-text="period.per_name"></option>
                    </template>
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
            <p class="mt-4 text-gray-500">Loading water rates...</p>
        </div>

        <!-- Rates by Account Type -->
        <div x-show="!loading" class="space-y-6">
            <template x-for="(tiers, accountType) in items" :key="accountType">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="accountType"></h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Tier</th>
                                    <th scope="col" class="px-6 py-3">Range (m³)</th>
                                    <th scope="col" class="px-6 py-3">Base Rate</th>
                                    <th scope="col" class="px-6 py-3">Increment Rate</th>
                                    <th scope="col" class="px-6 py-3 text-center">Status</th>
                                    <th scope="col" class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="rate in tiers" :key="rate.wr_id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white" x-text="rate.range_id"></td>
                                        <td class="px-6 py-4" x-text="rate.range_min + ' - ' + rate.range_max"></td>
                                        <td class="px-6 py-4" x-text="'₱' + parseFloat(rate.rate_val).toFixed(2)"></td>
                                        <td class="px-6 py-4" x-text="'₱' + parseFloat(rate.rate_inc).toFixed(2)"></td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                x-bind:class="rate.stat_id == 2 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                                class="px-2 py-1 text-xs font-medium rounded-full"
                                                x-text="rate.stat_id == 2 ? 'ACTIVE' : 'INACTIVE'"
                                            ></span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button @click="openViewModal(rate)" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="View Details">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </button>
                                                <button @click="openEditModal(rate)" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors" title="Edit">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </button>
                                                <button @click="openDeleteModal(rate)" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="tiers.length === 0">
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-inbox text-3xl mb-2"></i>
                                            <p>No rate tiers configured</p>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <template x-if="Object.keys(items).length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No water rates configured</p>
                </div>
            </template>
        </div>

        <!-- Modals -->
        <x-ui.admin.config.water-rate.modals.create-rate />
        <x-ui.admin.config.water-rate.modals.edit-rate />
        <x-ui.admin.config.water-rate.modals.view-rate />
        <x-ui.admin.config.water-rate.modals.delete-rate />

        <!-- Success Notification -->
        <div x-show="showSuccess"
             x-transition
             class="fixed top-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800" x-text="successMessage"></p>
            </div>
        </div>

        <!-- Error Notification -->
        <div x-show="showError"
             x-transition
             class="fixed top-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-800" x-text="errorMessage"></p>
            </div>
        </div>
    </div>
</x-app-layout>
