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

    <div class="mb-4 flex justify-end">
        <button onclick="openAddAdjustmentModal()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
            <i class="fas fa-plus mr-2"></i>Add Adjustment
        </button>
    </div>

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

    @php
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
        :data="[]"
        :searchable="false"
        :paginated="true"
        :pageSize="10"
        :actions="false"
    />
</div>
