<!-- Consumer Ledgers Tab -->
<div>
    <!-- Search and Filters -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <x-ui.search-bar placeholder="Search by consumer name, ID, meter number..." />
            </div>
            <div class="sm:w-64">
                <select id="sourceTypeFilterDropdown" onchange="filterBySourceType(this.value)" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Transaction Types</option>
                    <option value="billing">Billing</option>
                    <option value="payment">Payment</option>
                    <option value="adjustment">Adjustment</option>
                    <option value="penalty">Penalty</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div id="ledgerSummaryWrapper" class="mb-8">
        @include('components.ui.ledger.info-cards')
    </div>

    <!-- Consumer Ledgers Table -->
    <x-ui.action-functions 
        searchPlaceholder="Search ledger entries..."
        filterLabel="All Status"
        :filterOptions="[
            ['value' => 'Active', 'label' => 'Active'],
            ['value' => 'Inactive', 'label' => 'Inactive'],
            ['value' => 'Closed', 'label' => 'Closed']
        ]"
        :showDateFilter="true"
        :showExport="true"
        tableId="consumerLedgersTable"
    />

    @php
        $consumerLedgerHeaders = [
            ['key' => 'consumer', 'label' => 'Consumer', 'html' => true],
            ['key' => 'account_no', 'label' => 'Account No', 'html' => false],
            ['key' => 'meter_no', 'label' => 'Meter No', 'html' => false],
            ['key' => 'balance', 'label' => 'Current Balance', 'html' => true],
            ['key' => 'total_billed', 'label' => 'Total Billed', 'html' => true],
            ['key' => 'total_paid', 'label' => 'Total Paid', 'html' => true],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
            ['key' => 'actions', 'label' => 'Actions', 'html' => true],
        ];
    @endphp

    <x-table
        id="consumerLedgersTable"
        :headers="$consumerLedgerHeaders"
        :data="[]"
        :searchable="false"
        :paginated="true"
        :pageSize="15"
        :actions="false"
    />
</div>
