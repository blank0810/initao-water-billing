<!-- Rate Details Tab -->
<div>
    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-blue-800 dark:text-blue-300">
                <strong>Rate Details:</strong> Define increment ranges and pricing for each rate parent. Each detail specifies consumption ranges and corresponding charges.
            </div>
        </div>
    </div>

    <!-- Filter by Rate Parent -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Rate Parent (Billing Period)</label>
                <select id="rate-parent-filter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">-- All Rate Parents --</option>
                    <option value="BP-2024-01">January 2024 - Residential Standard</option>
                    <option value="BP-2024-02">February 2024 - Residential Standard</option>
                    <option value="BP-2024-03">March 2024 - Commercial Premium</option>
                </select>
            </div>
            <div class="sm:w-64">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">&nbsp;</label>
                <x-ui.button variant="secondary" size="md" onclick="addRateDetail()" icon="fas fa-plus">
                    Add Rate Detail
                </x-ui.button>
            </div>
        </div>
    </div>

    <!-- Rate Details Table -->
    <x-ui.action-functions 
        searchPlaceholder="Search by increment range..."
        filterLabel="All Types"
        :filterOptions="[
            ['value' => 'Standard', 'label' => 'Standard'],
            ['value' => 'Premium', 'label' => 'Premium'],
            ['value' => 'Residential', 'label' => 'Residential']
        ]"
        :showDateFilter="false"
        :showExport="true"
        tableId="rateDetailsTable"
    />

    @php
        $rateDetailHeaders = [
            ['key' => 'rate_parent', 'label' => 'Rate Parent', 'html' => false],
            ['key' => 'increment_from', 'label' => 'From (m³)', 'html' => false],
            ['key' => 'increment_to', 'label' => 'To (m³)', 'html' => false],
            ['key' => 'amount', 'label' => 'Amount per m³', 'html' => true],
            ['key' => 'surcharge', 'label' => 'Surcharge', 'html' => true],
            ['key' => 'total_rate', 'label' => 'Total Rate', 'html' => true],
            ['key' => 'effective_from', 'label' => 'Effective From', 'html' => false],
            ['key' => 'actions', 'label' => 'Actions', 'html' => true],
        ];
    @endphp

    <x-table
        id="rateDetailsTable"
        :headers="$rateDetailHeaders"
        :data="[]"
        :searchable="false"
        :paginated="true"
        :pageSize="15"
        :actions="false"
    />
</div>

<script>
function addRateDetail() {
    alert('Opening Add Rate Detail form...');
}

window.addRateDetail = addRateDetail;
</script>
