<x-app-layout>
    {{-- Page Header --}}
    <x-ui.page-header title="Customer List" icon="fas fa-users" />

    {{-- Stats Cards --}}
    <div id="customer-stats" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        {{-- Loading Skeletons - Will be replaced by JavaScript --}}
        <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg h-24"></div>
        <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg h-24"></div>
        <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg h-24"></div>
        <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg h-24"></div>
    </div>

    {{-- Action Functions --}}
    <x-ui.action-functions
        searchPlaceholder="Search customer..."
        filterLabel="All Status"
        :filterOptions="[
            ['value' => 'ACTIVE', 'label' => 'Active'],
            ['value' => 'PENDING', 'label' => 'Pending'],
            ['value' => 'INACTIVE', 'label' => 'Inactive']
        ]"
        :showDateFilter="false"
        :showExport="true"
        tableId="customer-list-tbody"
    />

    {{-- Customer List Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Customer
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Address & Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Meter No
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Current Bill
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="customer-list-tbody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    {{-- Loading State - Will be replaced by JavaScript --}}
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            Loading...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination Controls --}}
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-t border-gray-200 dark:border-gray-600 sm:px-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                {{-- Left: Page Size Selector --}}
                <div class="flex items-center gap-2">
                    <label for="customerPageSize" class="text-sm text-gray-700 dark:text-gray-300">Show:</label>
                    <select id="customerPageSize" class="border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-700 dark:text-gray-300">entries</span>
                </div>

                {{-- Center: Page Counter --}}
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Page <span id="customerCurrentPage">1</span> of <span id="customerTotalPages">1</span>
                </div>

                {{-- Right: Navigation Buttons --}}
                <div class="flex items-center gap-2">
                    <button id="customerPrevBtn"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        Previous
                    </button>
                    <button id="customerNextBtn"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>

            {{-- Total Records Display --}}
            <div class="mt-2 text-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span id="customerTotalRecords">0</span> results
                </span>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    @vite(['resources/js/data/customer/customer-list-simple.js'])
</x-app-layout>
