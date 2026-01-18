<!-- Uploaded Readings Tab Content -->
<div class="space-y-6">
    <!-- Filters -->
    <x-ui.card>
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Schedule</label>
                <select id="uploadedScheduleFilter" onchange="loadUploadedReadings()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                    <option value="">All Schedules</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Reader</label>
                <select id="uploadedReaderFilter" onchange="loadUploadedReadings()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                    <option value="">All Readers</option>
                </select>
            </div>
            <div>
                <button onclick="loadUploadedReadings()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
        </div>
    </x-ui.card>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <i class="fas fa-upload text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Uploaded</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="totalUploadedCount">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <i class="fas fa-print text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Printed</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="printedCount">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <i class="fas fa-qrcode text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Scanned</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="scannedCount">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <i class="fas fa-peso-sign text-amber-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="totalAmount">0.00</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Uploaded Readings Table -->
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-cloud-upload-alt mr-2 text-blue-600"></i>Uploaded Readings
            </h3>
            <div class="flex items-center gap-2">
                <input type="text" id="uploadedSearchInput" placeholder="Search account, name..."
                    onkeyup="filterUploadedReadings()"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm w-64">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Prev Reading</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Present Reading</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Consumption</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Site Amount</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Computed</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody id="uploadedReadingsBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Loading uploaded readings...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span id="uploadedShowingFrom">0</span> to <span id="uploadedShowingTo">0</span> of <span id="uploadedTotalRecords">0</span> records
            </div>
            <div class="flex gap-2">
                <button onclick="prevUploadedPage()" id="uploadedPrevBtn" disabled class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <button onclick="nextUploadedPage()" id="uploadedNextBtn" disabled class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </div>
        </div>
    </x-ui.card>
</div>

<script>
    let uploadedReadingsInitialized = false;
    let uploadedReadingsData = [];
    let filteredUploadedReadings = [];
    let uploadedCurrentPage = 1;
    const uploadedPerPage = 25;

    window.initUploadedReadingsTab = function() {
        if (!uploadedReadingsInitialized) {
            loadUploadedScheduleFilter();
            loadUploadedReaderFilter();
            loadUploadedReadings();
            uploadedReadingsInitialized = true;
        }
    };

    async function loadUploadedScheduleFilter() {
        try {
            const response = await fetch('/reading-schedules');
            const result = await response.json();
            if (result.success) {
                const select = document.getElementById('uploadedScheduleFilter');
                if (!select) return;

                result.data.forEach(schedule => {
                    const option = document.createElement('option');
                    option.value = schedule.schedule_id;
                    option.textContent = `${schedule.area_name} - ${schedule.period_name}`;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading schedules filter:', error);
        }
    }

    async function loadUploadedReaderFilter() {
        try {
            const response = await fetch('/reading-schedules/meter-readers');
            const result = await response.json();
            if (result.success) {
                const select = document.getElementById('uploadedReaderFilter');
                if (!select) return;

                result.data.forEach(reader => {
                    const option = document.createElement('option');
                    option.value = reader.id;
                    option.textContent = reader.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading readers filter:', error);
        }
    }

    async function loadUploadedReadings() {
        const tbody = document.getElementById('uploadedReadingsBody');
        const scheduleId = document.getElementById('uploadedScheduleFilter')?.value;
        const readerId = document.getElementById('uploadedReaderFilter')?.value;

        tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Loading uploaded readings...</td></tr>';

        try {
            let url = '/uploaded-readings';
            const params = new URLSearchParams();
            if (scheduleId) params.append('schedule_id', scheduleId);
            if (readerId) params.append('user_id', readerId);
            if (params.toString()) url += '?' + params.toString();

            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                uploadedReadingsData = result.data;
                filteredUploadedReadings = [...uploadedReadingsData];
                uploadedCurrentPage = 1;
                updateUploadedStats(result.stats);
                renderUploadedReadings();
            } else {
                tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No uploaded readings found.</td></tr>';
            }
        } catch (error) {
            console.error('Error loading uploaded readings:', error);
            tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-8 text-center text-red-500">Error loading uploaded readings.</td></tr>';
        }
    }

    function updateUploadedStats(stats) {
        document.getElementById('totalUploadedCount').textContent = stats?.total || 0;
        document.getElementById('printedCount').textContent = stats?.printed || 0;
        document.getElementById('scannedCount').textContent = stats?.scanned || 0;
        document.getElementById('totalAmount').textContent = formatCurrency(stats?.total_amount || 0);
    }

    function filterUploadedReadings() {
        const search = document.getElementById('uploadedSearchInput').value.toLowerCase();

        filteredUploadedReadings = uploadedReadingsData.filter(reading => {
            return (reading.account_no || '').toLowerCase().includes(search) ||
                   (reading.customer_name || '').toLowerCase().includes(search) ||
                   (reading.area_desc || '').toLowerCase().includes(search) ||
                   (reading.meter_serial || '').toLowerCase().includes(search);
        });

        uploadedCurrentPage = 1;
        renderUploadedReadings();
    }

    function renderUploadedReadings() {
        const tbody = document.getElementById('uploadedReadingsBody');
        const start = (uploadedCurrentPage - 1) * uploadedPerPage;
        const end = start + uploadedPerPage;
        const pageData = filteredUploadedReadings.slice(start, end);

        if (pageData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No uploaded readings found.</td></tr>';
            updateUploadedPagination();
            return;
        }

        tbody.innerHTML = pageData.map(reading => {
            const consumption = reading.present_reading && reading.previous_reading
                ? (parseFloat(reading.present_reading) - parseFloat(reading.previous_reading)).toFixed(3)
                : '-';

            const statusBadges = [];
            if (reading.is_printed) {
                statusBadges.push('<span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Printed</span>');
            }
            if (reading.is_scanned) {
                statusBadges.push('<span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">Scanned</span>');
            }
            if (statusBadges.length === 0) {
                statusBadges.push('<span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">Pending</span>');
            }

            return `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3">
                        <span class="font-medium text-gray-900 dark:text-white">${reading.account_no || '-'}</span>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-gray-900 dark:text-white">${reading.customer_name || '-'}</p>
                        <p class="text-xs text-gray-500">${reading.address || '-'}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">${reading.area_desc || '-'}</td>
                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">${reading.previous_reading ?? '-'}</td>
                    <td class="px-4 py-3 text-center font-medium text-gray-900 dark:text-white">${reading.present_reading ?? '-'}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-medium ${parseFloat(consumption) > 0 ? 'text-blue-600' : 'text-gray-500'}">${consumption}</span>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">${reading.site_bill_amount ? formatCurrency(reading.site_bill_amount) : '-'}</td>
                    <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">${reading.computed_amount ? formatCurrency(reading.computed_amount) : '-'}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex flex-wrap gap-1 justify-center">${statusBadges.join('')}</div>
                    </td>
                    <td class="px-4 py-3 text-center text-xs text-gray-500">${reading.reading_date || '-'}</td>
                </tr>
            `;
        }).join('');

        updateUploadedPagination();
    }

    function updateUploadedPagination() {
        const total = filteredUploadedReadings.length;
        const start = total === 0 ? 0 : (uploadedCurrentPage - 1) * uploadedPerPage + 1;
        const end = Math.min(uploadedCurrentPage * uploadedPerPage, total);
        const maxPage = Math.ceil(total / uploadedPerPage);

        document.getElementById('uploadedShowingFrom').textContent = start;
        document.getElementById('uploadedShowingTo').textContent = end;
        document.getElementById('uploadedTotalRecords').textContent = total;

        document.getElementById('uploadedPrevBtn').disabled = uploadedCurrentPage <= 1;
        document.getElementById('uploadedNextBtn').disabled = uploadedCurrentPage >= maxPage;
    }

    function prevUploadedPage() {
        if (uploadedCurrentPage > 1) {
            uploadedCurrentPage--;
            renderUploadedReadings();
        }
    }

    function nextUploadedPage() {
        const maxPage = Math.ceil(filteredUploadedReadings.length / uploadedPerPage);
        if (uploadedCurrentPage < maxPage) {
            uploadedCurrentPage++;
            renderUploadedReadings();
        }
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2
        }).format(value);
    }
</script>
