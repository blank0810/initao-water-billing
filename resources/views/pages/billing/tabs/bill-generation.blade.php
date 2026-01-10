<!-- Bill Generation Tab Content -->
<div>
    <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-info-circle text-green-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-green-800 dark:text-green-300">
                <strong>Bill Generation:</strong> Generate water bills for a selected billing period by applying approved rate structure. Rate details are read-only references from Master Files module.
            </div>
        </div>
    </div>

    <!-- Bill Generation Controls - Single Horizontal Row -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-cogs mr-2"></i>Generate Bills
        </h3>
        
        <div class="flex flex-col lg:flex-row gap-3 items-end">
            <!-- Billing Period -->
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Billing Period</label>
                <select id="generation-period" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500">
                    <option value="BP-2024-02">February 2024 (Open)</option>
                    <option value="BP-2024-01">January 2024 (Closed)</option>
                </select>
            </div>

            <!-- Rate Structure -->
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rate Structure</label>
                <select disabled class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-sm">
                    <option>Residential Standard (Active)</option>
                </select>
            </div>

            <!-- Generation Scope -->
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Generation Scope</label>
                <select id="generation-scope" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500">
                    <option value="all">All Customers</option>
                    <option value="zone">By Zone</option>
                    <option value="barangay">By Barangay</option>
                    <option value="individual">Individual Customer</option>
                </select>
            </div>

            <!-- Generate Bills Button -->
            <button onclick="openGenerateBillModal()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition whitespace-nowrap">
                <i class="fas fa-bolt mr-1"></i>Generate Bills
            </button>
        </div>

        <div class="text-xs text-gray-500 dark:text-gray-400 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <i class="fas fa-clock mr-1"></i>Last generated: Jan 29, 2024
        </div>
    </div>

    <!-- Generated Bills Table -->
    <x-ui.action-functions 
        searchPlaceholder="Search by bill no, customer..."
        filterLabel="All Status"
        :filterOptions="[
            ['value' => 'Generated', 'label' => 'Generated'],
            ['value' => 'Pending', 'label' => 'Pending'],
            ['value' => 'Posted', 'label' => 'Posted']
        ]"
        :showDateFilter="true"
        :showExport="true"
        tableId="billGenerationTable"
    />

    @php
        $billGenHeaders = [
            ['key' => 'bill_no', 'label' => 'Bill No', 'html' => false],
            ['key' => 'customer', 'label' => 'Customer', 'html' => true],
            ['key' => 'period', 'label' => 'Period', 'html' => false],
            ['key' => 'consumption', 'label' => 'Consumption (m³)', 'html' => false],
            ['key' => 'amount', 'label' => 'Amount', 'html' => true],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
            ['key' => 'actions', 'label' => 'Actions', 'html' => true],
        ];

        $billGenData = [
            [
                'bill_no' => 'BILL-2024-001',
                'customer' => '<div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">ACC-2024-001</div>',
                'period' => 'Jan 2024',
                'consumption' => '25 m³',
                'amount' => '<span class="font-semibold text-gray-900 dark:text-white">₱2,450.00</span>',
                'status' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Generated</span>',
                'actions' => '<button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm" onclick="openBillDetailsModal()"><i class="fas fa-eye"></i></button>'
            ],
            [
                'bill_no' => 'BILL-2024-002',
                'customer' => '<div class="text-sm font-medium text-gray-900 dark:text-white">Maria Santos</div><div class="text-xs text-gray-500">ACC-2024-002</div>',
                'period' => 'Jan 2024',
                'consumption' => '32 m³',
                'amount' => '<span class="font-semibold text-gray-900 dark:text-white">₱3,200.00</span>',
                'status' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">Pending</span>',
                'actions' => '<button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm" onclick="openBillDetailsModal()"><i class="fas fa-eye"></i></button>'
            ],
            [
                'bill_no' => 'BILL-2024-003',
                'customer' => '<div class="text-sm font-medium text-gray-900 dark:text-white">Pedro Garcia</div><div class="text-xs text-gray-500">ACC-2024-003</div>',
                'period' => 'Jan 2024',
                'consumption' => '18 m³',
                'amount' => '<span class="font-semibold text-gray-900 dark:text-white">₱1,850.00</span>',
                'status' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Generated</span>',
                'actions' => '<button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm" onclick="openBillDetailsModal()"><i class="fas fa-eye"></i></button>'
            ]
        ];
    @endphp

    <x-table
        id="billGenerationTable"
        :headers="$billGenHeaders"
        :data="$billGenData"
        :searchable="false"
        :paginated="true"
        :pageSize="10"
        :actions="false"
    />
</div>
