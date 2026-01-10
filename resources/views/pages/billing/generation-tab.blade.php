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

    <!-- Bill Generation Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-cogs mr-2"></i>Generate Bills
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Billing Period</label>
                <select id="generation-period" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                    <option value="BP-2024-02">February 2024 (Open)</option>
                    <option value="BP-2024-01">January 2024 (Closed)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rate Structure (Reference)</label>
                <select disabled class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400">
                    <option>Residential Standard (Active)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Rate applied from Master Files</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Generation Scope</label>
                <select id="generation-scope" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                    <option value="all">All Customers</option>
                    <option value="zone">By Zone</option>
                    <option value="barangay">By Barangay</option>
                    <option value="individual">Individual Customer</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="openGenerateBillModal()" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                <i class="fas fa-bolt mr-2"></i>Generate Bills
            </button>
            <button onclick="openPreviewBillModal()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                <i class="fas fa-eye mr-2"></i>Preview
            </button>
            <span class="text-sm text-gray-500 dark:text-gray-400 ml-auto">
                <i class="fas fa-clock mr-1"></i>Last generated: Jan 29, 2024
            </span>
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
    @endphp

    <x-table
        id="billGenerationTable"
        :headers="$billGenHeaders"
        :data="[]"
        :searchable="false"
        :paginated="true"
        :pageSize="10"
        :actions="false"
    />
</div>
