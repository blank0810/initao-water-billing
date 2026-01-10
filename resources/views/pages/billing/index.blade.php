<x-app-layout>
    <div class="flex-1 flex flex-col">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex-1">
            
            <!-- Page Header -->
            <x-ui.page-header 
                title="Billing Module" 
                subtitle="Manage water bills, customers, rates, and billing records"
            >
            </x-ui.page-header>

            <!-- Summary Cards -->
            @include('components.ui.billing.summary-cards')

            <!-- Main Tabs Container -->
            <div class="mt-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
                        <nav class="flex flex-nowrap" aria-label="Tabs">
                            <!-- Customers Tab -->
                            <button onclick="switchTab('customers')" id="tab-customers" class="tab-button px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white border-b-2 border-transparent hover:border-gray-300 active-tab">
                                <i class="fas fa-users mr-2"></i>Customers
                            </button>
                            
                            <!-- Bill Generation Tab -->
                            <button onclick="switchTab('generation')" id="tab-generation" class="tab-button px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white border-b-2 border-transparent hover:border-gray-300">
                                <i class="fas fa-cogs mr-2"></i>Bill Generation
                            </button>
                            
                            <!-- Billing Records Tab -->
                            <button onclick="switchTab('records')" id="tab-records" class="tab-button px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white border-b-2 border-transparent hover:border-gray-300">
                                <i class="fas fa-clipboard-list mr-2"></i>Billing Records
                            </button>
                            
                            <!-- Adjustments Tab -->
                            <button onclick="switchTab('adjustments')" id="tab-adjustments" class="tab-button px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white border-b-2 border-transparent hover:border-gray-300">
                                <i class="fas fa-sliders-h mr-2"></i>Adjustments
                            </button>
                            
                            <!-- Ledger Tab -->
                            <button onclick="switchTab('ledger')" id="tab-ledger" class="tab-button px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white border-b-2 border-transparent hover:border-gray-300">
                                <i class="fas fa-book mr-2"></i>Ledger
                            </button>
                            
                            <!-- Download Bills Tab -->
                            <button onclick="switchTab('downloads')" id="tab-downloads" class="tab-button px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white border-b-2 border-transparent hover:border-gray-300">
                                <i class="fas fa-download mr-2"></i>Download Bills
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- Customers Tab Content -->
                        <div id="content-customers" class="tab-content">
                            @include('pages.billing.tabs.bill-customers')
                        </div>

                        <!-- Bill Generation Tab Content -->
                        <div id="content-generation" class="tab-content hidden">
                            @include('pages.billing.tabs.bill-generation')
                        </div>

                        <!-- Billing Records Tab Content -->
                        <div id="content-records" class="tab-content hidden">
                            @include('pages.billing.tabs.bill-records')
                        </div>

                        <!-- Adjustments Tab Content -->
                        <div id="content-adjustments" class="tab-content hidden">
                            @include('pages.billing.tabs.bill-adjustments')
                        </div>

                        <!-- Ledger Tab Content -->
                        <div id="content-ledger" class="tab-content hidden">
                            @include('pages.billing.tabs.bill-ledger')
                        </div>

                        <!-- Download Bills Tab Content -->
                        <div id="content-downloads" class="tab-content hidden">
                            @include('pages.billing.tabs.bill-downloads')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modals -->
            @include('components.ui.billing.payment-modal')
            @include('components.ui.billing.adjustment-modal')
            @include('components.ui.billing.bill-details-modal')
            @include('components.ui.billing.generate-bill-modal')
            @include('components.ui.billing.preview-bill-modal')
            @include('components.ui.billing.receipt-modal')
            
            <!-- Additional Adjustment Modal (Inline) -->
            <div id="addAdjustmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add Bill Adjustment</h3>
                        <button onclick="closeAdjustmentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bill ID</label>
                                <input type="text" placeholder="Enter bill ID" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consumer</label>
                                <input type="text" placeholder="Consumer name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adjustment Type</label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                <option>-- Select Type --</option>
                                <option value="credit">Credit Memo</option>
                                <option value="debit">Debit Memo</option>
                                <option value="correction">Correction</option>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount</label>
                                <input type="number" placeholder="0.00" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason</label>
                                <input type="text" placeholder="Adjustment reason" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button onclick="closeAdjustmentModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-sm font-medium">
                            Cancel
                        </button>
                        <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                            Save Adjustment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                btn.classList.add('border-transparent');
            });
            
            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            // Add active state to clicked button
            const activeBtn = document.getElementById('tab-' + tabName);
            activeBtn.classList.remove('border-transparent');
            activeBtn.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        }

        // Initialize first tab as active
        document.addEventListener('DOMContentLoaded', function() {
            const firstBtn = document.getElementById('tab-customers');
            firstBtn.classList.remove('border-transparent');
            firstBtn.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        });

        // Modal functions
        function openProcessPaymentModal() {
            const modal = document.getElementById('paymentModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closePaymentModal() {
            const modal = document.getElementById('paymentModal');
            if (modal) modal.classList.add('hidden');
        }

        function openAdjustmentModal() {
            const modal = document.getElementById('addAdjustmentModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeAdjustmentModal() {
            const modal = document.getElementById('addAdjustmentModal');
            if (modal) modal.classList.add('hidden');
        }

        function openBillDetailsModal() {
            const modal = document.getElementById('billDetailsModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeBillDetailsModal() {
            const modal = document.getElementById('billDetailsModal');
            if (modal) modal.classList.add('hidden');
        }

        function openGenerateBillModal() {
            const modal = document.getElementById('generateBillModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeGenerateBillModal() {
            const modal = document.getElementById('generateBillModal');
            if (modal) modal.classList.add('hidden');
        }

        function openBillPreviewModal() {
            const modal = document.getElementById('previewBillModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeBillPreviewModal() {
            const modal = document.getElementById('previewBillModal');
            if (modal) modal.classList.add('hidden');
        }

        function openReceiptModal() {
            const modal = document.getElementById('receiptModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeReceiptModal() {
            const modal = document.getElementById('receiptModal');
            if (modal) modal.classList.add('hidden');
        }

        // Download functionality
        function downloadBills(format) {
            if (!format) return;
            alert(`Downloading billing report as ${format.toUpperCase()}...`);
            // TODO: Implement actual download logic
        }
    </script>

    <style>
        .active-tab {
            @apply border-blue-500 text-blue-600 dark:text-blue-400;
        }

        .hidden {
            display: none;
        }

        .tab-button {
            transition: all 0.2s ease;
        }

        .tab-button:hover {
            @apply bg-gray-50 dark:bg-gray-700;
        }
    </style>

    <!-- Load necessary scripts for Alpine, tables, and billing data -->
    @vite(['resources/js/bootstrap.js', 'resources/js/app.js', 'resources/js/utils/action-functions.js'])
</x-app-layout>
