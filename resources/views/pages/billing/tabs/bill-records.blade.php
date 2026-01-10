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
        $recordsData = [
            [
                'bill_no' => 'BILL-2024-001',
                'customer' => '<span class=\"font-medium\">Juan Dela Cruz</span>',
                'period' => 'January 2024',
                'consumption' => '125 m³',
                'amount' => '<span class=\"text-green-600 font-semibold\">₱2,875.50</span>',
                'status' => '<span class=\"inline-flex items-center px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium\">Generated</span>',
                'actions' => '<button class=\"text-blue-600 hover:text-blue-800 text-sm\"><i class=\"fas fa-eye\"></i></button>'
            ],
            [
                'bill_no' => 'BILL-2024-002',
                'customer' => '<span class=\"font-medium\">Maria Santos</span>',
                'period' => 'January 2024',
                'consumption' => '95 m³',
                'amount' => '<span class=\"text-green-600 font-semibold\">₱2,175.00</span>',
                'status' => '<span class=\"inline-flex items-center px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded-full text-xs font-medium\">Posted</span>',
                'actions' => '<button class=\"text-blue-600 hover:text-blue-800 text-sm\"><i class=\"fas fa-eye\"></i></button>'
            ]
        ];

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
        :data="$recordsData"
        :searchable="false"
        :paginated="true"
        :pageSize="10"
        :actions="false"
    />
</div>
