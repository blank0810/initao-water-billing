<!-- Billing Records Tab Content -->
<div>
    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-blue-800 dark:text-blue-300">
                <strong>Billing Records:</strong> Official history of all generated bills per billing period. This serves as audit reference and cannot be modified after posting.
            </div>
        </div>
    </div>

    <x-ui.action-functions 
        searchPlaceholder="Search by bill no, customer, period..."
        filterLabel="All Periods"
        :filterOptions="[
            ['value' => 'BP-2024-01', 'label' => 'January 2024'],
            ['value' => 'BP-2024-02', 'label' => 'February 2024'],
            ['value' => 'BP-2024-03', 'label' => 'March 2024']
        ]"
        :showDateFilter="true"
        :showExport="true"
        tableId="billingRecordsTable"
    />

    @php
        $billingRecordsHeaders = [
            ['key' => 'bill_no', 'label' => 'Bill No', 'html' => false],
            ['key' => 'customer', 'label' => 'Customer', 'html' => true],
            ['key' => 'period', 'label' => 'Billing Period', 'html' => false],
            ['key' => 'consumption', 'label' => 'Consumption (m³)', 'html' => false],
            ['key' => 'amount', 'label' => 'Amount', 'html' => true],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
            ['key' => 'actions', 'label' => 'Actions', 'html' => true],
        ];
    @endphp

    <x-table
        id="billingRecordsTable"
        :headers="$billingRecordsHeaders"
        :data="[]"
        :searchable="false"
        :paginated="true"
        :pageSize="10"
        :actions="false"
    />
</div>
