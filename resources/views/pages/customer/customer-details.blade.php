<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div x-data="{ allowedActions: [], customerStatus: 'UNKNOWN' }"
                 x-init="
                    allowedActions = window.customerAllowedActions || [];
                    customerStatus = window.customerStatus || 'UNKNOWN';
                    window.addEventListener('customer-details-loaded', (e) => {
                        allowedActions = e.detail.allowedActions;
                        customerStatus = e.detail.customerStatus;
                    });
                 ">

            <x-ui.page-header
                title="Customer Details"
                subtitle="View customer information and history">
            <x-slot name="actions">
                <div class="flex items-center gap-2">
                    <x-ui.button variant="outline" href="{{ route('customer.list') }}">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </x-ui.button>
                    <template x-if="allowedActions.includes('edit')">
                        <button onclick="document.dispatchEvent(new CustomEvent('open-edit-customer-modal'))"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </button>
                    </template>
                    <template x-if="allowedActions.includes('delete')">
                        <button onclick="document.dispatchEvent(new CustomEvent('confirm-delete-customer'))"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </template>
                    <template x-if="allowedActions.includes('reactivate')">
                        <button onclick="document.dispatchEvent(new CustomEvent('reactivate-customer'))"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <i class="fas fa-redo mr-2"></i>Reactivate
                        </button>
                    </template>
                </div>
            </x-slot>
            </x-ui.page-header>

            {{-- Status Banners --}}
            <div x-show="customerStatus === 'PENDING'" x-cloak class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-clock text-yellow-500 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">Pending Customer</h4>
                        <p class="text-sm text-yellow-700 dark:text-yellow-400">This customer is in pending status. Reactivate to restore full access.</p>
                    </div>
                </div>
            </div>

            <div x-show="customerStatus === 'SUSPENDED'" x-cloak class="mb-4 p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-pause-circle text-orange-500 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-orange-800 dark:text-orange-300">Suspended Customer</h4>
                        <p class="text-sm text-orange-700 dark:text-orange-400">This customer is suspended. Only viewing and paying existing bills is available.</p>
                    </div>
                </div>
            </div>

            <div x-show="customerStatus === 'INACTIVE'" x-cloak class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-ban text-red-500 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-red-800 dark:text-red-300">Inactive Customer</h4>
                        <p class="text-sm text-red-700 dark:text-red-400">This customer is inactive. Only viewing and paying existing bills is available.</p>
                    </div>
                </div>
            </div>

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
                    <button onclick="switchTab('ledger')" id="tab-ledger" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                        <i class="fas fa-book mr-2"></i>Ledger
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
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Account Type</th>
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

        @include('pages.customer.tabs.ledger-tab')

            </div> {{-- end x-data Alpine scope --}}
    </div>
</div>

<x-ui.customer.modals.connection-details />
<x-ui.customer.modals.ledger-entry-details />

    <script>
        window.canVoidPayments = @json(auth()->user()?->can('payments.void') ?? false);
    </script>
    @vite(['resources/js/data/customer/customer-details-data.js', 'resources/js/data/customer/enhanced-customer-data.js', 'resources/js/data/customer/customer-ledger-data.js'])
</x-app-layout>
