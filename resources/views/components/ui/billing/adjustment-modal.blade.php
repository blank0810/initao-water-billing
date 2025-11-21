<div id="addAdjustmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add Bill Adjustment</h3>
            <button onclick="closeAddAdjustmentModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bill ID *</label>
                    <input type="number" id="adjustmentBillId" required placeholder="Enter bill ID" onchange="loadBillInfo()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consumer</label>
                    <input type="text" id="adjustmentConsumer" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>
            
            <div id="billInfoCard" class="hidden p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Current Amount:</span>
                        <div id="billCurrentAmount" class="font-semibold text-gray-900 dark:text-white">₱0.00</div>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Due Date:</span>
                        <div id="billDueDate" class="font-semibold text-gray-900 dark:text-white">--</div>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                        <div id="billStatus" class="font-semibold text-gray-900 dark:text-white">--</div>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adjustment Type *</label>
                <select id="adjustmentType" required onchange="updateAdjustmentPreview()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Select type...</option>
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount *</label>
                    <input type="number" id="adjustmentAmount" required placeholder="0.00" min="0" step="0.01" onchange="updateAdjustmentPreview()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Direction</label>
                    <input type="text" id="adjustmentDirection" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>
            
            <div id="adjustmentPreview" class="hidden p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">New Total Amount:</span>
                    <span id="newTotalAmount" class="text-xl font-bold text-yellow-600 dark:text-yellow-400">₱0.00</span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks *</label>
                <textarea id="adjustmentRemarks" rows="3" required placeholder="Enter reason for adjustment..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeAddAdjustmentModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="submitAdjustment()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="fas fa-check mr-2"></i>Add Adjustment
            </button>
        </div>
    </div>
</div>

<script>
function openAddAdjustmentModal() {
    document.getElementById('addAdjustmentModal').classList.remove('hidden');
    document.getElementById('billInfoCard').classList.add('hidden');
    document.getElementById('adjustmentPreview').classList.add('hidden');
    
    if (window.billingData) {
        const select = document.getElementById('adjustmentType');
        select.innerHTML = '<option value="">Select type...</option>' + 
            window.billingData.billAdjustmentTypes.map(t => `<option value="${t.bill_adjustment_type_id}" data-direction="${t.direction}">${t.name} (${t.direction})</option>`).join('');
    }
}

function loadBillInfo() {
    const billId = document.getElementById('adjustmentBillId').value;
    const bill = window.billingData.waterBillHistory.find(b => b.bill_id == billId);
    
    if (bill) {
        const consumer = window.billingData.consumers.find(c => c.connection_id === bill.connection_id);
        const statusMap = { 1: 'Active', 2: 'Paid', 3: 'Cancelled', 4: 'Overdue', 5: 'Adjusted' };
        
        document.getElementById('adjustmentConsumer').value = consumer?.name || 'N/A';
        document.getElementById('billCurrentAmount').textContent = '₱' + bill.total_amount.toFixed(2);
        document.getElementById('billDueDate').textContent = bill.due_date;
        document.getElementById('billStatus').textContent = statusMap[bill.stat_id];
        document.getElementById('billInfoCard').classList.remove('hidden');
    } else {
        document.getElementById('billInfoCard').classList.add('hidden');
        showToast('Bill not found', 'error');
    }
}

function updateAdjustmentPreview() {
    const billId = document.getElementById('adjustmentBillId').value;
    const typeSelect = document.getElementById('adjustmentType');
    const amount = parseFloat(document.getElementById('adjustmentAmount').value) || 0;
    
    if (!billId || !typeSelect.value || !amount) return;
    
    const bill = window.billingData.waterBillHistory.find(b => b.bill_id == billId);
    const direction = typeSelect.options[typeSelect.selectedIndex].dataset.direction;
    
    document.getElementById('adjustmentDirection').value = direction === '+' ? 'Increase (+)' : 'Decrease (-)';
    
    if (bill) {
        const newTotal = direction === '+' ? bill.total_amount + amount : bill.total_amount - amount;
        document.getElementById('newTotalAmount').textContent = '₱' + newTotal.toFixed(2);
        document.getElementById('adjustmentPreview').classList.remove('hidden');
    }
}

function closeAddAdjustmentModal() {
    document.getElementById('addAdjustmentModal').classList.add('hidden');
    document.getElementById('adjustmentBillId').value = '';
    document.getElementById('adjustmentConsumer').value = '';
    document.getElementById('adjustmentType').value = '';
    document.getElementById('adjustmentAmount').value = '';
    document.getElementById('adjustmentRemarks').value = '';
}

function submitAdjustment() {
    const billId = document.getElementById('adjustmentBillId').value;
    const type = document.getElementById('adjustmentType').value;
    const amount = document.getElementById('adjustmentAmount').value;
    const remarks = document.getElementById('adjustmentRemarks').value;
    
    if (!billId || !type || !amount || !remarks) {
        showToast('Please fill all required fields', 'error');
        return;
    }
    
    const adjustment = {
        bill_id: parseInt(billId),
        bill_adjustment_type_id: parseInt(type),
        amount: parseFloat(amount),
        remarks: remarks,
        source_type: 'ADJUSTMENT',
        created_at: new Date().toISOString().split('T')[0]
    };
    
    console.log('Adjustment created:', adjustment);
    showToast('Adjustment added successfully!', 'success');
    closeAddAdjustmentModal();
}

window.loadBillInfo = loadBillInfo;
window.updateAdjustmentPreview = updateAdjustmentPreview;

window.openAddAdjustmentModal = openAddAdjustmentModal;
window.closeAddAdjustmentModal = closeAddAdjustmentModal;
window.submitAdjustment = submitAdjustment;
</script>
