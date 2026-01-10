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
        // Load customer data
        $consumerData = [
            [
                'consumer' => '<span class="font-medium">Juan Dela Cruz</span>',
                'account_no' => 'ACC-001',
                'reading' => '2,450 m³',
                'consumption' => '125',
                'amount' => '<span class="text-red-600 font-semibold">₱2,875.50</span>',
                'status' => '<span class="inline-flex items-center px-2 py-1 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-full text-xs font-medium"><span class="w-1.5 h-1.5 bg-red-600 dark:bg-red-400 rounded-full mr-1.5"></span>Unpaid</span>',
                'actions' => '<button class="text-blue-600 hover:text-blue-800 text-sm font-medium" onclick="viewCustomerDetails(\'Juan Dela Cruz\', \'ACC-001\')"><i class="fas fa-eye mr-1"></i>View</button>'
            ],
            [
                'consumer' => '<span class="font-medium">Maria Santos</span>',
                'account_no' => 'ACC-002',
                'reading' => '1,890 m³',
                'consumption' => '95',
                'amount' => '<span class="text-green-600 font-semibold">₱2,175.00</span>',
                'status' => '<span class="inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full text-xs font-medium"><span class="w-1.5 h-1.5 bg-green-600 dark:bg-green-400 rounded-full mr-1.5"></span>Paid</span>',
                'actions' => '<button class="text-blue-600 hover:text-blue-800 text-sm font-medium" onclick="viewCustomerDetails(\'Maria Santos\', \'ACC-002\')"><i class="fas fa-eye mr-1"></i>View</button>'
            ],
            [
                'consumer' => '<span class="font-medium">Pedro Garcia</span>',
                'account_no' => 'ACC-003',
                'reading' => '2,120 m³',
                'consumption' => '110',
                'amount' => '<span class="text-orange-600 font-semibold">₱1,450.75</span>',
                'status' => '<span class="inline-flex items-center px-2 py-1 bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 rounded-full text-xs font-medium"><span class="w-1.5 h-1.5 bg-orange-600 dark:bg-orange-400 rounded-full mr-1.5"></span>Partial</span>',
                'actions' => '<button class="text-blue-600 hover:text-blue-800 text-sm font-medium" onclick="viewCustomerDetails(\'Pedro Garcia\', \'ACC-003\')"><i class="fas fa-eye mr-1"></i>View</button>'
            ]
        ];

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
        :data="$consumerData"
        :searchable="false"
        :paginated="true"
        :pageSize="10"
        :actions="false"
    />
</div>

<script>
function viewCustomerDetails(name, accountNo) {
    window.location.href = '{{ route("billing.customer-details") }}?customer=' + encodeURIComponent(name) + '&account=' + encodeURIComponent(accountNo);
}
</script>
