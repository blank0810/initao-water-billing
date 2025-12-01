<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <x-ui.page-header
                title="Invoice Management"
                subtitle="View and manage all customer invoices"
                icon="fas fa-file-invoice">
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('customer.list') }}">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Customers
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Tab Navigation -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <a href="{{ route('approve.customer') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                            <i class="fas fa-check-circle mr-2"></i>Approval Queue
                        </a>
                        <a href="{{ route('invoice.list') }}" class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                            <i class="fas fa-file-invoice mr-2"></i>Invoice List
                        </a>
                        <a href="{{ route('declined.customer') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                            <i class="fas fa-times-circle mr-2"></i>Declined Customer
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-6 flex gap-4">
                <input type="text" id="searchInput" placeholder="Search invoices..."
                    class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <select id="statusFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    <option value="ALL">All Invoices</option>
                    <option value="PENDING">Pending Payments</option>
                    <option value="PAID">Paid</option>
                </select>
            </div>

            <!-- Invoice Table -->
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700" onclick="window.invoiceManager?.sortBy('invoice_id')">
                                    Invoice ID <i class="fas fa-sort ml-1"></i>
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700" onclick="window.invoiceManager?.sortBy('customer_name')">
                                    Customer Info <i class="fas fa-sort ml-1"></i>
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700" onclick="window.invoiceManager?.sortBy('invoice_date')">
                                    Invoice Date <i class="fas fa-sort ml-1"></i>
                                </th>
                                <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700" onclick="window.invoiceManager?.sortBy('due_date')">
                                    Due Date <i class="fas fa-sort ml-1"></i>
                                </th>
                                <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700" onclick="window.invoiceManager?.sortBy('total_amount')">
                                    Amount <i class="fas fa-sort ml-1"></i>
                                </th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700" onclick="window.invoiceManager?.sortBy('payment_status')">
                                    Status <i class="fas fa-sort ml-1"></i>
                                </th>
                                <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceTable" class="divide-y divide-gray-100 dark:divide-gray-700">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div id="invoicePagination" class="flex justify-between items-center mt-4 flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
                    <select id="invoicePageSize" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <button id="invoicePrevBtn" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-left mr-1"></i>Previous
                    </button>
                    <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
                        Page <span id="invoiceCurrentPage">1</span> of <span id="invoiceTotalPages">1</span>
                    </div>
                    <button id="invoiceNextBtn" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">
                        Next<i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>
                
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-white" id="invoiceStartRecord">0</span> to 
                    <span class="font-semibold text-gray-900 dark:text-white" id="invoiceEndRecord">0</span> of 
                    <span class="font-semibold text-gray-900 dark:text-white" id="invoiceTotalRecords">0</span> results
                </div>
            </div>
        </div>
    </div>

    <x-ui.customer.modals.invoice-modal />

    @vite('resources/js/data/customer/invoice-list-data.js')
</x-app-layout>
