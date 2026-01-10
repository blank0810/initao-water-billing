<!-- Billing Tabs Component -->
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
