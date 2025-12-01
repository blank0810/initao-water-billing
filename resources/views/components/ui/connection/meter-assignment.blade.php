<!-- Meter Assignment Modal -->
<div id="meterAssignmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Meter Assignment</h3>
            <button onclick="closeMeterAssignmentModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Assignment Type Selection -->
        <div class="mb-6">
            <div class="flex space-x-4 p-1 bg-gray-100 dark:bg-gray-700 rounded-lg">
                <button id="newAssignmentTab" onclick="switchAssignmentTab('new')" class="flex-1 px-4 py-2 text-sm font-medium rounded-md bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm">
                    <i class="fas fa-plus mr-2"></i>New Assignment
                </button>
                <button id="replacementTab" onclick="switchAssignmentTab('replacement')" class="flex-1 px-4 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-600">
                    <i class="fas fa-exchange-alt mr-2"></i>Replacement
                </button>
            </div>
        </div>

        <!-- New Assignment Form -->
        <div id="newAssignmentForm" class="space-y-6">
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Customer Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Name:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="maCustomerName">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Account No:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="maAccountNo">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Address:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="maAddress">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Area:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="maArea">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Meter Reader:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="maMeterReader">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Reading Schedule:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="maReadingSchedule">-</span>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Select Meter</h4>
                <select id="maMeterSelect" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Choose a meter...</option>
                </select>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Installation Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Technician *</label>
                        <select id="technicianSelect" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Select technician...</option>
                            <option value="Juan Technician">Juan Technician</option>
                            <option value="Maria Installer">Maria Installer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Installation Date *</label>
                        <input type="date" id="installationDate" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Initial Reading *</label>
                        <input type="number" id="initialReading" required placeholder="0.0" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
            </div>
        </div>

        <!-- Replacement Form -->
        <div id="replacementForm" class="hidden space-y-6">
            <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Faulty Meter to Replace</h4>
                <div id="faultyMeters" class="space-y-3"></div>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Replacement Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Final Reading *</label>
                        <input type="number" id="finalReading" required placeholder="0.0" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Replacement Date *</label>
                        <input type="date" id="replacementDate" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeMeterAssignmentModal()" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="submitMeterAssignment()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="fas fa-check mr-2"></i>Complete Assignment
            </button>
        </div>
    </div>
</div>

<script>
const meterAssignmentAvailableMeters = [
    { id: 'MTR-001', serial: 'WM-2024-001', type: 'Digital', brand: 'Sensus', status: 'Available' },
    { id: 'MTR-002', serial: 'WM-2024-002', type: 'Digital', brand: 'Itron', status: 'Available' },
    { id: 'MTR-003', serial: 'WM-2024-003', type: 'Analog', brand: 'Elster', status: 'Available' },
    { id: 'MTR-004', serial: 'WM-2024-004', type: 'Digital', brand: 'Sensus', status: 'Available' },
    { id: 'MTR-005', serial: 'WM-2024-005', type: 'Smart', brand: 'Kamstrup', status: 'Available' }
];

let selectedMeter = null;
let assignmentType = 'new';
let currentConnection = null;

function openMeterAssignmentModal(type = 'new', connection = null) {
    assignmentType = type;
    currentConnection = connection;
    
    if (connection) {
        document.getElementById('maCustomerName').textContent = connection.customer_name || '-';
        document.getElementById('maAccountNo').textContent = connection.account_no || '-';
        document.getElementById('maAddress').textContent = connection.address || '-';
        document.getElementById('maArea').textContent = connection.area || '-';
        document.getElementById('maMeterReader').textContent = connection.meterReader || '-';
        document.getElementById('maReadingSchedule').textContent = connection.readingSchedule ? new Date(connection.readingSchedule).toLocaleDateString() : '-';
    }
    
    const meterSelect = document.getElementById('maMeterSelect');
    meterSelect.innerHTML = '<option value="">Choose a meter...</option>' + 
        meterAssignmentAvailableMeters.map(m => `<option value="${m.id}">${m.serial} - ${m.type} (${m.brand})</option>`).join('');
    
    document.getElementById('meterAssignmentModal').classList.remove('hidden');
    switchAssignmentTab(type);
    document.getElementById('installationDate').valueAsDate = new Date();
}

function closeMeterAssignmentModal() {
    document.getElementById('meterAssignmentModal').classList.add('hidden');
}

function switchAssignmentTab(type) {
    assignmentType = type;
    document.getElementById('newAssignmentForm').classList.toggle('hidden', type !== 'new');
    document.getElementById('replacementForm').classList.toggle('hidden', type !== 'replacement');
    
    document.getElementById('newAssignmentTab').className = type === 'new' ? 
        'flex-1 px-4 py-2 text-sm font-medium rounded-md bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' :
        'flex-1 px-4 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-600';
    
    document.getElementById('replacementTab').className = type === 'replacement' ? 
        'flex-1 px-4 py-2 text-sm font-medium rounded-md bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' :
        'flex-1 px-4 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-600';
}

function submitMeterAssignment() {
    alert('Meter assignment completed successfully!');
    closeMeterAssignmentModal();
}

window.openMeterAssignmentModal = openMeterAssignmentModal;
window.closeMeterAssignmentModal = closeMeterAssignmentModal;
window.switchAssignmentTab = switchAssignmentTab;
window.submitMeterAssignment = submitMeterAssignment;
</script>
