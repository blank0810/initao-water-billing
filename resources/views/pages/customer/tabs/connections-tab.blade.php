<!-- Service Connections Tab -->
<div id="connections-content" class="tab-content hidden">
    <x-ui.action-functions 
        :showSearch="false"
        filterLabel="All Status"
        :filterOptions="[
            ['value' => 'Active', 'label' => 'Active'],
            ['value' => 'Inactive', 'label' => 'Inactive'],
            ['value' => 'Suspended', 'label' => 'Suspended']
        ]"
        :showDateFilter="false"
        :showExport="true"
        tableId="connections-table"
    />

    @php
        $connectionsData = [
            ['id' => 1, 'account_no' => 'ACC-001', 'customer_type' => 'Residential', 'meter_reader' => 'Zone A / North District', 'meter_no' => 'MTR-001', 'date_installed' => '2022-03-15', 'status' => '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">Active</span>'],
            ['id' => 2, 'account_no' => 'ACC-002', 'customer_type' => 'Commercial', 'meter_reader' => 'Zone B / South District', 'meter_no' => 'MTR-045', 'date_installed' => '2021-06-22', 'status' => '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">Active</span>'],
        ];

        $connectionsHeaders = [
            ['key' => 'account_no', 'label' => 'Account No', 'html' => false],
            ['key' => 'customer_type', 'label' => 'Customer Type', 'html' => false],
            ['key' => 'meter_reader', 'label' => 'Meter Reader & Area', 'html' => false],
            ['key' => 'meter_no', 'label' => 'Meter No', 'html' => false],
            ['key' => 'date_installed', 'label' => 'Date Installed', 'html' => false],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
        ];
    @endphp

    <x-table
        id="connections-table"
        :headers="$connectionsHeaders"
        :data="$connectionsData"
        :searchable="true"
        :paginated="true"
        :actions="false"
        :page-size="10"
    />
</div>
