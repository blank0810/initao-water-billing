<!-- Downloads Tab Content -->
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Reading Lists Download -->
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-file-csv mr-2 text-purple-600"></i>Meter Reading Lists
                </h3>
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                    CSV Format
                </span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Download lists of service connections for meter readers. These lists include previous readings and connection details.
            </p>

            <div class="space-y-3">
                <div class="flex items-end gap-2 mb-4">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Period</label>
                        <select id="downloadPeriodFilter" onchange="loadDownloadSchedules()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            <option value="">All Active Periods</option>
                        </select>
                    </div>
                    <button onclick="loadDownloadSchedules()" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Area / Period</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody id="downloadSchedulesBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading schedules...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </x-ui.card>

        <!-- Other Downloads (Placeholders) -->
        <div class="space-y-6">
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-file-pdf mr-2 text-red-600"></i>Reports & Templates
                    </h3>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 border border-gray-100 dark:border-gray-700 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg mr-3">
                                <i class="fas fa-file-pdf text-red-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Monthly Billing Summary</p>
                                <p class="text-xs text-gray-500">Consolidated report for the current period</p>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-3 border border-gray-100 dark:border-gray-700 rounded-lg opacity-50">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg mr-3">
                                <i class="fas fa-file-excel text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Consumer Master List</p>
                                <p class="text-xs text-gray-500">Excel export of all active connections</p>
                            </div>
                        </div>
                        <span class="text-xs font-medium text-gray-400 italic">Coming Soon</span>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>

<script>
    let downloadsInitialized = false;

    window.initDownloadsTab = function() {
        if (!downloadsInitialized) {
            loadDownloadPeriods();
            loadDownloadSchedules();
            downloadsInitialized = true;
        }
    };

    async function loadDownloadPeriods() {
        try {
            const response = await fetch('/reading-schedules/periods');
            const result = await response.json();
            if (result.success) {
                const select = document.getElementById('downloadPeriodFilter');
                if (!select) return;
                
                result.data.forEach(period => {
                    const option = document.createElement('option');
                    option.value = period.id;
                    option.textContent = period.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading periods for download:', error);
        }
    }

    async function loadDownloadSchedules() {
        const tbody = document.getElementById('downloadSchedulesBody');
        const periodId = document.getElementById('downloadPeriodFilter').value;
        
        tbody.innerHTML = '<tr><td colspan="2" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Loading schedules...</td></tr>';

        try {
            let url = '/reading-schedules?status=pending,in_progress';
            if (periodId) {
                url = `/reading-schedules/by-period/${periodId}`;
            }

            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                const schedules = result.data.filter(s => s.status !== 'completed');
                
                if (schedules.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="2" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No active reading schedules found.</td></tr>';
                    return;
                }

                tbody.innerHTML = schedules.map(s => `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900 dark:text-white">${s.area_name}</p>
                            <p class="text-xs text-gray-500">${s.period_name} • ${s.reader_name}</p>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="/reading-schedules/${s.schedule_id}/download" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 rounded shadow-sm transition-colors">
                                <i class="fas fa-download mr-1.5"></i> Download CSV
                            </a>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (error) {
            console.error('Error loading schedules for download:', error);
            tbody.innerHTML = '<tr><td colspan="2" class="px-4 py-8 text-center text-red-500">Error loading schedules.</td></tr>';
        }
    }
</script>