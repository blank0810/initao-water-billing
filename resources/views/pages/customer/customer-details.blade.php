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
                            <p class="text-sm font-medium text-gray-900 dark:text-white" id="consumer-id">1001</p>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Full Name</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" id="consumer-name">Juan Dela Cruz</p>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Address</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" id="consumer-address">Purok 1, Poblacion</p>
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
                            <p class="text-sm font-mono font-medium text-gray-900 dark:text-white" id="consumer-meter">MTR-001</p>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Rate Class</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" id="consumer-rate">₱25.50/m³</p>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Total Bill</p>
                            <p class="text-sm font-semibold text-red-600 dark:text-red-400" id="consumer-bill">₱3,500.00</p>
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300" id="consumer-status">Active</span>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Ledger Balance</p>
                            <p class="text-sm font-semibold text-green-600 dark:text-green-400" id="consumer-ledger">₱0.00</p>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Last Updated</p>
                            <p class="text-sm text-gray-900 dark:text-white">Jan 5, 2024</p>
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

            <!-- Include tab files for modular structure -->
            @include('pages.customer.tabs.documents-tab')
            @include('pages.customer.tabs.connections-tab')
            @include('pages.customer.tabs.ledger-tab')
    </div>
</div>

<x-ui.customer.modals.connection-details />

    @vite(['resources/js/data/customer/customer-details-data.js', 'resources/js/data/customer/enhanced-customer-data.js', 'resources/js/data/customer/customer-ledger-data.js', 'resources/js/data/billing/customer-profile-data.js'])

    <script>
    // Tab switching functionality
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active styling from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });

        // Show selected tab content
        const contentId = `${tabName}-content`;
        const contentElement = document.getElementById(contentId);
        if (contentElement) {
            contentElement.classList.remove('hidden');
        }

        // Highlight selected tab button
        const tabButton = document.getElementById(`tab-${tabName}`);
        if (tabButton) {
            tabButton.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            tabButton.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        }

        // Load ledger data if ledger tab is selected
        if (tabName === 'ledger') {
            // Ledger data is now handled by x-table component with static data
            console.log('Ledger tab activated');
        }
    </script>
</x-app-layout>
