<x-app-layout>
    <div x-data="areaManager()" class="p-6">
        <!-- Page Header -->
        <x-ui.admin.config.shared.page-header
            title="Manage Areas"
            subtitle="Create and manage service areas in Initao"
            :can-create="true"
            create-label="Add Area"
        />

        <!-- Search & Filters -->
        <x-ui.admin.config.shared.search-filter
            :statuses="[
                ['value' => '', 'label' => 'All Statuses'],
                ['value' => '2', 'label' => 'Active'],
                ['value' => '3', 'label' => 'Inactive']
            ]"
            placeholder="Search by area name..."
        />

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
            <p class="mt-4 text-gray-500">Loading areas...</p>
        </div>

        <!-- Table -->
        <div x-show="!loading" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <x-ui.admin.config.area.table />
        </div>

        <!-- Pagination -->
        <div x-show="!loading && pagination.lastPage > 1" class="mt-4 flex justify-center">
            <nav class="flex items-center gap-2">
                <button
                    @click="goToPage(pagination.currentPage - 1)"
                    :disabled="pagination.currentPage === 1"
                    class="px-3 py-2 rounded-lg border disabled:opacity-50"
                >
                    Previous
                </button>

                <template x-for="page in Array.from({length: pagination.lastPage}, (_, i) => i + 1)" :key="page">
                    <button
                        @click="goToPage(page)"
                        :class="page === pagination.currentPage ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
                        class="px-3 py-2 rounded-lg border"
                        x-text="page"
                    ></button>
                </template>

                <button
                    @click="goToPage(pagination.currentPage + 1)"
                    :disabled="pagination.currentPage === pagination.lastPage"
                    class="px-3 py-2 rounded-lg border disabled:opacity-50"
                >
                    Next
                </button>
            </nav>
        </div>

        <!-- Modals -->
        <x-ui.admin.config.area.modals.create-area />
        <x-ui.admin.config.area.modals.edit-area />
        <x-ui.admin.config.area.modals.view-area />
        <x-ui.admin.config.area.modals.delete-area />

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
