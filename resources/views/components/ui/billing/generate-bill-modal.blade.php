<div id="generateBillModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Generate Water Bill</h3>
            <button onclick="closeGenerateBillModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consumer *</label>
                    <select id="billConsumer" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Select Consumer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Billing Period *</label>
                    <input type="month" id="billPeriod" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Previous Reading *</label>
                    <input type="number" id="billPrevReading" step="0.001" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Reading *</label>
                    <input type="number" id="billCurrentReading" step="0.001" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consumption (m³)</label>
                    <input type="text" id="billConsumption" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Due Date *</label>
                    <input type="date" id="billDueDate" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                </div>
            </div>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Estimated Amount:</span>
                    <span id="billEstimatedAmount" class="text-2xl font-bold text-blue-600 dark:text-blue-400">₱0.00</span>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeGenerateBillModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
            <button onclick="submitGenerateBill()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="fas fa-file-invoice mr-2"></i>Generate Bill
            </button>
        </div>
    </div>
</div>

<script>
function openGenerateBillModal() {
    document.getElementById('generateBillModal').classList.remove('hidden');
    const select = document.getElementById('billConsumer');
    if (window.billingData) {
        select.innerHTML = '<option value="">Select Consumer</option>' + 
            window.billingData.consumers.map(c => `<option value="${c.connection_id}">${c.name} - ${c.account_no}</option>`).join('');
    }
}

function closeGenerateBillModal() {
    document.getElementById('generateBillModal').classList.add('hidden');
}

function submitGenerateBill() {
    const consumer = document.getElementById('billConsumer').value;
    const period = document.getElementById('billPeriod').value;
    const prevReading = document.getElementById('billPrevReading').value;
    const currentReading = document.getElementById('billCurrentReading').value;
    const dueDate = document.getElementById('billDueDate').value;
    
    if (!consumer || !period || !prevReading || !currentReading || !dueDate) {
        alert('Please fill all required fields');
        return;
    }
    
    if (window.showToast) {
        showToast('Bill generated successfully!', 'success');
    } else {
        alert('Bill generated successfully!');
    }
    closeGenerateBillModal();
}

document.getElementById('billCurrentReading')?.addEventListener('input', function() {
    const prev = parseFloat(document.getElementById('billPrevReading').value) || 0;
    const current = parseFloat(this.value) || 0;
    const consumption = Math.max(0, current - prev);
    document.getElementById('billConsumption').value = consumption.toFixed(3);
    document.getElementById('billEstimatedAmount').textContent = '₱' + (consumption * 18).toFixed(2);
});

window.openGenerateBillModal = openGenerateBillModal;
window.closeGenerateBillModal = closeGenerateBillModal;
window.submitGenerateBill = submitGenerateBill;
</script>
