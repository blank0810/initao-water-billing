<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header -->
            <x-ui.page-header 
                title="Billing Overall Data" 
                subtitle="Comprehensive billing analytics and insights"
                back-url="{{ route('billing.management') }}"
                back-text="Back to Billing Management"
            />

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <x-ui.stat-card 
                    title="Total Consumers" 
                    value="0" 
                    icon="fas fa-users" 
                    id="totalConsumers"
                />
                <x-ui.stat-card 
                    title="Total Amount Due" 
                    value="₱ 0.00" 
                    icon="fas fa-exclamation-triangle" 
                    id="totalAmountDue"
                />
                <x-ui.stat-card 
                    title="Total Paid" 
                    value="₱ 0.00" 
                    icon="fas fa-check-circle" 
                    id="totalPaid"
                />
                <x-ui.stat-card 
                    title="Outstanding Balance" 
                    value="₱ 0.00" 
                    icon="fas fa-clock" 
                    id="outstandingBalance"
                />
            </div>

            <!-- Area and Assignments Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Areas Table -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Areas</h3>
                        <button onclick="openAreaModal()" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
                            <i class="fas fa-plus mr-1"></i> Add Area
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-80">
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
                    <div class="overflow-x-auto max-h-80">
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

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Payment Status Chart -->
                <x-ui.card class="h-80">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Status Distribution</h3>
                        <x-ui.export-print type="chart" target-id="paymentStatusChart" filename="payment-status-chart" title="Payment Status Distribution" />
                    </div>
                    <div class="relative h-64">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                </x-ui.card>

                <!-- Monthly Revenue Trend -->
                <x-ui.card class="h-80">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Revenue Trend</h3>
                        <x-ui.export-print type="chart" target-id="revenueChart" filename="revenue-trend-chart" title="Monthly Revenue Trend" />
                    </div>
                    <div class="relative h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </x-ui.card>
            </div>

            <!-- Data Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Consumers by Amount Due -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Consumers by Amount Due</h3>
                        <x-ui.export-print type="table" target-id="topConsumersTableFull" filename="top-consumers" title="Top Consumers by Amount Due" />
                    </div>
                    <div class="overflow-x-auto">
                        <table id="topConsumersTableFull" class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumer</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount Due</th>
                                </tr>
                            </thead>
                            <tbody id="topConsumersTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>

                <!-- Recent Payments -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Payments</h3>
                        <x-ui.export-print type="table" target-id="recentPaymentsTableFull" filename="recent-payments" title="Recent Payments" />
                    </div>
                    <div class="overflow-x-auto">
                        <table id="recentPaymentsTableFull" class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumer</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody id="recentPaymentsTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            </div>

        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@vite(['resources/js/billing.js'])

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts and data
    initializeBillingOverallData();
});

function initializeBillingOverallData() {
    // Sample data - replace with actual data from your backend
    const billingData = {
        totalConsumers: 1250,
        totalAmountDue: 2850000,
        totalPaid: 1950000,
        outstandingBalance: 900000
    };

    // Update summary cards - find the value elements within the stat cards
    const totalConsumersCard = document.querySelector('#totalConsumers .text-2xl');
    const totalAmountDueCard = document.querySelector('#totalAmountDue .text-2xl');
    const totalPaidCard = document.querySelector('#totalPaid .text-2xl');
    const outstandingBalanceCard = document.querySelector('#outstandingBalance .text-2xl');
    
    if (totalConsumersCard) totalConsumersCard.textContent = billingData.totalConsumers.toLocaleString();
    if (totalAmountDueCard) totalAmountDueCard.textContent = '₱ ' + billingData.totalAmountDue.toLocaleString();
    if (totalPaidCard) totalPaidCard.textContent = '₱ ' + billingData.totalPaid.toLocaleString();
    if (outstandingBalanceCard) outstandingBalanceCard.textContent = '₱ ' + billingData.outstandingBalance.toLocaleString();

    // Initialize Payment Status Chart
    const paymentCtx = document.getElementById('paymentStatusChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Overdue', 'Pending'],
            datasets: [{
                data: [65, 20, 15],
                backgroundColor: ['#10B981', '#EF4444', '#F59E0B']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Initialize Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [320000, 285000, 340000, 310000, 365000, 380000],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Populate tables
    populateTopConsumersTable();
    populateRecentPaymentsTable();
    
    // Make tables sortable
    setTimeout(() => {
        TableSorter.makeTableSortable('topConsumersTableFull');
        TableSorter.makeTableSortable('recentPaymentsTableFull');
    }, 100);
}

function populateTopConsumersTable() {
    const topConsumers = [
        { name: 'Juan Dela Cruz', amount: 15000 },
        { name: 'Maria Santos', amount: 12500 },
        { name: 'Pedro Garcia', amount: 11200 },
        { name: 'Ana Rodriguez', amount: 9800 },
        { name: 'Carlos Lopez', amount: 8900 }
    ];

    const tbody = document.getElementById('topConsumersTable');
    tbody.innerHTML = topConsumers.map(consumer => `
        <tr class="text-sm">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${consumer.name}</td>
            <td class="px-4 py-2 text-red-600 dark:text-red-400">₱ ${consumer.amount.toLocaleString()}</td>
        </tr>
    `).join('');
}

function populateRecentPaymentsTable() {
    const recentPayments = [
        { name: 'Lisa Chen', amount: 2500, date: '2024-01-15' },
        { name: 'Mark Johnson', amount: 3200, date: '2024-01-14' },
        { name: 'Sofia Martinez', amount: 1800, date: '2024-01-14' },
        { name: 'David Kim', amount: 2900, date: '2024-01-13' },
        { name: 'Emma Wilson', amount: 2100, date: '2024-01-13' }
    ];

    const tbody = document.getElementById('recentPaymentsTable');
    tbody.innerHTML = recentPayments.map(payment => `
        <tr class="text-sm">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${payment.name}</td>
            <td class="px-4 py-2 text-green-600 dark:text-green-400">₱ ${payment.amount.toLocaleString()}</td>
            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">${payment.date}</td>
        </tr>
    `).join('');
}

// ============================================================================
// Area and Assignment Management
// ============================================================================

let areasData = [];
let meterReadersData = [];

// Initialize Area and Assignment data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAreas();
    loadAssignments();
    loadMeterReaders();
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
            showToast(result.message, 'success');
        } else {
            showToast(result.message || 'Error saving area', 'error');
        }
    } catch (error) {
        console.error('Error saving area:', error);
        showToast('Error saving area', 'error');
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
            showToast(result.message, 'success');
        } else {
            showToast(result.message || 'Error deleting area', 'error');
        }
    } catch (error) {
        console.error('Error deleting area:', error);
        showToast('Error deleting area', 'error');
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
            showToast(result.message, 'success');
        } else {
            showToast(result.message || 'Error saving assignment', 'error');
        }
    } catch (error) {
        console.error('Error saving assignment:', error);
        showToast('Error saving assignment', 'error');
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
            showToast(result.message, 'success');
        } else {
            showToast(result.message || 'Error ending assignment', 'error');
        }
    } catch (error) {
        console.error('Error ending assignment:', error);
        showToast('Error ending assignment', 'error');
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
            showToast(result.message, 'success');
        } else {
            showToast(result.message || 'Error deleting assignment', 'error');
        }
    } catch (error) {
        console.error('Error deleting assignment:', error);
        showToast('Error deleting assignment', 'error');
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

function showToast(message, type = 'info') {
    // Check if there's a global toast function, otherwise use alert
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
