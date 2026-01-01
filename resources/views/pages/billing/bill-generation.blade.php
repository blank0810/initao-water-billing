<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <x-ui.page-header 
                title="Billing Module" 
                subtitle="Generate, manage, and monitor water bills. Rates and periods are reference-only (maintained in Master Files)."
            >
                <x-slot name="actions">
                    <x-ui.button variant="secondary" size="sm" href="#" icon="fas fa-chart-line">
                        Overall Data
                    </x-ui.button>
                    <x-ui.button variant="primary" size="sm" onclick="openProcessPaymentModal()" icon="fas fa-hand-holding-dollar">
                        Process Payment
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            @include('components.ui.billing.summary-cards')

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto">
                        <button onclick="showBillingTab('customers')" id="tab-customers" class="billing-tab border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400 whitespace-nowrap">
                            <i class="fas fa-users mr-2"></i>Customers
                        </button>
                        <button onclick="showBillingTab('generation')" id="tab-generation" class="billing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                            <i class="fas fa-file-invoice mr-2"></i>Bill Generation
                        </button>
                        <button onclick="showBillingTab('records')" id="tab-records" class="billing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>Billing Records
                        </button>
                        <button onclick="showBillingTab('adjustments')" id="tab-adjustments" class="billing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                            <i class="fas fa-balance-scale mr-2"></i>Adjustments
                        </button>
                        <button onclick="showBillingTab('ledger')" id="tab-ledger" class="billing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                            <i class="fas fa-book mr-2"></i>Ledger
                        </button>
                        <button onclick="showBillingTab('downloads')" id="tab-downloads" class="billing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                            <i class="fas fa-download mr-2"></i>Download Bills
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Content: Customers -->
            <div id="content-customers" class="billing-tab-content">
                @include('pages.billing.customers-tab')
            </div>

            <!-- Tab Content: Bill Generation -->
            <div id="content-generation" class="billing-tab-content hidden">
                @include('pages.billing.generation-tab')
            </div>

            <!-- Tab Content: Billing Records -->
            <div id="content-records" class="billing-tab-content hidden">
                @include('pages.billing.records-tab')
            </div>

            <!-- Tab Content: Adjustments -->
            <div id="content-adjustments" class="billing-tab-content hidden">
                @include('pages.billing.adjustments-tab')
            </div>

            <!-- Tab Content: Ledger -->
            <div id="content-ledger" class="billing-tab-content hidden">
                @include('pages.billing.ledger-tab-wrapper')
            </div>

            <!-- Tab Content: Download Bills -->
            <div id="content-downloads" class="billing-tab-content hidden">
                @include('pages.billing.downloads-tab')
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

    @vite([
        'resources/js/utils/action-functions.js',
        'resources/js/data/billing/billing-consumer-data.js',
        'resources/js/data/billing/billing-records-data.js',
        'resources/js/data/billing/billing-generation-data.js',
        'resources/js/data/billing/billing-adjustments-data.js',
        'resources/js/data/billing/billing-master.js',
        'resources/js/data/rate/rate-data.js'
    ])
    
    <script>
    function showBillingTab(tab) {
        // Hide all tab contents
        document.querySelectorAll('.billing-tab-content').forEach(el => el.classList.add('hidden'));
        
        // Remove active classes from all tabs
        document.querySelectorAll('.billing-tab').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });
        
        // Show selected tab content
        document.getElementById('content-' + tab).classList.remove('hidden');
        
        // Add active classes to selected tab
        const activeTab = document.getElementById('tab-' + tab);
        activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        activeTab.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
    }

    function downloadBills(format) {
        const reportType = document.getElementById('download-report-type').value;
        const customer = document.getElementById('download-customer').value;
        const period = document.getElementById('download-period').value;
        
        let message = `Downloading ${reportType} report in ${format.toUpperCase()} format`;
        if (customer) message += ` for: ${customer}`;
        if (period) message += ` (Period: ${period})`;
        
        alert(message + '\n\nDownload will be logged for audit purposes.');
        
        // Simulate download
        setTimeout(() => {
            alert('Download completed successfully!\nSaved to: Downloads/billing_report_' + Date.now() + '.' + format);
        }, 1500);
    }

    window.showBillingTab = showBillingTab;
    window.downloadBills = downloadBills;
    
    // Initialize first tab on load and ensure data is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize action functions for all tables
        setTimeout(() => {
            ['consumerBillingTable', 'billingRecordsTable', 'billGenerationTable', 'adjustmentsTable'].forEach(tableId => {
                if (document.getElementById(tableId)) {
                    new ActionFunctionsManager(tableId);
                }
            });
        }, 100);
        
        // Show first tab
        showBillingTab('customers');
    });
    </script>
</x-app-layout>
