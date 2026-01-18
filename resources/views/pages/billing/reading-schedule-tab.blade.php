<!-- Reading Schedule Tab Content -->
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Schedules</div>
            <div id="stat-total" class="text-2xl font-bold text-gray-900 dark:text-white">0</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <div class="text-sm font-medium text-yellow-500">Pending</div>
            <div id="stat-pending" class="text-2xl font-bold text-yellow-600">0</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <div class="text-sm font-medium text-blue-500">In Progress</div>
            <div id="stat-in-progress" class="text-2xl font-bold text-blue-600">0</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <div class="text-sm font-medium text-green-500">Completed</div>
            <div id="stat-completed" class="text-2xl font-bold text-green-600">0</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <div class="text-sm font-medium text-red-500">Delayed</div>
            <div id="stat-delayed" class="text-2xl font-bold text-red-600">0</div>
        </div>
    </div>

    <!-- Reading Schedules Table -->
    <x-ui.card>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reading Schedules</h3>
            <div class="flex space-x-2">
                <select id="scheduleStatusFilter" onchange="filterSchedules()" class="text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="delayed">Delayed</option>
                </select>
                <button onclick="openScheduleModal()" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
                    <i class="fas fa-plus mr-1"></i> Add Schedule
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Area</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reader</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Scheduled Dates</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Progress</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="schedulesTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>

<!-- Schedule Modal -->
<div id="scheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeScheduleModal()"></div>
        <div class="relative z-10 w-full max-w-lg p-6 mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 id="scheduleModalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Add Reading Schedule</h3>
                <button onclick="closeScheduleModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="scheduleForm" onsubmit="saveSchedule(event)">
                <input type="hidden" id="scheduleId" value="">

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="schedulePeriodId" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Period</label>
                        <select id="schedulePeriodId" name="period_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select Period</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="scheduleAreaId" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Area</label>
                        <select id="scheduleAreaId" name="area_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select Area</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Assigned Meter Reader</label>
                    <div id="scheduleReaderDisplay" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300">
                        <span class="text-gray-400 dark:text-gray-500">Select an area first</span>
                    </div>
                    <input type="hidden" id="scheduleReaderId" name="reader_id" value="">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="scheduledStartDate" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                        <input type="date" id="scheduledStartDate" name="scheduled_start_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label for="scheduledEndDate" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                        <input type="date" id="scheduledEndDate" name="scheduled_end_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="scheduleTotalMeters" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Total Meters (Optional)</label>
                    <input type="number" id="scheduleTotalMeters" name="total_meters" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter total meters to read">
                </div>

                <div class="mb-4">
                    <label for="scheduleNotes" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Notes (Optional)</label>
                    <textarea id="scheduleNotes" name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Additional notes..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeScheduleModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Schedule Modal -->
<div id="completeScheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeCompleteModal()"></div>
        <div class="relative z-10 w-full max-w-md p-6 mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Complete Schedule</h3>
                <button onclick="closeCompleteModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="completeScheduleForm" onsubmit="submitCompleteSchedule(event)">
                <input type="hidden" id="completeScheduleId" value="">

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="metersRead" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Meters Read</label>
                        <input type="number" id="metersRead" name="meters_read" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label for="metersMissed" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Meters Missed</label>
                        <input type="number" id="metersMissed" name="meters_missed" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCompleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">Complete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ============================================================================
// Reading Schedule Management
// ============================================================================

let schedulesData = [];
let scheduleAreasData = [];
let schedulePeriodsData = [];
let schedulesInitialized = false;

// Initialize when the schedule tab is shown
window.initSchedulesTab = function() {
    if (!schedulesInitialized) {
        loadSchedules();
        loadScheduleDropdowns();
        schedulesInitialized = true;
    }
};

// Auto-initialize on page load if this tab content is visible
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        if (document.getElementById('content-schedules') && !document.getElementById('content-schedules').classList.contains('hidden')) {
            window.initSchedulesTab();
        }
    }, 100);
});

// ============================================================================
// Schedule CRUD Functions
// ============================================================================

async function loadSchedules() {
    try {
        const status = document.getElementById('scheduleStatusFilter')?.value || '';
        const url = status ? `/reading-schedules?status=${status}` : '/reading-schedules';

        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            schedulesData = result.data;
            renderSchedulesTable(result.data);
            updateScheduleStats(result.stats);
        }
    } catch (error) {
        console.error('Error loading schedules:', error);
        document.getElementById('schedulesTableBody').innerHTML =
            '<tr><td colspan="7" class="px-4 py-4 text-center text-red-500">Error loading schedules</td></tr>';
    }
}

function filterSchedules() {
    loadSchedules();
}

function updateScheduleStats(stats) {
    if (!stats) return;

    document.getElementById('stat-total').textContent = stats.total_schedules || 0;
    document.getElementById('stat-pending').textContent = stats.pending || 0;
    document.getElementById('stat-in-progress').textContent = stats.in_progress || 0;
    document.getElementById('stat-completed').textContent = stats.completed || 0;
    document.getElementById('stat-delayed').textContent = stats.delayed || 0;
}

function renderSchedulesTable(schedules) {
    const tbody = document.getElementById('schedulesTableBody');

    if (!schedules || schedules.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No schedules found</td></tr>';
        return;
    }

    tbody.innerHTML = schedules.map(schedule => {
        const scheduledDates = `${schedule.scheduled_start_date} - ${schedule.scheduled_end_date}`;
        const progressPercent = schedule.completion_percentage || 0;
        const progressBar = schedule.total_meters > 0
            ? `<div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                   <div class="bg-blue-600 h-2.5 rounded-full" style="width: ${progressPercent}%"></div>
               </div>
               <span class="text-xs text-gray-500">${schedule.meters_read}/${schedule.total_meters}</span>`
            : '<span class="text-xs text-gray-400">N/A</span>';

        return `
            <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-2 text-gray-900 dark:text-white">${escapeHtmlSchedule(schedule.period_name)}</td>
                <td class="px-4 py-2 text-gray-900 dark:text-white">${escapeHtmlSchedule(schedule.area_name)}</td>
                <td class="px-4 py-2 text-gray-900 dark:text-white">${escapeHtmlSchedule(schedule.reader_name)}</td>
                <td class="px-4 py-2 text-gray-500 dark:text-gray-400 text-xs">${scheduledDates}</td>
                <td class="px-4 py-2">${progressBar}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${schedule.status_class}">
                        ${schedule.status_label}
                    </span>
                </td>
                <td class="px-4 py-2">
                    <div class="flex space-x-2">
                        ${getScheduleActionButtons(schedule)}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function getScheduleActionButtons(schedule) {
    let buttons = '';

    if (schedule.status === 'pending') {
        buttons += `
            <button onclick="startSchedule(${schedule.schedule_id})" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300" title="Start">
                <i class="fas fa-play"></i>
            </button>
            <button onclick="editSchedule(${schedule.schedule_id})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
        `;
    }

    if (schedule.status === 'in_progress') {
        buttons += `
            <button onclick="openCompleteModal(${schedule.schedule_id})" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300" title="Complete">
                <i class="fas fa-check-circle"></i>
            </button>
            <button onclick="markAsDelayed(${schedule.schedule_id})" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300" title="Mark Delayed">
                <i class="fas fa-exclamation-triangle"></i>
            </button>
        `;
    }

    if (schedule.status !== 'completed') {
        buttons += `
            <button onclick="deleteSchedule(${schedule.schedule_id})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        `;
    }

    if (schedule.status === 'completed') {
        buttons += `
            <button onclick="viewScheduleDetails(${schedule.schedule_id})" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300" title="View Details">
                <i class="fas fa-eye"></i>
            </button>
        `;
    }

    return buttons;
}

async function loadScheduleDropdowns() {
    try {
        const [areasRes, periodsRes] = await Promise.all([
            fetch('/reading-schedules/areas'),
            fetch('/reading-schedules/periods')
        ]);

        const [areasResult, periodsResult] = await Promise.all([
            areasRes.json(),
            periodsRes.json()
        ]);

        if (areasResult.success) {
            scheduleAreasData = areasResult.data;
            populateScheduleAreaDropdown(areasResult.data);
        }

        if (periodsResult.success) {
            schedulePeriodsData = periodsResult.data;
            populateSchedulePeriodDropdown(periodsResult.data);
        }
    } catch (error) {
        console.error('Error loading dropdowns:', error);
    }
}

function populateScheduleAreaDropdown(areas) {
    const select = document.getElementById('scheduleAreaId');
    if (!select) return;

    select.innerHTML = '<option value="">Select Area</option>';
    areas.forEach(area => {
        const option = document.createElement('option');
        option.value = area.id;
        option.textContent = escapeHtmlSchedule(area.name);
        option.dataset.assignedReaderId = area.assigned_reader_id || '';
        option.dataset.assignedReaderName = area.assigned_reader_name || '';
        select.appendChild(option);
    });

    // Add change event listener for auto-populating reader
    select.removeEventListener('change', handleAreaChange);
    select.addEventListener('change', handleAreaChange);
}

/**
 * Handle area selection change - auto-populate meter reader display
 */
function handleAreaChange(event) {
    const selectedOption = event.target.selectedOptions[0];
    const readerDisplay = document.getElementById('scheduleReaderDisplay');
    const readerInput = document.getElementById('scheduleReaderId');

    if (!selectedOption || selectedOption.value === '') {
        readerDisplay.innerHTML = '<span class="text-gray-400 dark:text-gray-500">Select an area first</span>';
        readerInput.value = '';
        return;
    }

    const readerId = selectedOption.dataset.assignedReaderId;
    const readerName = selectedOption.dataset.assignedReaderName;

    if (readerId && readerName) {
        readerDisplay.textContent = readerName;
        readerInput.value = readerId;
    } else {
        readerDisplay.innerHTML = '<span class="text-yellow-500 dark:text-yellow-400">No reader assigned to this area</span>';
        readerInput.value = '';
    }
}

function populateSchedulePeriodDropdown(periods) {
    const select = document.getElementById('schedulePeriodId');
    if (!select) return;

    select.innerHTML = '<option value="">Select Period</option>';
    periods.forEach(period => {
        select.innerHTML += `<option value="${period.id}">${escapeHtmlSchedule(period.name)}</option>`;
    });
}

function openScheduleModal(scheduleId = null) {
    document.getElementById('scheduleForm').reset();
    document.getElementById('scheduleId').value = '';

    const readerDisplay = document.getElementById('scheduleReaderDisplay');
    const readerInput = document.getElementById('scheduleReaderId');

    if (scheduleId) {
        const schedule = schedulesData.find(s => s.schedule_id === scheduleId);
        if (schedule) {
            document.getElementById('scheduleModalTitle').textContent = 'Edit Schedule';
            document.getElementById('scheduleId').value = schedule.schedule_id;
            document.getElementById('schedulePeriodId').value = schedule.period_id;
            document.getElementById('scheduleAreaId').value = schedule.area_id;
            document.getElementById('scheduledStartDate').value = schedule.scheduled_start_date;
            document.getElementById('scheduledEndDate').value = schedule.scheduled_end_date;
            document.getElementById('scheduleTotalMeters').value = schedule.total_meters || '';
            document.getElementById('scheduleNotes').value = schedule.notes || '';

            // Set reader display for edit mode
            if (schedule.reader_name) {
                readerDisplay.textContent = schedule.reader_name;
                readerInput.value = schedule.reader_id;
            } else {
                readerDisplay.innerHTML = '<span class="text-yellow-500 dark:text-yellow-400">No reader assigned</span>';
                readerInput.value = '';
            }
        }
    } else {
        document.getElementById('scheduleModalTitle').textContent = 'Add Reading Schedule';
        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('scheduledStartDate').value = today;

        // Reset reader display for new schedule
        readerDisplay.innerHTML = '<span class="text-gray-400 dark:text-gray-500">Select an area first</span>';
        readerInput.value = '';
    }

    document.getElementById('scheduleModal').classList.remove('hidden');
}

function closeScheduleModal() {
    document.getElementById('scheduleModal').classList.add('hidden');
}

function editSchedule(scheduleId) {
    openScheduleModal(scheduleId);
}

async function saveSchedule(event) {
    event.preventDefault();

    const scheduleId = document.getElementById('scheduleId').value;
    const data = {
        period_id: document.getElementById('schedulePeriodId').value,
        area_id: document.getElementById('scheduleAreaId').value,
        reader_id: document.getElementById('scheduleReaderId').value,
        scheduled_start_date: document.getElementById('scheduledStartDate').value,
        scheduled_end_date: document.getElementById('scheduledEndDate').value,
        total_meters: document.getElementById('scheduleTotalMeters').value || null,
        notes: document.getElementById('scheduleNotes').value || null
    };

    const isEdit = scheduleId !== '';
    const url = isEdit ? `/reading-schedules/${scheduleId}` : '/reading-schedules';
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            closeScheduleModal();
            loadSchedules();
            showScheduleToast(result.message, 'success');
        } else {
            showScheduleToast(result.message || 'Error saving schedule', 'error');
        }
    } catch (error) {
        console.error('Error saving schedule:', error);
        showScheduleToast('Error saving schedule', 'error');
    }
}

async function startSchedule(scheduleId) {
    if (!confirm('Start this reading schedule?')) return;

    try {
        const response = await fetch(`/reading-schedules/${scheduleId}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (result.success) {
            loadSchedules();
            showScheduleToast(result.message, 'success');
        } else {
            showScheduleToast(result.message || 'Error starting schedule', 'error');
        }
    } catch (error) {
        console.error('Error starting schedule:', error);
        showScheduleToast('Error starting schedule', 'error');
    }
}

function openCompleteModal(scheduleId) {
    document.getElementById('completeScheduleForm').reset();
    document.getElementById('completeScheduleId').value = scheduleId;

    const schedule = schedulesData.find(s => s.schedule_id === scheduleId);
    if (schedule) {
        document.getElementById('metersRead').value = schedule.meters_read || '';
        document.getElementById('metersMissed').value = schedule.meters_missed || '';
    }

    document.getElementById('completeScheduleModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeScheduleModal').classList.add('hidden');
}

async function submitCompleteSchedule(event) {
    event.preventDefault();

    const scheduleId = document.getElementById('completeScheduleId').value;
    const data = {
        meters_read: document.getElementById('metersRead').value || null,
        meters_missed: document.getElementById('metersMissed').value || null
    };

    try {
        const response = await fetch(`/reading-schedules/${scheduleId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            closeCompleteModal();
            loadSchedules();
            showScheduleToast(result.message, 'success');
        } else {
            showScheduleToast(result.message || 'Error completing schedule', 'error');
        }
    } catch (error) {
        console.error('Error completing schedule:', error);
        showScheduleToast('Error completing schedule', 'error');
    }
}

async function markAsDelayed(scheduleId) {
    const notes = prompt('Enter delay reason (optional):');
    if (notes === null) return; // User cancelled

    try {
        const response = await fetch(`/reading-schedules/${scheduleId}/delay`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ notes: notes })
        });

        const result = await response.json();

        if (result.success) {
            loadSchedules();
            showScheduleToast(result.message, 'success');
        } else {
            showScheduleToast(result.message || 'Error marking schedule as delayed', 'error');
        }
    } catch (error) {
        console.error('Error marking schedule as delayed:', error);
        showScheduleToast('Error marking schedule as delayed', 'error');
    }
}

async function deleteSchedule(scheduleId) {
    if (!confirm('Are you sure you want to delete this schedule?')) return;

    try {
        const response = await fetch(`/reading-schedules/${scheduleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (result.success) {
            loadSchedules();
            showScheduleToast(result.message, 'success');
        } else {
            showScheduleToast(result.message || 'Error deleting schedule', 'error');
        }
    } catch (error) {
        console.error('Error deleting schedule:', error);
        showScheduleToast('Error deleting schedule', 'error');
    }
}

function viewScheduleDetails(scheduleId) {
    const schedule = schedulesData.find(s => s.schedule_id === scheduleId);
    if (!schedule) return;

    const details = `
Schedule Details:
- Period: ${schedule.period_name}
- Area: ${schedule.area_name}
- Reader: ${schedule.reader_name}
- Scheduled: ${schedule.scheduled_start_date} to ${schedule.scheduled_end_date}
- Actual: ${schedule.actual_start_date || 'N/A'} to ${schedule.actual_end_date || 'N/A'}
- Progress: ${schedule.meters_read}/${schedule.total_meters} (${schedule.completion_percentage}%)
- Status: ${schedule.status_label}
${schedule.notes ? '- Notes: ' + schedule.notes : ''}
    `;

    alert(details.trim());
}

// ============================================================================
// Utility Functions
// ============================================================================

function escapeHtmlSchedule(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showScheduleToast(message, type = 'info') {
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
    } else if (typeof window.toast === 'function') {
        window.toast(message, type);
    } else {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white z-50 transition-opacity duration-300 ${
            type === 'success' ? 'bg-green-600' :
            type === 'error' ? 'bg-red-600' :
            'bg-blue-600'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}
</script>