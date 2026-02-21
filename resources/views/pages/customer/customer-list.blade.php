<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <x-ui.page-header title="Customer List" icon="fas fa-users" />

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div id="stat-total">
                    <x-ui.stat-card
                        title="Total Customers"
                        value="0"
                        icon="user" />
                </div>
                <div id="stat-residential">
                    <x-ui.stat-card
                        title="Residential Type"
                        value="0"
                        icon="home" />
                </div>
                <div id="stat-bill">
                    <x-ui.stat-card
                        title="Total Current Bill"
                        value="₱0.00"
                        icon="file-invoice-dollar" />
                </div>
                <div id="stat-overdue">
                    <x-ui.stat-card
                        title="Overdue"
                        value="0"
                        icon="exclamation-triangle" />
                </div>
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
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Address & Type
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Meter No
                                </th>
                                <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Current Bill
                                </th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700" id="customerTableBody">
                            {{-- Loading State - Will be replaced by JavaScript --}}
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Loading...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>



            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4 flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
                    <select id="customerPageSize" onchange="customerPagination.updatePageSize(this.value)" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
                </div>

                <div class="flex items-center gap-2">
                    <button id="customerPrevBtn" onclick="customerPagination.prevPage()" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                        <i class="fas fa-chevron-left mr-1"></i>Previous
                    </button>
                    <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
                        Page <span id="customerCurrentPage">1</span> of <span id="customerTotalPages">1</span>
                    </div>
                    <button id="customerNextBtn" onclick="customerPagination.nextPage()" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                        Next<i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-white" id="customerTotalRecords">0</span> results
                </div>
            </div>

        </div>
    </div>

    {{-- Edit Customer Modal --}}
    <x-ui.customer.modals.edit-customer />

    {{-- JavaScript --}}
    @vite(['resources/js/data/customer/customer-list-simple.js'])
</x-app-layout>
