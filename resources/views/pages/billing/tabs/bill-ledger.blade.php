<!-- Ledger Tab Content (COA Compliant) -->
<div>
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-shield-alt text-yellow-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-yellow-800 dark:text-yellow-300">
                <strong>COA Compliance:</strong> All ledger entries are permanent and audit-ready. No deletion or modification allowed after posting.
            </div>
        </div>
    </div>
    <x-ui.action-functions 
        searchPlaceholder="Search by description, reference..."
        filterLabel="All Types"
        :filterOptions="[
            ['value' => 'Debit', 'label' => 'Debit'],
            ['value' => 'Credit', 'label' => 'Credit']
        ]"
        :showDateFilter="true"
        :showExport="true"
        tableId="ledgerTable"
    />

    @php
        $ledgerData = [
            [
                'date' => '2024-01-05',
                'description' => 'Bill Generated - January 2024',
                'reference' => 'BILL-2024-001',
                'debit' => '<span class=\"text-red-600 font-semibold\">₱2,875.50</span>',
                'credit' => '-',
                'balance' => '<span class=\"font-semibold\">₱2,875.50</span>'
            ],
            [
                'date' => '2024-01-15',
                'description' => 'Payment Received - Check',
                'reference' => 'PAY-2024-001',
                'debit' => '-',
                'credit' => '<span class=\"text-green-600 font-semibold\">₱1,500.00</span>',
                'balance' => '<span class=\"font-semibold\">₱1,375.50</span>'
            ],
            [
                'date' => '2024-01-20',
                'description' => 'Adjustment - Rebate',
                'reference' => 'ADJ-2024-001',
                'debit' => '-',
                'credit' => '<span class=\"text-green-600 font-semibold\">₱150.00</span>',
                'balance' => '<span class=\"font-semibold\">₱1,225.50</span>'
            ]
        ];

        $ledgerHeaders = [
            ['key' => 'date', 'label' => 'Date', 'html' => false],
            ['key' => 'description', 'label' => 'Description', 'html' => false],
            ['key' => 'reference', 'label' => 'Reference', 'html' => false],
            ['key' => 'debit', 'label' => 'Debit', 'html' => true],
            ['key' => 'credit', 'label' => 'Credit', 'html' => true],
            ['key' => 'balance', 'label' => 'Balance', 'html' => true]
        ];
    @endphp

    <x-table
        id="ledgerTable"
        :headers="$ledgerHeaders"
        :data="$ledgerData"
        :searchable="false"
        :paginated="true"
        :pageSize="10"
    />
</div>
