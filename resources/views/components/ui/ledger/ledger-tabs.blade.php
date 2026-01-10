<!-- Ledger Tabs Component -->
<div class="mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 overflow-x-auto">
            <button onclick="switchLedgerTab('consumers')" id="tab-consumers" class="ledger-tab border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400 whitespace-nowrap">
                <i class="fas fa-users mr-2"></i>Consumer Ledgers
            </button>
            <button onclick="switchLedgerTab('transactions')" id="tab-transactions" class="ledger-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                <i class="fas fa-exchange-alt mr-2"></i>Transaction Types
            </button>
            <button onclick="switchLedgerTab('history')" id="tab-history" class="ledger-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                <i class="fas fa-history mr-2"></i>Ledger History
            </button>
        </nav>
    </div>
</div>
