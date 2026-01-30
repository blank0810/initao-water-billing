<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <x-ui.page-header
                title="Customer Details"
                subtitle="View customer information and history">
            <x-slot name="actions">
                <x-ui.button variant="outline" href="{{ route('customer.list') }}">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </x-ui.button>
            </x-slot>
            </x-ui.page-header>

            <!-- Customer Profile Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Customer Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-user-circle text-blue-600 dark:text-blue-400 mr-2 text-lg"></i>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Customer Information</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Customer ID</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" id="consumer-id">-</p>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Full Name</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" id="consumer-name">-</p>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Address</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" id="consumer-address">-</p>
                        </div>
                    </div>
                </div>

                <!-- Meter & Billing Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-tachometer-alt text-green-600 dark:text-green-400 mr-2 text-lg"></i>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Meter & Billing</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Meter Number</p>
                            <p class="text-sm font-mono font-medium text-gray-900 dark:text-white" id="consumer-meter">-</p>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Rate Class</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" id="consumer-rate">-</p>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Total Bill</p>
                            <p class="text-sm font-semibold text-red-600 dark:text-red-400" id="consumer-bill">-</p>
                        </div>
                    </div>
                </div>

                <!-- Account Status Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-info-circle text-purple-600 dark:text-purple-400 mr-2 text-lg"></i>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Account Status</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300" id="consumer-status">-</span>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Ledger Balance</p>
                            <p class="text-sm font-semibold text-green-600 dark:text-green-400" id="consumer-ledger">-</p>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Last Updated</p>
                            <p class="text-sm text-gray-900 dark:text-white" id="consumer-updated">-</p>
                        </div>
                    </div>
                </div>
            </div>

        <div class="mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="switchTab('documents')" id="tab-documents" class="tab-button border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                        <i class="fas fa-file-alt mr-2"></i>Documents & History
                    </button>
                    <button onclick="switchTab('connections')" id="tab-connections" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                        <i class="fas fa-plug mr-2"></i>Service Connections
                    </button>
                </nav>
            </div>
        </div>

        <div id="documents-content" class="tab-content">
            <div class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="documents-tbody" class="divide-y divide-gray-200 dark:divide-gray-700">
                    </tbody>
                </table>
            </div>
        </div>

        <div id="connections-content" class="tab-content hidden">
            <div class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Account No</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer Type</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Meter Reader & Area</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Meter No</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date Installed</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="connections-tbody" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td colspan="7" class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">Loading connections...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<x-ui.customer.modals.connection-details />

    @vite(['resources/js/data/customer/customer-details-data.js', 'resources/js/data/customer/enhanced-customer-data.js'])
</x-app-layout>
