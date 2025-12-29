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

    <div class="space-y-6">
        <!-- Download Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-download mr-2"></i>Export Billing Reports
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Report Type</label>
                    <select id="download-report-type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="individual">Individual Customer Bill</option>
                        <option value="summary">Billing Period Summary</option>
                        <option value="barangay">Barangay-wise Report</option>
                        <option value="zone">Zone-wise Report</option>
                        <option value="collection">Collection Reference Report</option>
                        <option value="audit">Audit Report (COA)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer/Area</label>
                    <select id="download-customer" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">All Customers</option>
                        <option value="1001">Juan Dela Cruz</option>
                        <option value="1002">Maria Santos</option>
                        <option value="1003">Pedro Garcia</option>
                        <option value="barangay-1">Barangay Poblacion</option>
                        <option value="zone-a">Zone A</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Billing Period</label>
                    <select id="download-period" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">All Periods</option>
                        <option value="BP-2024-01">January 2024 (Closed)</option>
                        <option value="BP-2024-02">February 2024 (Open)</option>
                        <option value="BP-2024-03">March 2024 (Posted)</option>
                    </select>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Select Export Format:</h4>
                <div class="flex flex-wrap gap-3">
                    <button onclick="downloadBills('pdf')" class="flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <i class="fas fa-file-pdf mr-2"></i>Export as PDF
                    </button>
                    <button onclick="downloadBills('excel')" class="flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        <i class="fas fa-file-excel mr-2"></i>Export as Excel
                    </button>
                    <button onclick="downloadBills('csv')" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class="fas fa-file-csv mr-2"></i>Export as CSV
                    </button>
                </div>
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>
                Downloads are logged for audit purposes. All exported reports include watermark and generation timestamp.
            </div>
        </div>

        <!-- Download History (Audit Trail) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-history mr-2"></i>Download History (Audit Trail)
            </h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date/Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Report Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Details</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Format</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">User</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">2024-01-29 14:30</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Billing Summary</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">All Customers - Jan 2024</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <i class="fas fa-file-pdf mr-1"></i>PDF
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">Admin</td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 p-2 rounded" title="Re-download">
                                    <i class="fas fa-download"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">2024-01-28 16:15</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Individual Bill</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">Juan Dela Cruz - Jan 2024</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-file-excel mr-1"></i>Excel
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">Billing Clerk</td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 p-2 rounded" title="Re-download">
                                    <i class="fas fa-download"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">2024-01-27 10:45</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Audit Report</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">COA Compliance Report - Jan 2024</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <i class="fas fa-file-pdf mr-1"></i>PDF
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">Admin</td>
                            <td class="px-4 py-3 text-center">
                                <button class="text-blue-600 hover:text-blue-900 dark:text-blue-400 p-2 rounded" title="Re-download">
                                    <i class="fas fa-download"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
