<x-app-layout>
    <div x-data="chargeItemManager()" class="p-6">
        <!-- Header -->
        <x-ui.admin.config.shared.page-header
            title="Manage Application Fee Templates"
            subtitle="Configure charge items for service applications and billing"
            :can-create="true"
            create-label="Add Charge Item"
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
                    placeholder="Search charge items..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                />
            </div>

            <!-- Charge Type Filter -->
            <div class="w-64">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Charge Type
                </label>
                <select
                    x-model="chargeTypeFilter"
                    @change="fetchItems()"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">All Types</option>
                    <option value="one_time">One Time</option>
                    <option value="recurring">Recurring</option>
                    <option value="per_unit">Per Unit</option>
                </select>
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
            <x-ui.admin.config.charge-item.table />
        </div>

        <!-- Pagination -->
        <x-ui.admin.config.shared.pagination />

        <!-- Modals -->
        <x-ui.admin.config.charge-item.modals.create-charge-item />
        <x-ui.admin.config.charge-item.modals.edit-charge-item />
        <x-ui.admin.config.charge-item.modals.view-charge-item />
        <x-ui.admin.config.charge-item.modals.delete-charge-item />
    </div>
</x-app-layout>
