<x-app-layout>
    <div x-data="accountTypeManager()" class="p-6">
        <!-- Header -->
        <x-ui.admin.config.shared.page-header
            title="Manage Account Types"
            subtitle="Configure customer account types for billing (Residential, Commercial, etc.)"
            :can-create="true"
            create-label="Add Account Type"
            @create="openCreateModal()"
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
                    placeholder="Search account types..."
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
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <!-- Table -->
        <div x-show="!loading">
            <x-ui.admin.config.account-type.table />
        </div>

        <!-- Pagination -->
        <x-ui.admin.config.shared.pagination />

        <!-- Modals -->
        <x-ui.admin.config.account-type.modals.create-account-type />
        <x-ui.admin.config.account-type.modals.edit-account-type />
        <x-ui.admin.config.account-type.modals.view-account-type />
        <x-ui.admin.config.account-type.modals.delete-account-type />
    </div>
</x-app-layout>
