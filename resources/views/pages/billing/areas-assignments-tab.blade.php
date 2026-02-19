<!-- Areas & Assignments Tab Content -->
<div class="space-y-6">
    <!-- Connection Area Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <i class="fas fa-plug text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Connections</p>
                    <p id="statTotalConnections" class="text-xl font-bold text-gray-900 dark:text-white">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">With Area</p>
                    <p id="statWithArea" class="text-xl font-bold text-gray-900 dark:text-white">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <i class="fas fa-exclamation-circle text-orange-600 dark:text-orange-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Without Area</p>
                    <p id="statWithoutArea" class="text-xl font-bold text-gray-900 dark:text-white">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <i class="fas fa-map-marker-alt text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Areas</p>
                    <p id="statTotalAreas" class="text-xl font-bold text-gray-900 dark:text-white">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Connection Area Assignment Section -->
    <x-ui.card>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-map-marked-alt mr-2 text-blue-600"></i>Assign Areas to Service Connections
            </h3>
            <div class="flex items-center gap-2">
                <button onclick="autoAssignConnections()" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg">
                    <i class="fas fa-magic mr-1"></i> Auto-Assign
                </button>
                <button onclick="openBulkAssignModal()" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
                    <i class="fas fa-layer-group mr-1"></i> Bulk Assign
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Area</label>
                <select id="connectionAreaFilter" onchange="loadConnectionsByArea()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                    <option value="all">All Connections</option>
                    <option value="" selected>All with area assigned</option>
                    <option value="none">Without area (unassigned)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Barangay</label>
                <select id="connectionBarangayFilter" onchange="loadConnectionsByArea()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                    <option value="">All Barangays</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" id="connectionSearchInput" placeholder="Account, name..." onkeyup="debounceConnectionSearch()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
            </div>
            <div class="flex items-end gap-2">
                <button onclick="loadConnectionsByArea()" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600" title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button id="removeAreaBtn" onclick="removeAreaFromSelected()" disabled class="px-3 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 disabled:opacity-50 disabled:cursor-not-allowed" title="Remove Area">
                    <i class="fas fa-unlink"></i>
                </button>
            </div>
        </div>

        <!-- Connections Table -->
        <div class="overflow-x-auto max-h-96 border border-gray-200 dark:border-gray-700 rounded-lg">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAllConnections" onchange="toggleSelectAllConnections()" class="rounded border-gray-300 dark:border-gray-600">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Account No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Account Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barangay</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Current Area</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="connectionsTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p id="connectionCount" class="mt-2 text-sm text-gray-500 dark:text-gray-400"></p>
    </x-ui.card>

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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Connections</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="areasTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <!-- Area Assignments Table -->
        <x-ui.card>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Meter Reader Assignments</h3>
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

<!-- Bulk Assign Area Modal -->
<div id="bulkAssignModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeBulkAssignModal()"></div>
        <div class="relative z-10 w-full max-w-lg p-6 mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-layer-group mr-2 text-blue-600"></i>Bulk Assign Area to Connections
                </h3>
                <button onclick="closeBulkAssignModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    <span id="selectedCountText">0 connections selected</span>
                </p>
                <label for="bulkAreaSelect" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Select Area to Assign</label>
                <select id="bulkAreaSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Select Area</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeBulkAssignModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Cancel</button>
                <button type="button" onclick="submitBulkAssign()" id="bulkAssignBtn" disabled class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-check mr-1"></i> Assign Area
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Single Connection Area Assignment Modal -->
<div id="singleAssignModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeSingleAssignModal()"></div>
        <div class="relative z-10 w-full max-w-md p-6 mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>Assign Area
                </h3>
                <button onclick="closeSingleAssignModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <input type="hidden" id="singleConnectionId" value="">
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Account:</p>
                <p id="singleAccountNo" class="font-medium text-gray-900 dark:text-white"></p>
            </div>
            <div class="mb-4">
                <label for="singleAreaSelect" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Select Area</label>
                <select id="singleAreaSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Select Area</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeSingleAssignModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Cancel</button>
                <button type="button" onclick="submitSingleAssign()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-check mr-1"></i> Assign
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Auto-Assign Confirmation Modal -->
<div id="autoAssignModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeAutoAssignModal()"></div>
        <div class="relative z-10 w-full max-w-md p-6 mx-auto bg-white rounded-lg shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-magic mr-2 text-green-600"></i>Auto-Assign Connections
                </h3>
                <button onclick="closeAutoAssignModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg mb-3">
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        <i class="fas fa-info-circle mr-1"></i>
                        This will automatically assign all <strong>unassigned</strong> connections to areas based on their barangay address.
                    </p>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Connections that already have an area will not be affected. Connections without a matching barangay will be skipped.
                </p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeAutoAssignModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Cancel</button>
                <button type="button" id="autoAssignConfirmBtn" onclick="submitAutoAssign()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                    <i class="fas fa-magic mr-1"></i> Auto-Assign
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================================================
// Area and Assignment Management
// ============================================================================

let areasData = [];
let areaStatsData = {};
let meterReadersData = [];
let connectionsData = [];
let selectedConnections = new Set();
let areasInitialized = false;
let connectionSearchTimeout = null;

// Initialize when the areas tab is shown
window.initAreasTab = function() {
    if (!areasInitialized) {
        loadAreas();
        loadAssignments();
        loadMeterReaders();
        loadBarangays();
        loadConnectionAreaStats();
        loadConnectionsByArea();
        areasInitialized = true;
    }
};

// ============================================================================
// Data Loading Functions
// ============================================================================

async function loadBarangays() {
    try {
        const response = await fetch('/api/address/barangays');
        const barangays = await response.json();

        const select = document.getElementById('connectionBarangayFilter');
        if (!select) return;

        barangays.forEach(bg => {
            const option = document.createElement('option');
            option.value = bg.b_id;
            option.textContent = bg.b_desc;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading barangays:', error);
    }
}

// ============================================================================
// Connection Area Stats
// ============================================================================

async function loadConnectionAreaStats() {
    try {
        const response = await fetch('/areas/connection-area-stats');
        const result = await response.json();

        if (result.success) {
            areaStatsData = result.data;
            document.getElementById('statTotalConnections').textContent = result.data.total_active_connections;
            document.getElementById('statWithArea').textContent = result.data.connections_with_area;
            document.getElementById('statWithoutArea').textContent = result.data.connections_without_area;
            document.getElementById('statTotalAreas').textContent = result.data.per_area?.length || 0;
        }
    } catch (error) {
        console.error('Error loading connection area stats:', error);
    }
}

// ============================================================================
// Connection Area Assignment Functions
// ============================================================================

function debounceConnectionSearch() {
    clearTimeout(connectionSearchTimeout);
    connectionSearchTimeout = setTimeout(() => {
        loadConnectionsByArea();
    }, 300);
}

async function loadConnectionsByArea() {
    const areaFilter = document.getElementById('connectionAreaFilter').value;
    const barangayFilter = document.getElementById('connectionBarangayFilter').value;
    const search = document.getElementById('connectionSearchInput').value;
    const tbody = document.getElementById('connectionsTableBody');

    tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Loading...</td></tr>';

    try {
        let url;
        let params = new URLSearchParams();
        if (search) params.append('search', search);
        if (barangayFilter) params.append('barangay_id', barangayFilter);
        params.append('limit', '100');

        if (areaFilter === 'none') {
            url = `/areas/connections-without-area?${params.toString()}`;
        } else if (areaFilter === 'all') {
            params.append('area_id', '-1');
            url = `/areas/connections-by-area?${params.toString()}`;
        } else if (areaFilter) {
            params.append('area_id', areaFilter);
            url = `/areas/connections-by-area?${params.toString()}`;
        } else {
            // Default: All with area assigned
            url = `/areas/connections-by-area?${params.toString()}`;
        }

        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            connectionsData = result.data;
            selectedConnections.clear();
            renderConnectionsTable(result.data);
            updateSelectionUI();
        }
    } catch (error) {
        console.error('Error loading connections:', error);
        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-red-500">Error loading connections</td></tr>';
    }
}

function renderConnectionsTable(connections) {
    const tbody = document.getElementById('connectionsTableBody');

    if (!connections || connections.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No connections found</td></tr>';
        document.getElementById('connectionCount').textContent = '';
        return;
    }

    tbody.innerHTML = connections.map(conn => `
        <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-4 py-2">
                <input type="checkbox" class="connection-checkbox rounded border-gray-300 dark:border-gray-600"
                    data-id="${conn.connection_id}" onchange="toggleConnectionSelection(${conn.connection_id})">
            </td>
            <td class="px-4 py-2 font-mono text-gray-900 dark:text-white">${escapeHtml(conn.account_no)}</td>
            <td class="px-4 py-2 text-gray-900 dark:text-white">${escapeHtml(conn.customer_name)}</td>
            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">${escapeHtml(conn.account_type)}</td>
            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">${escapeHtml(conn.barangay)}</td>
            <td class="px-4 py-2">
                ${conn.area_name
                    ? `<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">${escapeHtml(conn.area_name)}</span>`
                    : `<span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200">Unassigned</span>`
                }
            </td>
            <td class="px-4 py-2">
                <button onclick="openSingleAssignModal(${conn.connection_id}, '${escapeHtml(conn.account_no)}', ${conn.area_id || 'null'})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400" title="Assign Area">
                    <i class="fas fa-map-marker-alt"></i>
                </button>
            </td>
        </tr>
    `).join('');

    document.getElementById('connectionCount').textContent = `Showing ${connections.length} connection(s)`;
}

function toggleConnectionSelection(connectionId) {
    if (selectedConnections.has(connectionId)) {
        selectedConnections.delete(connectionId);
    } else {
        selectedConnections.add(connectionId);
    }
    updateSelectionUI();
}

function toggleSelectAllConnections() {
    const selectAll = document.getElementById('selectAllConnections');
    const checkboxes = document.querySelectorAll('.connection-checkbox');

    if (selectAll.checked) {
        checkboxes.forEach(cb => {
            cb.checked = true;
            selectedConnections.add(parseInt(cb.dataset.id));
        });
    } else {
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        selectedConnections.clear();
    }
    updateSelectionUI();
}

function updateSelectionUI() {
    const count = selectedConnections.size;
    const removeBtn = document.getElementById('removeAreaBtn');
    const bulkBtn = document.getElementById('bulkAssignBtn');

    removeBtn.disabled = count === 0;

    document.getElementById('selectedCountText').textContent = `${count} connection(s) selected`;

    if (bulkBtn) {
        bulkBtn.disabled = count === 0 || !document.getElementById('bulkAreaSelect').value;
    }
}

// ============================================================================
// Bulk Assignment Modal Functions
// ============================================================================

function openBulkAssignModal() {
    if (selectedConnections.size === 0) {
        showAreaToast('Please select at least one connection', 'error');
        return;
    }

    // Populate area dropdown
    const select = document.getElementById('bulkAreaSelect');
    select.innerHTML = '<option value="">Select Area</option>';
    areasData.filter(a => a.is_active).forEach(area => {
        select.innerHTML += `<option value="${area.a_id}">${escapeHtml(area.a_desc)}</option>`;
    });

    document.getElementById('selectedCountText').textContent = `${selectedConnections.size} connection(s) selected`;
    document.getElementById('bulkAssignModal').classList.remove('hidden');

    // Enable/disable button based on selection
    select.onchange = function() {
        document.getElementById('bulkAssignBtn').disabled = !this.value;
    };
}

function closeBulkAssignModal() {
    document.getElementById('bulkAssignModal').classList.add('hidden');
}

async function submitBulkAssign() {
    const areaId = document.getElementById('bulkAreaSelect').value;
    if (!areaId) {
        showAreaToast('Please select an area', 'error');
        return;
    }

    try {
        const response = await fetch('/areas/assign-connections', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                area_id: parseInt(areaId),
                connection_ids: Array.from(selectedConnections)
            })
        });

        const result = await response.json();

        if (result.success) {
            closeBulkAssignModal();
            selectedConnections.clear();
            loadConnectionsByArea();
            loadConnectionAreaStats();
            loadAreas();
            showAreaToast(result.message, 'success');
        } else {
            showAreaToast(result.message || 'Error assigning area', 'error');
        }
    } catch (error) {
        console.error('Error assigning area:', error);
        showAreaToast('Error assigning area', 'error');
    }
}

// ============================================================================
// Single Assignment Modal Functions
// ============================================================================

function openSingleAssignModal(connectionId, accountNo, currentAreaId = null) {
    document.getElementById('singleConnectionId').value = connectionId;
    document.getElementById('singleAccountNo').textContent = accountNo;

    // Populate area dropdown
    const select = document.getElementById('singleAreaSelect');
    select.innerHTML = '<option value="">Select Area</option>';
    areasData.filter(a => a.is_active).forEach(area => {
        const selected = (currentAreaId && area.a_id == currentAreaId) ? 'selected' : '';
        select.innerHTML += `<option value="${area.a_id}" ${selected}>${escapeHtml(area.a_desc)}</option>`;
    });

    document.getElementById('singleAssignModal').classList.remove('hidden');
}

function closeSingleAssignModal() {
    document.getElementById('singleAssignModal').classList.add('hidden');
}

async function submitSingleAssign() {
    const connectionId = document.getElementById('singleConnectionId').value;
    const areaId = document.getElementById('singleAreaSelect').value;

    if (!areaId) {
        showAreaToast('Please select an area', 'error');
        return;
    }

    try {
        const response = await fetch('/areas/assign-connections', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                area_id: parseInt(areaId),
                connection_ids: [parseInt(connectionId)]
            })
        });

        const result = await response.json();

        if (result.success) {
            closeSingleAssignModal();
            loadConnectionsByArea();
            loadConnectionAreaStats();
            loadAreas();
            showAreaToast(result.message, 'success');
        } else {
            showAreaToast(result.message || 'Error assigning area', 'error');
        }
    } catch (error) {
        console.error('Error assigning area:', error);
        showAreaToast('Error assigning area', 'error');
    }
}

// ============================================================================
// Remove Area from Connections
// ============================================================================

async function removeAreaFromSelected() {
    if (selectedConnections.size === 0) {
        showAreaToast('Please select at least one connection', 'error');
        return;
    }

    if (!confirm(`Remove area assignment from ${selectedConnections.size} connection(s)?`)) {
        return;
    }

    try {
        const response = await fetch('/areas/remove-connections', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                connection_ids: Array.from(selectedConnections)
            })
        });

        const result = await response.json();

        if (result.success) {
            selectedConnections.clear();
            loadConnectionsByArea();
            loadConnectionAreaStats();
            loadAreas();
            showAreaToast(result.message, 'success');
        } else {
            showAreaToast(result.message || 'Error removing area', 'error');
        }
    } catch (error) {
        console.error('Error removing area:', error);
        showAreaToast('Error removing area', 'error');
    }
}

// ============================================================================
// Auto-Assign Connections by Barangay
// ============================================================================

function autoAssignConnections() {
    document.getElementById('autoAssignModal').classList.remove('hidden');
}

function closeAutoAssignModal() {
    document.getElementById('autoAssignModal').classList.add('hidden');
}

async function submitAutoAssign() {
    const btn = document.getElementById('autoAssignConfirmBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Assigning...';

    try {
        const response = await fetch('/areas/auto-assign', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (result.success) {
            closeAutoAssignModal();
            loadConnectionsByArea();
            loadConnectionAreaStats();
            loadAreas();
            showAreaToast(result.message, 'success');
        } else {
            showAreaToast(result.message || 'Error running auto-assign', 'error');
        }
    } catch (error) {
        console.error('Error auto-assigning connections:', error);
        showAreaToast('Error running auto-assign', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-magic mr-1"></i> Auto-Assign';
    }
}

// ============================================================================
// Area CRUD Functions
// ============================================================================

async function loadAreas() {
    try {
        const [areasResponse, statsResponse] = await Promise.all([
            fetch('/areas/list'),
            fetch('/areas/connection-area-stats')
        ]);

        const areasResult = await areasResponse.json();
        const statsResult = await statsResponse.json();

        if (areasResult.success) {
            areasData = areasResult.data;

            // Merge connection counts from stats
            if (statsResult.success && statsResult.data.per_area) {
                const countMap = {};
                statsResult.data.per_area.forEach(item => {
                    countMap[item.a_id] = item.connection_count;
                });

                areasData = areasData.map(area => ({
                    ...area,
                    connection_count: countMap[area.a_id] || 0
                }));
            }

            renderAreasTable(areasData);
            populateAreaDropdown(areasData);
            populateConnectionAreaFilter(areasData);
        }
    } catch (error) {
        console.error('Error loading areas:', error);
        document.getElementById('areasTableBody').innerHTML =
            '<tr><td colspan="4" class="px-4 py-4 text-center text-red-500">Error loading areas</td></tr>';
    }
}

function renderAreasTable(areas) {
    const tbody = document.getElementById('areasTableBody');

    if (!areas || areas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">No areas found</td></tr>';
        return;
    }

    tbody.innerHTML = areas.map(area => `
        <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${escapeHtml(area.a_desc)}</td>
            <td class="px-4 py-2">
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                    ${area.connection_count || 0}
                </span>
            </td>
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

function populateConnectionAreaFilter(areas) {
    const select = document.getElementById('connectionAreaFilter');
    if (!select) return;

    const currentValue = select.value;

    // Keep the first three options
    select.innerHTML = `
        <option value="all" ${currentValue === 'all' ? 'selected' : ''}>All Connections</option>
        <option value="" ${currentValue === '' ? 'selected' : ''}>All with area assigned</option>
        <option value="none" ${currentValue === 'none' ? 'selected' : ''}>Without area (unassigned)</option>
    `;

    areas.filter(a => a.is_active).forEach(area => {
        const selected = currentValue == area.a_id ? 'selected' : '';
        select.innerHTML += `<option value="${area.a_id}" ${selected}>${escapeHtml(area.a_desc)} (${area.connection_count || 0})</option>`;
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
                        <button onclick="editAssignment(${assignment.area_assignment_id})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
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

async function editAssignment(assignmentId) {
    try {
        const response = await fetch(`/area-assignments/${assignmentId}`);
        const result = await response.json();

        if (result.success) {
            const assignment = result.data;
            document.getElementById('assignmentId').value = assignment.area_assignment_id;
            document.getElementById('assignmentAreaId').value = assignment.area_id;
            document.getElementById('assignmentUserId').value = assignment.user_id;
            document.getElementById('effectiveFrom').value = assignment.effective_from;
            document.getElementById('effectiveTo').value = assignment.effective_to || '';

            document.getElementById('assignmentModalTitle').textContent = 'Edit Assignment';
            document.getElementById('assignmentModal').classList.remove('hidden');

            // Area should be disabled during edit to avoid consistency issues
            document.getElementById('assignmentAreaId').disabled = true;
        }
    } catch (error) {
        console.error('Error fetching assignment details:', error);
        showAreaToast('Error loading assignment details', 'error');
    }
}

function openAssignmentModal() {
    document.getElementById('assignmentForm').reset();
    document.getElementById('assignmentId').value = '';
    document.getElementById('assignmentModalTitle').textContent = 'Assign Meter Reader';
    document.getElementById('assignmentAreaId').disabled = false;

    // Set default date to today
    document.getElementById('effectiveFrom').value = new Date().toISOString().split('T')[0];

    document.getElementById('assignmentModal').classList.remove('hidden');
}

function closeAssignmentModal() {
    document.getElementById('assignmentModal').classList.add('hidden');
}

async function saveAssignment(event) {
    event.preventDefault();

    const assignmentId = document.getElementById('assignmentId').value;
    const isEdit = assignmentId !== '';

    const data = {
        area_id: document.getElementById('assignmentAreaId').value,
        user_id: document.getElementById('assignmentUserId').value,
        effective_from: document.getElementById('effectiveFrom').value,
        effective_to: document.getElementById('effectiveTo').value || null
    };

    const url = isEdit ? `/area-assignments/${assignmentId}` : '/area-assignments';
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
