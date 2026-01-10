<!-- Rate Parents (Billing Periods) Tab -->
<div>
    <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-info-circle text-green-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-green-800 dark:text-green-300">
                <strong>Rate Parents:</strong> Represent billing periods with active rate structures. Consumers are assigned to a rate parent for a specific period.
            </div>
        </div>
    </div>

    <!-- Create New Rate Parent Button -->
    <div class="mb-6">
        <x-ui.button variant="primary" size="md" onclick="openCreateRateParentModal()" icon="fas fa-plus">
            Create Billing Period
        </x-ui.button>
    </div>

    <!-- Rate Parents Summary Cards -->
    @include('components.ui.rate.rate-parent-summary')

    <!-- Rate Parents Table -->
    <x-ui.action-functions 
        searchPlaceholder="Search by period name, code..."
        filterLabel="All Status"
        :filterOptions="[
            ['value' => 'Active', 'label' => 'Active'],
            ['value' => 'Inactive', 'label' => 'Inactive'],
            ['value' => 'Closed', 'label' => 'Closed']
        ]"
        :showDateFilter="true"
        :showExport="true"
        tableId="rateParentsTable"
    />

    @php
        $rateParentHeaders = [
            ['key' => 'period_code', 'label' => 'Period Code', 'html' => false],
            ['key' => 'period_name', 'label' => 'Period Name', 'html' => false],
            ['key' => 'start_date', 'label' => 'Start Date', 'html' => false],
            ['key' => 'end_date', 'label' => 'End Date', 'html' => false],
            ['key' => 'rate_details_count', 'label' => 'Rate Details', 'html' => true],
            ['key' => 'consumers_assigned', 'label' => 'Consumers Assigned', 'html' => true],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
            ['key' => 'actions', 'label' => 'Actions', 'html' => true],
        ];
    @endphp

    <x-table
        id="rateParentsTable"
        :headers="$rateParentHeaders"
        :data="[]"
        :searchable="false"
        :paginated="true"
        :pageSize="15"
        :actions="false"
    />
</div>

<script>
function openCreateRateParentModal() {
    alert('Opening Create Billing Period form...');
}

window.openCreateRateParentModal = openCreateRateParentModal;
</script>
