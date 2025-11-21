<!-- Add Meter Modal -->
<div id="addMeterModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add New Meter</h3>
            <button onclick="closeAddMeterModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meter Serial Number *</label>
                <input type="text" id="meterSerial" required placeholder="MTR-XXX-XXXXX" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meter Brand *</label>
                <select id="meterBrand" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Select brand...</option>
                    <option value="AquaMeter">AquaMeter</option>
                    <option value="FlowTech">FlowTech</option>
                    <option value="WaterPro">WaterPro</option>
                    <option value="HydroSense">HydroSense</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meter Type</label>
                <select id="meterType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="Residential">Residential</option>
                    <option value="Commercial">Commercial</option>
                    <option value="Industrial">Industrial</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purchase Date</label>
                <input type="date" id="purchaseDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                <textarea id="meterNotes" rows="3" placeholder="Optional notes..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeAddMeterModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="submitAddMeter()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="fas fa-check mr-2"></i>Add Meter
            </button>
        </div>
    </div>
</div>

<script>
function openAddMeterModal() {
    document.getElementById('addMeterModal').classList.remove('hidden');
    document.getElementById('purchaseDate').valueAsDate = new Date();
}

function closeAddMeterModal() {
    document.getElementById('addMeterModal').classList.add('hidden');
    document.getElementById('meterSerial').value = '';
    document.getElementById('meterBrand').value = '';
    document.getElementById('meterType').value = 'Residential';
    document.getElementById('meterNotes').value = '';
}

function submitAddMeter() {
    const serial = document.getElementById('meterSerial').value;
    const brand = document.getElementById('meterBrand').value;
    
    if (!serial || !brand) {
        showAlert('Please fill all required fields', 'error');
        return;
    }
    
    showAlert('Meter added successfully!', 'success');
    closeAddMeterModal();
    
    if (window.renderInventory) {
        setTimeout(() => window.renderInventory(), 100);
    }
}

window.openAddMeterModal = openAddMeterModal;
window.closeAddMeterModal = closeAddMeterModal;
window.submitAddMeter = submitAddMeter;
</script>
