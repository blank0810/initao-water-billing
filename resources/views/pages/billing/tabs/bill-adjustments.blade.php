<!-- Adjustments Tab Content -->
<div>
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-yellow-800 dark:text-yellow-300">
                <strong>Authorized Adjustments Only:</strong> All billing corrections require proper authorization and justification. Complete audit trail is maintained for COA compliance.
            </div>
        </div>
    </div>

    <div class="flex gap-2 items-end">
        <div class="flex-1">
            <x-ui.action-functions 
                searchPlaceholder="Search by adjustment no, customer..."
                filterLabel="All Types"
                :filterOptions="[
                    ['value' => 'Credit', 'label' => 'Credit Memo'],
                    ['value' => 'Debit', 'label' => 'Debit Memo'],
                    ['value' => 'Correction', 'label' => 'Correction']
                ]"
                :showDateFilter="true"
                :showExport="true"
                tableId="adjustmentsTable"
            />
        </div>
        <button onclick="openAdjustmentModal()" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition flex items-center gap-1 h-fit whitespace-nowrap">
            <i class="fas fa-plus text-xs"></i>Add Adjustment
        </button>
    </div>

    @php
        $adjustmentData = [
            [
                'adj_no' => 'ADJ-2024-001',
                'consumer' => '<span class=\"font-medium\">Juan Dela Cruz</span>',
                'type' => '<span class=\"inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded text-xs font-medium\">Credit Memo</span>',
                'amount' => '<span class=\"text-green-600 font-semibold\">-₱150.00</span>',
                'reason' => 'Meter Error Correction',
                'status' => '<span class=\"inline-flex items-center px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium\">Approved</span>',
                'actions' => '<button class=\"text-blue-600 hover:text-blue-800 text-sm\"><i class=\"fas fa-eye\"></i></button>'
            ],
            [
                'adj_no' => 'ADJ-2024-002',
                'consumer' => '<span class=\"font-medium\">Maria Santos</span>',
                'type' => '<span class=\"inline-flex items-center px-2 py-1 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded text-xs font-medium\">Debit Memo</span>',
                'amount' => '<span class=\"text-red-600 font-semibold\">+₱75.00</span>',
                'reason' => 'Late Payment Fee',
                'status' => '<span class=\"inline-flex items-center px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium\">Approved</span>',
                'actions' => '<button class=\"text-blue-600 hover:text-blue-800 text-sm\"><i class=\"fas fa-eye\"></i></button>'
            ]
        ];

        $adjustmentHeaders = [
            ['key' => 'adj_no', 'label' => 'Adjustment No', 'html' => false],
            ['key' => 'consumer', 'label' => 'Customer', 'html' => true],
            ['key' => 'type', 'label' => 'Type', 'html' => true],
            ['key' => 'amount', 'label' => 'Amount', 'html' => true],
            ['key' => 'reason', 'label' => 'Reason', 'html' => false],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
            ['key' => 'actions', 'label' => 'Actions', 'html' => true],
        ];
    @endphp

    <x-table
        id="adjustmentsTable"
        :headers="$adjustmentHeaders"
        :data="$adjustmentData"
        :searchable="false"
        :paginated="true"
        :pageSize="10"
        :actions="false"
    />
</div>
