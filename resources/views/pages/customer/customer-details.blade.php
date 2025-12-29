<x-app-layout>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="p-6">
        <x-ui.page-header
            title="Customer Details"
            subtitle="View customer information and history">
            <x-slot name="actions">
                <x-ui.button variant="outline" href="{{ route('customer.list') }}">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </x-ui.button>
            </x-slot>
        </x-ui.page-header>

        <x-ui.card class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer Information</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Customer ID:</span>
                            <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white" id="consumer-id">1001</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Name:</span>
                            <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white" id="consumer-name">Juan Dela Cruz</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Address:</span>
                            <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white" id="consumer-address">Purok 1, Poblacion</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Meter & Billing</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Meter No:</span>
                            <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white" id="consumer-meter">MTR-001</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Rate:</span>
                            <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white" id="consumer-rate">₱25.50/m³</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Bill:</span>
                            <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white" id="consumer-bill">₱3,500.00</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Status</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" id="consumer-status">Active</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Ledger Balance:</span>
                            <span class="ml-2 text-sm font-medium text-green-600" id="consumer-ledger">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>

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
