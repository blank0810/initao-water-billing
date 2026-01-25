<x-app-layout>
    <div x-data="purokManager()" class="p-6">
        <!-- Header -->
        <x-ui.admin.config.shared.page-header
            title="Manage Puroks"
            subtitle="Configure sub-barangay areas for address management"
            :can-create="true"
            create-label="Add Purok"
        />

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-4 mb-6">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Search
                </label>
                <input
                    type="text"
                    x-model="search"
                    @input.debounce.300ms="fetchItems()"
                    placeholder="Search puroks..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                />
            </div>

            <!-- Status Filter -->
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
                </label>
                <select
                    x-model="statusFilter"
                    @change="fetchItems()"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">All Status</option>
                    <option value="2">Active</option>
                    <option value="3">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <!-- Table -->
        <div x-show="!loading">
            <x-ui.admin.config.purok.table />
        </div>

        <!-- Pagination -->
        <x-ui.admin.config.shared.pagination />

        <!-- Modals -->
        <x-ui.admin.config.purok.modals.create-purok />
        <x-ui.admin.config.purok.modals.edit-purok />
        <x-ui.admin.config.purok.modals.view-purok />
        <x-ui.admin.config.purok.modals.delete-purok />

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
