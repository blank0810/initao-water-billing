<!-- Download Bills Tab Content (Read-Only) -->
<div>
    <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-lock text-orange-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-orange-800 dark:text-orange-300">
                <strong>Read-Only Reports:</strong> Download billing outputs (individual bills, summaries, reports) in PDF or spreadsheet format. No data editing allowed.
            </div>
        </div>
    </div>

    <x-ui.action-functions 
        searchPlaceholder="Search by filename, report type..."
        filterLabel="All Formats"
        :filterOptions="[
            ['value' => 'PDF', 'label' => 'PDF'],
            ['value' => 'CSV', 'label' => 'CSV'],
            ['value' => 'Excel', 'label' => 'Excel']
        ]"
        :showDateFilter="true"
        :showExport="true"
        tableId="downloadHistoryTable"
    />

    <!-- Download Controls Panel -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-download mr-2"></i>Export Billing Reports
        </h3>
        
        <div class="flex flex-col lg:flex-row gap-3 items-end">
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Report Type</label>
                <select id="download-report-type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="individual">Individual Customer Bill</option>
                    <option value="summary">Billing Period Summary</option>
                    <option value="barangay">Barangay-wise Report</option>
                    <option value="zone">Zone-wise Report</option>
                    <option value="collection">Collection Reference Report</option>
                    <option value="audit">Audit Report (COA)</option>
                </select>
            </div>
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer/Area</label>
                <select id="download-customer" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Customers</option>
                    <option value="1001">Juan Dela Cruz</option>
                    <option value="1002">Maria Santos</option>
                    <option value="1003">Pedro Garcia</option>
                    <option value="barangay-1">Barangay Poblacion</option>
                    <option value="zone-a">Zone A</option>
                </select>
            </div>
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Billing Period</label>
                <select id="download-period" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Periods</option>
                    <option value="BP-2024-01">January 2024 (Closed)</option>
                    <option value="BP-2024-02">February 2024 (Open)</option>
                    <option value="BP-2024-03">March 2024 (Posted)</option>
                </select>
            </div>
            <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Download Format</label>
                <select id="export-format" onchange="downloadBills(this.value)" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Format --</option>
                    <option value="pdf">📄 PDF</option>
                    <option value="csv">📋 CSV</option>
                    <option value="excel">📊 Excel</option>
                </select>
            </div>
        </div>
    </div>

    @php
        $downloadHeaders = [
            ['key' => 'filename', 'label' => 'File Name', 'html' => false],
            ['key' => 'report_type', 'label' => 'Report Type', 'html' => false],
            ['key' => 'download_date', 'label' => 'Downloaded', 'html' => false],
            ['key' => 'format', 'label' => 'Format', 'html' => true],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
            ['key' => 'actions', 'label' => 'Actions', 'html' => true],
        ];

        $downloadData = [
            [
                'filename' => 'Billing_Summary_Feb2024.pdf',
                'report_type' => 'Billing Period Summary',
                'download_date' => 'Jan 29, 2024 @ 2:45 PM',
                'format' => '<span class="px-2 py-1 text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded">PDF</span>',
                'status' => '<span class="px-2 py-1 text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded">Completed</span>',
                'actions' => '<button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm" onclick="redownloadFile(this)"><i class="fas fa-redo mr-1"></i>Re-download</button>'
            ],
            [
                'filename' => 'Customer_Bills_Report.csv',
                'report_type' => 'Individual Customer Bill',
                'download_date' => 'Jan 28, 2024 @ 10:15 AM',
                'format' => '<span class="px-2 py-1 text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded">CSV</span>',
                'status' => '<span class="px-2 py-1 text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded">Completed</span>',
                'actions' => '<button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm" onclick="redownloadFile(this)"><i class="fas fa-redo mr-1"></i>Re-download</button>'
            ]
        ];
    @endphp

    <x-table
        id="downloadHistoryTable"
        :headers="$downloadHeaders"
        :data="$downloadData"
        :searchable="false"
        :paginated="true"
        :pageSize="10"
        :actions="false"
    />
</div>
