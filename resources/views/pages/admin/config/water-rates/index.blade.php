<x-app-layout>
    <div x-data="waterRateManager()" class="p-6">
        <!-- Page Header -->
        <x-ui.admin.config.shared.page-header
            title="Manage Water Rates"
            subtitle="Configure tiered water rate pricing by account type"
            :can-create="true"
            create-label="Add Rate Tier"
            @create="openCreateModal()"
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
                    <!-- Periods will be loaded dynamically -->
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
                    <x-ui.admin.config.water-rate.table :account-type="accountType" />
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
