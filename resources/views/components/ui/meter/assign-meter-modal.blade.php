<!-- Assign Meter Modal (Copy from meter-assignment.blade.php) -->
<div id="assignMeterModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Assign Meter to Customer</h3>
            <button onclick="closeAssignMeterModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Customer Selection -->
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 mb-6">
            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Customer Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Customer *</label>
                    <select id="assignCustomerSelect" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Choose customer...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Address</label>
                    <input type="text" id="assignServiceAddress" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Available Meters -->
        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 mb-6">
            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Available Meters</h4>
            <div id="assignAvailableMeters" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            </div>
        </div>

        <!-- Installation Details -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg mb-6">
            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Installation Details</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Technician *</label>
                    <select id="assignTechnicianSelect" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Select technician...</option>
                        <option value="Juan Technician">Juan Technician</option>
                        <option value="Maria Installer">Maria Installer</option>
                        <option value="Pedro Technician">Pedro Technician</option>
                        <option value="Ana Installer">Ana Installer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Installation Date *</label>
                    <input type="date" id="assignInstallationDate" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Initial Reading *</label>
                    <input type="number" id="assignInitialReading" required placeholder="0.0" min="0" step="0.1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Installation Time</label>
                    <input type="time" id="assignInstallationTime" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Installation Notes</label>
                <textarea id="assignInstallationNotes" rows="3" placeholder="Optional installation notes..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3">
            <button onclick="closeAssignMeterModal()" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
            <button onclick="submitAssignMeter()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="fas fa-check mr-2"></i>Assign Meter
            </button>
        </div>
    </div>
</div>

<script>
let assignSelectedMeter = null;

function openAssignMeterModal() {
    document.getElementById('assignMeterModal').classList.remove('hidden');
    loadAssignCustomers();
    loadAssignAvailableMeters();
    document.getElementById('assignInstallationDate').valueAsDate = new Date();
}

function closeAssignMeterModal() {
    document.getElementById('assignMeterModal').classList.add('hidden');
    assignSelectedMeter = null;
}

function loadAssignCustomers() {
    const customers = [
        { id: 1001, name: 'Gelogo, Norben', address: 'Brgy. 1, Main St' },
        { id: 1002, name: 'Sayson, Sarah', address: 'Brgy. 2, Oak Ave' },
        { id: 1005, name: 'Cruz, Manuel', address: 'Brgy. 5, Cedar Ave' }
    ];
    
    const select = document.getElementById('assignCustomerSelect');
    select.innerHTML = '<option value="">Choose customer...</option>' + 
        customers.map(c => `<option value="${c.id}" data-address="${c.address}">${c.name}</option>`).join('');
    
    select.onchange = function() {
        const option = this.options[this.selectedIndex];
        document.getElementById('assignServiceAddress').value = option.dataset.address || '';
    };
}

function loadAssignAvailableMeters() {
    const meters = [
        { mtr_id: 5001, mtr_serial: 'MTR-XYZ-12345', mtr_brand: 'AquaMeter' },
        { mtr_id: 5002, mtr_serial: 'MTR-ABC-67890', mtr_brand: 'FlowTech' },
        { mtr_id: 5006, mtr_serial: 'MTR-MNO-99887', mtr_brand: 'AquaMeter' }
    ];
    
    const container = document.getElementById('assignAvailableMeters');
    container.innerHTML = meters.map(m => `
        <div onclick="selectAssignMeter(${m.mtr_id})" id="assign-meter-${m.mtr_id}" 
             class="p-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-green-500 dark:hover:border-green-400">
            <div class="font-medium text-gray-900 dark:text-white">${m.mtr_serial}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">${m.mtr_brand}</div>
        </div>
    `).join('');
}

function selectAssignMeter(meterId) {
    assignSelectedMeter = meterId;
    document.querySelectorAll('[id^="assign-meter-"]').forEach(el => {
        el.className = 'p-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-green-500 dark:hover:border-green-400';
    });
    document.getElementById('assign-meter-' + meterId).className = 'p-3 border-2 border-green-500 dark:border-green-400 bg-green-50 dark:bg-green-900/20 rounded-lg cursor-pointer';
}

function submitAssignMeter() {
    const customerId = document.getElementById('assignCustomerSelect').value;
    const technician = document.getElementById('assignTechnicianSelect').value;
    const installDate = document.getElementById('assignInstallationDate').value;
    const initialReading = document.getElementById('assignInitialReading').value;
    
    if (!customerId || !assignSelectedMeter || !technician || !installDate || !initialReading) {
        showAlert('Please fill all required fields and select a meter', 'error');
        return;
    }
    
    showAlert('Meter assigned successfully!', 'success');
    closeAssignMeterModal();
}

window.openAssignMeterModal = openAssignMeterModal;
window.closeAssignMeterModal = closeAssignMeterModal;
window.selectAssignMeter = selectAssignMeter;
window.submitAssignMeter = submitAssignMeter;
</script>
