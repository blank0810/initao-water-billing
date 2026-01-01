<!-- Customers Tab Content -->
<div>
    <x-ui.action-functions 
        searchPlaceholder="Search by customer name, account no..."
        filterLabel="All Status"
        :filterOptions="[
            ['value' => 'Paid', 'label' => 'Paid'],
            ['value' => 'Unpaid', 'label' => 'Unpaid'],
            ['value' => 'Partial', 'label' => 'Partial']
        ]"
        :showDateFilter="true"
        :showExport="true"
        tableId="consumerBillingTable"
    />

    @php
        $consumerHeaders = [
            ['key' => 'consumer', 'label' => 'Customer', 'html' => true],
            ['key' => 'account_no', 'label' => 'Account No', 'html' => false],
            ['key' => 'reading', 'label' => 'Current Reading', 'html' => false],
            ['key' => 'consumption', 'label' => 'Consumption (m³)', 'html' => false],
            ['key' => 'amount', 'label' => 'Amount Due', 'html' => true],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
            ['key' => 'actions', 'label' => 'Actions', 'html' => true],
        ];
    @endphp

    <x-table
        id="consumerBillingTable"
        :headers="$consumerHeaders"
        :data="[]"
        :searchable="false"
        :paginated="true"
        :pageSize="10"
        :actions="false"
    />
</div>
