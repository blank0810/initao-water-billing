<!-- Areas & Assignments Tab Content -->
<div class="space-y-6">
    <!-- Area and Assignments Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Areas Table -->
        <x-ui.card>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Areas</h3>
                <button onclick="openAreaModal()" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
                    <i class="fas fa-plus mr-1"></i> Add Area
                </button>
            </div>
            <div class="overflow-x-auto max-h-96">
                <table id="areasTableFull" class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Area Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="areasTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <!-- Area Assignments Table -->
        <x-ui.card>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Area Assignments</h3>
                <button onclick="openAssignmentModal()" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg">
                    <i class="fas fa-user-plus mr-1"></i> Assign Reader
                </button>
            </div>
            <div class="overflow-x-auto max-h-96">
                <table id="assignmentsTableFull" class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Area</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Meter Reader</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assignmentsTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>

<!-- Area Modal -->
<div id="areaModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeAreaModal()"></div>
        <div class="relative z-10 w-full max-w-md p-6 mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 id="areaModalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Add Area</h3>
                <button onclick="closeAreaModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="areaForm" onsubmit="saveArea(event)">
                <input type="hidden" id="areaId" value="">
                <div class="mb-4">
                    <label for="areaName" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Area Name</label>
                    <input type="text" id="areaName" name="a_desc" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter area name">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAreaModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<div id="assignmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeAssignmentModal()"></div>
        <div class="relative z-10 w-full max-w-md p-6 mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 id="assignmentModalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Assign Meter Reader</h3>
                <button onclick="closeAssignmentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="assignmentForm" onsubmit="saveAssignment(event)">
                <input type="hidden" id="assignmentId" value="">
                <div class="mb-4">
                    <label for="assignmentAreaId" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Area</label>
                    <select id="assignmentAreaId" name="area_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select Area</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="assignmentUserId" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Meter Reader</label>
                    <select id="assignmentUserId" name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select Meter Reader</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="effectiveFrom" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Effective From</label>
                    <input type="date" id="effectiveFrom" name="effective_from" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="mb-4">
                    <label for="effectiveTo" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Effective To (Optional)</label>
                    <input type="date" id="effectiveTo" name="effective_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAssignmentModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ============================================================================
// Area and Assignment Management
// ============================================================================

let areasData = [];
let meterReadersData = [];
let areasInitialized = false;

// Initialize when the areas tab is shown
window.initAreasTab = function() {
    if (!areasInitialized) {
        loadAreas();
        loadAssignments();
        loadMeterReaders();
        areasInitialized = true;
    }
};

// Auto-initialize on page load if this tab content is visible
document.addEventListener('DOMContentLoaded', function() {
    // Initialize immediately if needed, or wait for tab switch
    setTimeout(() => {
        if (document.getElementById('content-areas') && !document.getElementById('content-areas').classList.contains('hidden')) {
            window.initAreasTab();
        }
    }, 100);
});

// ============================================================================
// Area CRUD Functions
// ============================================================================

async function loadAreas() {
    try {
        const response = await fetch('/areas/list');
        const result = await response.json();

        if (result.success) {
            areasData = result.data;
            renderAreasTable(result.data);
            populateAreaDropdown(result.data);
        }
    } catch (error) {
        console.error('Error loading areas:', error);
        document.getElementById('areasTableBody').innerHTML =
            '<tr><td colspan="3" class="px-4 py-4 text-center text-red-500">Error loading areas</td></tr>';
    }
}

function renderAreasTable(areas) {
    const tbody = document.getElementById('areasTableBody');

    if (!areas || areas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No areas found</td></tr>';
        return;
    }

    tbody.innerHTML = areas.map(area => `
        <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${escapeHtml(area.a_desc)}</td>
            <td class="px-4 py-2">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${area.status_class}">
                    ${area.status_name}
                </span>
            </td>
            <td class="px-4 py-2">
                <div class="flex space-x-2">
                    <button onclick="editArea(${area.a_id})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteArea(${area.a_id})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function populateAreaDropdown(areas) {
    const select = document.getElementById('assignmentAreaId');
    if (!select) return;

    select.innerHTML = '<option value="">Select Area</option>';

    areas.filter(a => a.is_active).forEach(area => {
        select.innerHTML += `<option value="${area.a_id}">${escapeHtml(area.a_desc)}</option>`;
    });
}

function openAreaModal(areaId = null) {
    document.getElementById('areaForm').reset();
    document.getElementById('areaId').value = '';

    if (areaId) {
        const area = areasData.find(a => a.a_id === areaId);
        if (area) {
            document.getElementById('areaModalTitle').textContent = 'Edit Area';
            document.getElementById('areaId').value = area.a_id;
            document.getElementById('areaName').value = area.a_desc;
        }
    } else {
        document.getElementById('areaModalTitle').textContent = 'Add Area';
    }

    document.getElementById('areaModal').classList.remove('hidden');
}

function closeAreaModal() {
    document.getElementById('areaModal').classList.add('hidden');
}

function editArea(areaId) {
    openAreaModal(areaId);
}

async function saveArea(event) {
    event.preventDefault();

    const areaId = document.getElementById('areaId').value;
    const areaName = document.getElementById('areaName').value;

    const isEdit = areaId !== '';
    const url = isEdit ? `/areas/${areaId}` : '/areas';
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ a_desc: areaName })
        });

        const result = await response.json();

        if (result.success) {
            closeAreaModal();
            loadAreas();
            showAreaToast(result.message, 'success');
        } else {
            showAreaToast(result.message || 'Error saving area', 'error');
        }
    } catch (error) {
        console.error('Error saving area:', error);
        showAreaToast('Error saving area', 'error');
    }
}

async function deleteArea(areaId) {
    if (!confirm('Are you sure you want to delete this area?')) {
        return;
    }

    try {
        const response = await fetch(`/areas/${areaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (result.success) {
            loadAreas();
            showAreaToast(result.message, 'success');
        } else {
            showAreaToast(result.message || 'Error deleting area', 'error');
        }
    } catch (error) {
        console.error('Error deleting area:', error);
        showAreaToast('Error deleting area', 'error');
    }
}

// ============================================================================
// Assignment CRUD Functions
// ============================================================================

async function loadAssignments() {
    try {
        const response = await fetch('/area-assignments?all=true');
        const result = await response.json();

        if (result.success) {
            renderAssignmentsTable(result.data);
        }
    } catch (error) {
        console.error('Error loading assignments:', error);
        document.getElementById('assignmentsTableBody').innerHTML =
            '<tr><td colspan="5" class="px-4 py-4 text-center text-red-500">Error loading assignments</td></tr>';
    }
}

async function loadMeterReaders() {
    try {
        const response = await fetch('/area-assignments/meter-readers');
        const result = await response.json();

        if (result.success) {
            meterReadersData = result.data;
            populateMeterReaderDropdown(result.data);
        }
    } catch (error) {
        console.error('Error loading meter readers:', error);
    }
}

function renderAssignmentsTable(assignments) {
    const tbody = document.getElementById('assignmentsTableBody');

    if (!assignments || assignments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No assignments found</td></tr>';
        return;
    }

    tbody.innerHTML = assignments.map(assignment => {
        const period = assignment.effective_to
            ? `${assignment.effective_from} to ${assignment.effective_to}`
            : `${assignment.effective_from} - Present`;

        return `
            <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-2 text-gray-900 dark:text-white">${escapeHtml(assignment.area_name)}</td>
                <td class="px-4 py-2 text-gray-900 dark:text-white">${escapeHtml(assignment.user_name)}</td>
                <td class="px-4 py-2 text-gray-500 dark:text-gray-400 text-xs">${period}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${assignment.status_class}">
                        ${assignment.status}
                    </span>
                </td>
                <td class="px-4 py-2">
                    <div class="flex space-x-2">
                        ${assignment.is_active ? `
                            <button onclick="endAssignment(${assignment.area_assignment_id})" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300" title="End Assignment">
                                <i class="fas fa-stop-circle"></i>
                            </button>
                        ` : ''}
                        <button onclick="deleteAssignment(${assignment.area_assignment_id})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function populateMeterReaderDropdown(meterReaders) {
    const select = document.getElementById('assignmentUserId');
    if (!select) return;

    select.innerHTML = '<option value="">Select Meter Reader</option>';

    meterReaders.forEach(reader => {
        select.innerHTML += `<option value="${reader.id}">${escapeHtml(reader.name)} (${escapeHtml(reader.email)})</option>`;
    });
}

function openAssignmentModal() {
    document.getElementById('assignmentForm').reset();
    document.getElementById('assignmentId').value = '';
    document.getElementById('assignmentModalTitle').textContent = 'Assign Meter Reader';

    // Set default date to today
    document.getElementById('effectiveFrom').value = new Date().toISOString().split('T')[0];

    document.getElementById('assignmentModal').classList.remove('hidden');
}

function closeAssignmentModal() {
    document.getElementById('assignmentModal').classList.add('hidden');
}

async function saveAssignment(event) {
    event.preventDefault();

    const data = {
        area_id: document.getElementById('assignmentAreaId').value,
        user_id: document.getElementById('assignmentUserId').value,
        effective_from: document.getElementById('effectiveFrom').value,
        effective_to: document.getElementById('effectiveTo').value || null
    };

    try {
        const response = await fetch('/area-assignments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            closeAssignmentModal();
            loadAssignments();
            showAreaToast(result.message, 'success');
        } else {
            showAreaToast(result.message || 'Error saving assignment', 'error');
        }
    } catch (error) {
        console.error('Error saving assignment:', error);
        showAreaToast('Error saving assignment', 'error');
    }
}

async function endAssignment(assignmentId) {
    const endDate = prompt('Enter end date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);

    if (!endDate) return;

    try {
        const response = await fetch(`/area-assignments/${assignmentId}/end`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ effective_to: endDate })
        });

        const result = await response.json();

        if (result.success) {
            loadAssignments();
            showAreaToast(result.message, 'success');
        } else {
            showAreaToast(result.message || 'Error ending assignment', 'error');
        }
    } catch (error) {
        console.error('Error ending assignment:', error);
        showAreaToast('Error ending assignment', 'error');
    }
}

async function deleteAssignment(assignmentId) {
    if (!confirm('Are you sure you want to delete this assignment?')) {
        return;
    }

    try {
        const response = await fetch(`/area-assignments/${assignmentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (result.success) {
            loadAssignments();
            showAreaToast(result.message, 'success');
        } else {
            showAreaToast(result.message || 'Error deleting assignment', 'error');
        }
    } catch (error) {
        console.error('Error deleting assignment:', error);
        showAreaToast('Error deleting assignment', 'error');
    }
}

// ============================================================================
// Utility Functions
// ============================================================================

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showAreaToast(message, type = 'info') {
    // Check if there's a global toast function, otherwise use simple implementation
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
    } else if (typeof window.toast === 'function') {
        window.toast(message, type);
    } else {
        // Fallback to a simple toast implementation
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