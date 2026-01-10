<!-- Ledger Tab -->
<div id="ledger-content" class="tab-content hidden">
    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-book text-blue-600 dark:text-blue-400 mt-0.5 mr-2"></i>
            <div class="text-sm text-blue-800 dark:text-blue-300">
                <strong>Transaction Ledger:</strong> Complete payment and billing history for this customer account.
            </div>
        </div>
    </div>

    <x-ui.action-functions 
        :showSearch="false"
        filterLabel="All Type"
        :filterOptions="[
            ['value' => 'Bill', 'label' => 'Bill'],
            ['value' => 'Payment', 'label' => 'Payment'],
            ['value' => 'Adjustment', 'label' => 'Adjustment']
        ]"
        :showDateFilter="true"
        :showExport="true"
        tableId="ledger-table"
    />

    @php
        $ledgerData = [
            ['id' => 1, 'date' => '2024-01-05', 'description' => 'Bill Generated - January 2024', 'reference' => '<span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">BILL-2024-001</span>', 'debit' => '<span class="text-red-600 dark:text-red-400 font-semibold">₱739.20</span>', 'credit' => '-', 'balance' => '₱739.20'],
            ['id' => 2, 'date' => '2024-01-15', 'description' => 'Payment Received - Over the Counter', 'reference' => '<span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">PAY-2024-001</span>', 'debit' => '-', 'credit' => '<span class="text-green-600 dark:text-green-400 font-semibold">₱739.20</span>', 'balance' => '₱0.00'],
            ['id' => 3, 'date' => '2023-12-05', 'description' => 'Bill Generated - December 2023', 'reference' => '<span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">BILL-2023-012</span>', 'debit' => '<span class="text-red-600 dark:text-red-400 font-semibold">₱654.80</span>', 'credit' => '-', 'balance' => '₱654.80'],
            ['id' => 4, 'date' => '2023-12-20', 'description' => 'Payment Received - GCash', 'reference' => '<span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">PAY-2023-012</span>', 'debit' => '-', 'credit' => '<span class="text-green-600 dark:text-green-400 font-semibold">₱654.80</span>', 'balance' => '₱0.00'],
            ['id' => 5, 'date' => '2023-11-10', 'description' => 'Adjustment - Senior Citizen Discount', 'reference' => '<span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">ADJ-2023-011</span>', 'debit' => '-', 'credit' => '<span class="text-green-600 dark:text-green-400 font-semibold">₱32.74</span>', 'balance' => '₱0.00'],
        ];

        $ledgerHeaders = [
            ['key' => 'date', 'label' => 'Date', 'html' => false],
            ['key' => 'description', 'label' => 'Description', 'html' => false],
            ['key' => 'reference', 'label' => 'Reference', 'html' => true],
            ['key' => 'debit', 'label' => 'Debit', 'html' => true],
            ['key' => 'credit', 'label' => 'Credit', 'html' => true],
            ['key' => 'balance', 'label' => 'Balance', 'html' => false],
        ];
    @endphp

    <x-table
        id="ledger-table"
        :headers="$ledgerHeaders"
        :data="$ledgerData"
        :searchable="true"
        :paginated="true"
        :actions="false"
        :page-size="10"
    />
</div>
