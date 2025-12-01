<div id="assignMeterModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                    <i class="fas fa-tachometer-alt text-teal-600 dark:text-teal-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assign Meter</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Complete service connection</p>
                </div>
            </div>
            <button onclick="closeAssignMeterModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Customer Information -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Customer Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Name:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="assignCustomerName">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Account No:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="assignAccountNo">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Address:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="assignAddress">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Area:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="assignArea">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Meter Reader:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="assignMeterReader">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Reading Schedule:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="assignReadingSchedule">-</span>
                    </div>
                </div>
            </div>

            <!-- Meter Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Available Meter <span class="text-red-500">*</span>
                </label>
                <select id="assignMeterSelect" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                    <option value="">Choose a meter...</option>
                </select>
            </div>

            <!-- Installation Details -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Installation Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="assignInstallDate" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Initial Reading <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="assignInitialReading" required placeholder="0" min="0" step="0.01" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Installation Notes</label>
                <textarea id="assignNotes" rows="3" placeholder="Enter any installation notes..." class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500"></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <button onclick="closeAssignMeterModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="confirmMeterAssignment()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-check mr-2"></i>Complete Connection
            </button>
        </div>
    </div>
</div>

<script>
const availableMeters = [
    { id: 'MTR-001', serial: 'WM-2024-001', type: 'Digital', brand: 'Sensus', status: 'Available' },
    { id: 'MTR-002', serial: 'WM-2024-002', type: 'Digital', brand: 'Itron', status: 'Available' },
    { id: 'MTR-003', serial: 'WM-2024-003', type: 'Analog', brand: 'Elster', status: 'Available' },
    { id: 'MTR-004', serial: 'WM-2024-004', type: 'Digital', brand: 'Sensus', status: 'Available' },
    { id: 'MTR-005', serial: 'WM-2024-005', type: 'Smart', brand: 'Kamstrup', status: 'Available' }
];

let selectedConnection = null;

function openAssignMeterModal(connection) {
    selectedConnection = connection;
    
    document.getElementById('assignCustomerName').textContent = connection.customer_name;
    document.getElementById('assignAccountNo').textContent = connection.account_no;
    document.getElementById('assignAddress').textContent = connection.address || 'N/A';
    document.getElementById('assignArea').textContent = connection.area || 'N/A';
    document.getElementById('assignMeterReader').textContent = connection.meterReader || 'N/A';
    document.getElementById('assignReadingSchedule').textContent = connection.readingSchedule ? new Date(connection.readingSchedule).toLocaleDateString() : 'N/A';
    
    const meterSelect = document.getElementById('assignMeterSelect');
    meterSelect.innerHTML = '<option value="">Choose a meter...</option>' + 
        availableMeters.map(m => `<option value="${m.id}">${m.serial} - ${m.type} (${m.brand})</option>`).join('');
    
    document.getElementById('assignInstallDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('assignInitialReading').value = '0';
    document.getElementById('assignNotes').value = '';
    
    document.getElementById('assignMeterModal').classList.remove('hidden');
}

function closeAssignMeterModal() {
    document.getElementById('assignMeterModal').classList.add('hidden');
    selectedConnection = null;
}

function confirmMeterAssignment() {
    const meterId = document.getElementById('assignMeterSelect').value;
    const installDate = document.getElementById('assignInstallDate').value;
    const initialReading = document.getElementById('assignInitialReading').value;
    const notes = document.getElementById('assignNotes').value;
    
    if (!meterId || !installDate || !initialReading) {
        alert('Please fill in all required fields');
        return;
    }
    
    if (selectedConnection) {
        selectedConnection.connection_status = 'COMPLETED';
        selectedConnection.completed_date = installDate;
        selectedConnection.meter_id = meterId;
        selectedConnection.initial_reading = initialReading;
        
        // Move customer to consumer list
        const consumerData = {
            customer_code: selectedConnection.customer_code,
            customer_name: selectedConnection.customer_name,
            account_no: selectedConnection.account_no,
            area: selectedConnection.area,
            meterReader: selectedConnection.meterReader,
            readingSchedule: selectedConnection.readingSchedule,
            meter_id: meterId,
            initial_reading: initialReading,
            connection_date: installDate,
            status: 'Active'
        };
        
        // Store in localStorage for consumer list
        let consumers = JSON.parse(localStorage.getItem('consumerList') || '[]');
        consumers.push(consumerData);
        localStorage.setItem('consumerList', JSON.stringify(consumers));
        
        alert(`✓ Service connection completed!\n\nCustomer: ${selectedConnection.customer_name}\nMeter: ${meterId}\nStatus: CONNECTED\n\nCustomer moved to Consumer List`);
        
        closeAssignMeterModal();
        
        if (window.location.reload) {
            setTimeout(() => window.location.reload(), 1000);
        }
    }
}

window.openAssignMeterModal = openAssignMeterModal;
window.closeAssignMeterModal = closeAssignMeterModal;
window.confirmMeterAssignment = confirmMeterAssignment;
</script>
