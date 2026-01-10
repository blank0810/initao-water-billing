<!-- Consumer Rates Tab -->
<div>
    <div class="bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-info-circle text-purple-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-purple-800 dark:text-purple-300">
                <strong>Consumer Rates:</strong> View and manage rate assignments to consumers. Each consumer is linked to a specific rate parent (billing period).
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <x-ui.search-bar placeholder="Search by consumer name, account no, meter number..." />
            </div>
            <div class="sm:w-64">
                <select id="consumer-rate-filter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500">
                    <option value="">All Rate Parents</option>
                    <option value="BP-2024-01">Jan 2024 - Residential</option>
                    <option value="BP-2024-02">Feb 2024 - Residential</option>
                    <option value="BP-2024-03">Mar 2024 - Commercial</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Consumer Rates Table -->
    <x-ui.action-functions 
        searchPlaceholder="Search rate assignments..."
        filterLabel="All Status"
        :filterOptions="[
            ['value' => 'Active', 'label' => 'Active'],
            ['value' => 'Inactive', 'label' => 'Inactive'],
            ['value' => 'Changed', 'label' => 'Rate Changed']
        ]"
        :showDateFilter="true"
        :showExport="true"
        tableId="consumerRatesTable"
    />

    @php
        $consumerRateHeaders = [
            ['key' => 'consumer', 'label' => 'Consumer', 'html' => true],
            ['key' => 'account_no', 'label' => 'Account No', 'html' => false],
            ['key' => 'meter_no', 'label' => 'Meter No', 'html' => false],
            ['key' => 'rate_parent', 'label' => 'Assigned Rate (Period)', 'html' => false],
            ['key' => 'effective_from', 'label' => 'Effective From', 'html' => false],
            ['key' => 'effective_to', 'label' => 'Effective To', 'html' => false],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
            ['key' => 'actions', 'label' => 'Actions', 'html' => true],
        ];
    @endphp

    <x-table
        id="consumerRatesTable"
        :headers="$consumerRateHeaders"
        :data="[]"
        :searchable="false"
        :paginated="true"
        :pageSize="15"
        :actions="false"
    />
</div>
