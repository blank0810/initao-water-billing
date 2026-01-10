<!-- Ledger History Tab (Full Chronological View) -->
<div>
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-shield-alt text-yellow-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-yellow-800 dark:text-yellow-300">
                <strong>Immutable History:</strong> All entries are permanent, audit-ready, and cannot be deleted or modified after posting. COA compliant.
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="mb-6 space-y-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-filter mr-2"></i>Filter Ledger History
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                    <input type="date" id="ledger-date-from" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                    <input type="date" id="ledger-date-to" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consumer</label>
                    <input type="text" placeholder="Search consumer..." id="ledger-consumer" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transaction Type</label>
                    <select id="ledger-type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="billing">Billing</option>
                        <option value="payment">Payment</option>
                        <option value="adjustment">Adjustment</option>
                        <option value="penalty">Penalty</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                <button onclick="applyLedgerFilter()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-search mr-2"></i>Apply Filter
                </button>
                <button onclick="resetLedgerFilter()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-900 rounded-lg font-medium transition">
                    <i class="fas fa-redo mr-2"></i>Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Ledger History Table -->
    <x-ui.action-functions 
        searchPlaceholder="Search by transaction no, consumer, reference..."
        filterLabel="All Statuses"
        :filterOptions="[
            ['value' => 'Posted', 'label' => 'Posted'],
            ['value' => 'Pending', 'label' => 'Pending'],
            ['value' => 'Reversed', 'label' => 'Reversed']
        ]"
        :showDateFilter="true"
        :showExport="true"
        tableId="ledgerHistoryTable"
    />

    @php
        $ledgerHistoryHeaders = [
            ['key' => 'entry_date', 'label' => 'Entry Date', 'html' => false],
            ['key' => 'transaction_no', 'label' => 'Transaction No', 'html' => false],
            ['key' => 'consumer', 'label' => 'Consumer', 'html' => true],
            ['key' => 'type', 'label' => 'Type', 'html' => true],
            ['key' => 'debit', 'label' => 'Debit', 'html' => true],
            ['key' => 'credit', 'label' => 'Credit', 'html' => true],
            ['key' => 'balance', 'label' => 'Balance', 'html' => true],
            ['key' => 'reference', 'label' => 'Reference', 'html' => false],
            ['key' => 'posted_by', 'label' => 'Posted By', 'html' => false],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
        ];
    @endphp

    <x-table
        id="ledgerHistoryTable"
        :headers="$ledgerHistoryHeaders"
        :data="[]"
        :searchable="false"
        :paginated="true"
        :pageSize="20"
        :actions="false"
    />
</div>

<script>
function applyLedgerFilter() {
    alert('Applying ledger history filter...');
}

function resetLedgerFilter() {
    document.getElementById('ledger-date-from').value = '';
    document.getElementById('ledger-date-to').value = '';
    document.getElementById('ledger-consumer').value = '';
    document.getElementById('ledger-type').value = '';
}

window.applyLedgerFilter = applyLedgerFilter;
window.resetLedgerFilter = resetLedgerFilter;
</script>
