<x-app-layout>
    <div x-data="purokManager()" class="p-6">
        <!-- Header -->
        <x-ui.admin.config.shared.page-header
            title="Manage Puroks"
            subtitle="Configure sub-barangay areas for address management"
            :can-create="true"
            create-label="Add Purok"
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
                    placeholder="Search puroks..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                />
            </div>

            <!-- Barangay Filter -->
            <div class="w-64">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Barangay
                </label>
                <select
                    x-model="barangayFilter"
                    @change="fetchItems()"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">All Barangays</option>
                    <template x-for="barangay in barangays" :key="barangay.b_id">
                        <option :value="barangay.b_id" x-text="barangay.b_desc"></option>
                    </template>
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
            <x-ui.admin.config.purok.table />
        </div>

        <!-- Pagination -->
        <x-ui.admin.config.shared.pagination />

        <!-- Modals -->
        <x-ui.admin.config.purok.modals.create-purok />
        <x-ui.admin.config.purok.modals.edit-purok />
        <x-ui.admin.config.purok.modals.view-purok />
        <x-ui.admin.config.purok.modals.delete-purok />
    </div>
</x-app-layout>
