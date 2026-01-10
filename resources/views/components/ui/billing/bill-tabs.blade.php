<!-- Bill Tabs Component (For Bill Show Page) -->
<div class="mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 overflow-x-auto">
            <button onclick="showBillTab('details')" id="tab-details" class="bill-tab border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400 whitespace-nowrap">
                <i class="fas fa-file-alt mr-2"></i>Bill Details
            </button>
            <button onclick="showBillTab('ledger')" id="tab-ledger" class="bill-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                <i class="fas fa-book mr-2"></i>Ledger History
            </button>
            <button onclick="showBillTab('downloads')" id="tab-downloads" class="bill-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                <i class="fas fa-download mr-2"></i>Downloads
            </button>
            <button onclick="showBillTab('payments')" id="tab-payments" class="bill-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                <i class="fas fa-hand-holding-usd mr-2"></i>Payments
            </button>
        </nav>
    </div>
</div>
