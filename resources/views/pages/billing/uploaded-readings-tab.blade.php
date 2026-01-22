<!-- Uploaded Readings Tab Content -->
<div class="space-y-6">
    <!-- Filters -->
    <x-ui.card>
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Period</label>
                <select id="uploadedPeriodFilter" onchange="loadUploadedReadings()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                    <option value="">All Periods</option>
                </select>
            </div>
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
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Status</label>
                <select id="uploadedStatusFilter" onchange="filterUploadedReadings()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                    <option value="">All Status</option>
                    <option value="unprocessed" selected>Unprocessed</option>
                    <option value="processed">Processed</option>
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
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unprocessed</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400" id="unprocessedCount">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Processed</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400" id="processedCount">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <i class="fas fa-check-double text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Can Process</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="canProcessCount">0</p>
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
            <div class="flex items-center gap-3">
                <input type="text" id="uploadedSearchInput" placeholder="Search account, name..."
                    onkeyup="filterUploadedReadings()"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm w-64">

                <!-- Selection Info & Process Button -->
                <div id="selectionActions" class="hidden flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        <span id="selectedCount">0</span> selected
                    </span>
                    <button onclick="processSelectedReadings()" id="processBtn"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-cogs mr-2"></i>Process Selected
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-3 py-3 text-center">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </th>
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
                        <td colspan="11" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
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

<!-- Processing Modal -->
<div id="processingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Processing Readings</h3>
            <p class="text-gray-600 dark:text-gray-400" id="processingStatus">Please wait...</p>
        </div>
    </div>
</div>

<script>
    let uploadedReadingsInitialized = false;
    let uploadedReadingsData = [];
    let filteredUploadedReadings = [];
    let uploadedCurrentPage = 1;
    const uploadedPerPage = 25;
    let selectedReadings = new Set();

    window.initUploadedReadingsTab = async function() {
        if (!uploadedReadingsInitialized) {
            // Load period filter first and wait for it to complete (needs to set default)
            await loadUploadedPeriodFilter();
            loadUploadedScheduleFilter();
            loadUploadedReaderFilter();
            loadUploadedReadings();
            uploadedReadingsInitialized = true;
        }
    };

    async function loadUploadedPeriodFilter() {
        try {
            const response = await fetch('/water-bills/billing-periods');
            const result = await response.json();
            if (result.periods) {
                const select = document.getElementById('uploadedPeriodFilter');
                if (!select) return;

                // Find current period (first one that is not closed, or the most recent)
                let currentPeriodId = null;
                const periods = result.periods;

                // Try to find the current active period (not closed)
                const activePeriod = periods.find(p => !p.is_closed);
                if (activePeriod) {
                    currentPeriodId = activePeriod.per_id;
                } else if (periods.length > 0) {
                    // If all periods are closed, use the most recent one
                    currentPeriodId = periods[0].per_id;
                }

                // Add options
                periods.forEach(period => {
                    const option = document.createElement('option');
                    option.value = period.per_id;
                    option.textContent = period.per_name;
                    if (period.per_id === currentPeriodId) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading periods filter:', error);
        }
    }

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
        const periodId = document.getElementById('uploadedPeriodFilter')?.value;
        const scheduleId = document.getElementById('uploadedScheduleFilter')?.value;
        const readerId = document.getElementById('uploadedReaderFilter')?.value;

        tbody.innerHTML = '<tr><td colspan="11" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Loading uploaded readings...</td></tr>';

        // Clear selection
        selectedReadings.clear();
        updateSelectionUI();

        try {
            let url = '/uploaded-readings';
            const params = new URLSearchParams();
            if (periodId) params.append('period_id', periodId);
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
                loadProcessingStats();
                filterUploadedReadings();
            } else {
                tbody.innerHTML = '<tr><td colspan="11" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No uploaded readings found.</td></tr>';
            }
        } catch (error) {
            console.error('Error loading uploaded readings:', error);
            tbody.innerHTML = '<tr><td colspan="11" class="px-4 py-8 text-center text-red-500">Error loading uploaded readings.</td></tr>';
        }
    }

    async function loadProcessingStats() {
        try {
            const periodId = document.getElementById('uploadedPeriodFilter')?.value;
            const scheduleId = document.getElementById('uploadedScheduleFilter')?.value;

            const params = new URLSearchParams();
            if (periodId) params.append('period_id', periodId);
            if (scheduleId) params.append('schedule_id', scheduleId);

            let url = '/uploaded-readings/processing-stats';
            if (params.toString()) url += '?' + params.toString();

            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                document.getElementById('unprocessedCount').textContent = result.stats.unprocessed || 0;
                document.getElementById('processedCount').textContent = result.stats.processed || 0;
                document.getElementById('canProcessCount').textContent = result.stats.can_process || 0;
            }
        } catch (error) {
            console.error('Error loading processing stats:', error);
        }
    }

    function updateUploadedStats(stats) {
        document.getElementById('totalUploadedCount').textContent = stats?.total || 0;
        document.getElementById('totalAmount').textContent = formatCurrency(stats?.total_amount || 0);
    }

    function filterUploadedReadings() {
        const search = document.getElementById('uploadedSearchInput').value.toLowerCase();
        const statusFilter = document.getElementById('uploadedStatusFilter').value;

        filteredUploadedReadings = uploadedReadingsData.filter(reading => {
            // Search filter
            const matchesSearch = !search ||
                (reading.account_no || '').toLowerCase().includes(search) ||
                (reading.customer_name || '').toLowerCase().includes(search) ||
                (reading.area_desc || '').toLowerCase().includes(search) ||
                (reading.meter_serial || '').toLowerCase().includes(search);

            // Status filter
            let matchesStatus = true;
            if (statusFilter === 'unprocessed') {
                matchesStatus = !reading.is_processed;
            } else if (statusFilter === 'processed') {
                matchesStatus = reading.is_processed;
            }

            return matchesSearch && matchesStatus;
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
            tbody.innerHTML = '<tr><td colspan="11" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No uploaded readings found.</td></tr>';
            updateUploadedPagination();
            return;
        }

        tbody.innerHTML = pageData.map(reading => {
            const consumption = reading.present_reading && reading.previous_reading
                ? (parseFloat(reading.present_reading) - parseFloat(reading.previous_reading)).toFixed(3)
                : '-';

            const statusBadges = [];
            if (reading.is_processed) {
                statusBadges.push('<span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400"><i class="fas fa-check mr-1"></i>Processed</span>');
            } else {
                if (reading.is_printed) {
                    statusBadges.push('<span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">Printed</span>');
                }
                if (reading.is_scanned) {
                    statusBadges.push('<span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">Scanned</span>');
                }
                if (statusBadges.length === 0) {
                    statusBadges.push('<span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Pending</span>');
                }
            }

            const isChecked = selectedReadings.has(reading.uploaded_reading_id);
            const canProcess = !reading.is_processed && reading.present_reading !== null && reading.previous_reading !== null && reading.connection_id !== null;
            const rowClass = reading.is_processed ? 'bg-green-50 dark:bg-green-900/10' : '';

            return `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 ${rowClass}">
                    <td class="px-3 py-3 text-center">
                        ${canProcess ? `
                            <input type="checkbox"
                                data-reading-id="${reading.uploaded_reading_id}"
                                ${isChecked ? 'checked' : ''}
                                onchange="toggleReadingSelection(${reading.uploaded_reading_id}, this.checked)"
                                class="reading-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        ` : `
                            <span class="text-gray-300 dark:text-gray-600"><i class="fas fa-check-circle"></i></span>
                        `}
                    </td>
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
        updateSelectAllCheckbox();
    }

    function toggleReadingSelection(readingId, isChecked) {
        if (isChecked) {
            selectedReadings.add(readingId);
        } else {
            selectedReadings.delete(readingId);
        }
        updateSelectionUI();
        updateSelectAllCheckbox();
    }

    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.reading-checkbox');
        checkboxes.forEach(cb => {
            const readingId = parseInt(cb.dataset.readingId);
            if (checkbox.checked) {
                selectedReadings.add(readingId);
                cb.checked = true;
            } else {
                selectedReadings.delete(readingId);
                cb.checked = false;
            }
        });
        updateSelectionUI();
    }

    function updateSelectAllCheckbox() {
        const checkboxes = document.querySelectorAll('.reading-checkbox');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');

        if (checkboxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
            return;
        }

        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;

        if (checkedCount === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === checkboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }

    function updateSelectionUI() {
        const selectionActions = document.getElementById('selectionActions');
        const selectedCount = document.getElementById('selectedCount');
        const processBtn = document.getElementById('processBtn');

        if (selectedReadings.size > 0) {
            selectionActions.classList.remove('hidden');
            selectedCount.textContent = selectedReadings.size;
            processBtn.disabled = false;
        } else {
            selectionActions.classList.add('hidden');
            selectedCount.textContent = '0';
        }
    }

    async function processSelectedReadings() {
        if (selectedReadings.size === 0) {
            if (window.showToast) {
                window.showToast('Warning', 'Please select at least one reading to process.', 'warning');
            } else {
                alert('Please select at least one reading to process.');
            }
            return;
        }

        // Confirm processing
        if (!confirm(`Are you sure you want to process ${selectedReadings.size} reading(s)? This will generate water bills for the selected readings.`)) {
            return;
        }

        // Show processing modal
        const modal = document.getElementById('processingModal');
        const statusText = document.getElementById('processingStatus');
        modal.classList.remove('hidden');
        statusText.textContent = `Processing ${selectedReadings.size} reading(s)...`;

        try {
            const response = await fetch('/uploaded-readings/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    reading_ids: Array.from(selectedReadings)
                })
            });

            const result = await response.json();

            // Hide modal
            modal.classList.add('hidden');

            if (result.success) {
                let message = result.message;
                if (result.skipped > 0) {
                    message += ` (${result.skipped} skipped)`;
                }

                if (window.showToast) {
                    window.showToast('Success', message, 'success');
                } else {
                    alert(message);
                }

                // Show errors if any
                if (result.errors && result.errors.length > 0) {
                    console.log('Processing errors:', result.errors);
                    result.errors.forEach(err => {
                        console.warn(`${err.account_no}: ${err.message}`);
                    });
                }

                // Reload data
                selectedReadings.clear();
                updateSelectionUI();
                loadUploadedReadings();

                // Refresh billing data if available
                if (typeof window.refreshBillingSummary === 'function') {
                    window.refreshBillingSummary();
                }
                document.dispatchEvent(new CustomEvent('bill-generated'));
            } else {
                if (window.showToast) {
                    window.showToast('Error', result.message || 'Failed to process readings.', 'error');
                } else {
                    alert(result.message || 'Failed to process readings.');
                }
            }
        } catch (error) {
            console.error('Error processing readings:', error);
            modal.classList.add('hidden');
            if (window.showToast) {
                window.showToast('Error', 'An error occurred while processing readings.', 'error');
            } else {
                alert('An error occurred while processing readings.');
            }
        }
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
