<!-- Documents & History Tab -->
<div id="documents-content" class="tab-content">
    <x-ui.action-functions 
        :showSearch="false"
        filterLabel="All Status"
        :filterOptions="[
            ['value' => 'Issued', 'label' => 'Issued'],
            ['value' => 'Processed', 'label' => 'Processed'],
            ['value' => 'Archived', 'label' => 'Archived']
        ]"
        :showDateFilter="false"
        :showExport="true"
        tableId="documents-table"
    />

    @php
        $documentsData = [
            ['id' => 1, 'type' => 'Billing Statement', 'details' => 'January 2024 Bill', 'date' => '2024-01-05', 'status' => '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">Issued</span>'],
            ['id' => 2, 'type' => 'Payment Receipt', 'details' => 'Payment Received - OTC', 'date' => '2024-01-15', 'status' => '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300">Processed</span>'],
            ['id' => 3, 'type' => 'Service Notice', 'details' => 'Annual Water Quality Report', 'date' => '2024-01-01', 'status' => '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300">Archived</span>'],
            ['id' => 4, 'type' => 'Billing Statement', 'details' => 'December 2023 Bill', 'date' => '2023-12-05', 'status' => '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">Issued</span>'],
        ];

        $documentsHeaders = [
            ['key' => 'type', 'label' => 'Type', 'html' => false],
            ['key' => 'details', 'label' => 'Details', 'html' => false],
            ['key' => 'date', 'label' => 'Date', 'html' => false],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
        ];
    @endphp

    <x-table
        id="documents-table"
        :headers="$documentsHeaders"
        :data="$documentsData"
        :searchable="true"
        :paginated="true"
        :actions="false"
        :page-size="10"
    />
</div>
