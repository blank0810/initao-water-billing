<x-app-layout>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="p-6">
        <!-- Page Header -->
        <x-ui.page-header
            title="Consumer List"
            subtitle="Manage consumer documents and verification status"
            icon="fas fa-file-alt" />

        <!-- Main Content -->
        <div class="mt-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
                <x-ui.stat-card
                    title="Total Consumers"
                    value="15"
                    icon="fas fa-users"
                    color="blue" />
                <x-ui.stat-card
                    title="Active Meters"
                    value="12"
                    icon="fas fa-tachometer-alt"
                    color="green" />
                <x-ui.stat-card
                    title="Pending Bills"
                    value="8"
                    icon="fas fa-file-invoice"
                    color="yellow" />
                <x-ui.stat-card
                    title="Total Revenue"
                    value="₱45,200"
                    icon="fas fa-money-bill"
                    color="purple" />
                <x-ui.stat-card
                    title="Overdue"
                    value="3"
                    icon="fas fa-exclamation-triangle"
                    color="red" />
            </div>

            <!-- Standardized Toolbar -->
            <x-ui.standard-toolbar 
                searchId="consumer-search"
                searchPlaceholder="Search consumers...">
                <x-slot name="filters">
                    <select id="verification-filter" onchange="filterConsumers()" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Pending">Pending</option>
                        <option value="Overdue">Overdue</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </x-slot>
            </x-ui.standard-toolbar>

            <!-- Consumer Documents Table -->
            <div class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="consumer-documents-table">
                    <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumer</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Location</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Meter No</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Due</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="consumer-documents-tbody">
                        <!-- Table rows will be populated by JavaScript -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td colspan="6" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4 flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Show</span>
                    <select id="consumerPageSize" onchange="consumerPagination.updatePageSize(this.value)" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-700 dark:text-gray-300">entries</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <button id="consumerPrevBtn" onclick="consumerPagination.prevPage()" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-chevron-left mr-1"></i>Previous
                    </button>
                    <div class="text-sm text-gray-700 dark:text-gray-300 px-3">
                        Page <span id="consumerCurrentPage">1</span> of <span id="consumerTotalPages">1</span>
                    </div>
                    <button id="consumerNextBtn" onclick="consumerPagination.nextPage()" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        Next<i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>
                
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <span id="consumerTotalRecords">0</span> results
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document View Modal -->
<x-ui.consumer.modals.view-document />

<!-- Document Verification Modal -->
<x-ui.consumer.modals.verify-document />


    @vite('resources/js/data/consumer/consumer.js')
</x-app-layout>
