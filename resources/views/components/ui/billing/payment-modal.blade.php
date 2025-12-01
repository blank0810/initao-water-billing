<div id="addPaymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Process a Payment</h3>
            <button onclick="closeAddPaymentModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Consumer *</label>
                <select id="paymentConsumer" onchange="loadConsumerBills()" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Select consumer...</option>
                </select>
            </div>

            <div id="consumerInfo" class="hidden bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Account No:</span>
                    <span id="consumerAccount" class="font-medium text-gray-900 dark:text-white"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Meter:</span>
                    <span id="consumerMeter" class="font-medium text-gray-900 dark:text-white"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Total Outstanding:</span>
                    <span id="consumerOutstanding" class="font-bold text-red-600 dark:text-red-400"></span>
                </div>
            </div>

            <div id="billsList" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unpaid Bills</label>
                <div id="billsContainer" class="border border-gray-300 dark:border-gray-600 rounded-lg max-h-48 overflow-y-auto"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount to Pay *</label>
                <input type="number" id="paymentAmount" required placeholder="0.00" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Date *</label>
                <input type="date" id="paymentDate" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeAddPaymentModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="submitPayment()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="fas fa-check mr-2"></i>Process Payment
            </button>
        </div>
    </div>
</div>

<script>
function openAddPaymentModal() {
    document.getElementById('addPaymentModal').classList.remove('hidden');
    document.getElementById('paymentDate').valueAsDate = new Date();
    document.getElementById('paymentAmount').value = '';
    document.getElementById('consumerInfo').classList.add('hidden');
    document.getElementById('billsList').classList.add('hidden');
    
    if (window.billingData) {
        const select = document.getElementById('paymentConsumer');
        select.innerHTML = '<option value="">Select consumer...</option>' + 
            window.billingData.consumers.map(c => `<option value="${c.connection_id}">${c.name}</option>`).join('');
    }
}

function loadConsumerBills() {
    const consumerId = parseInt(document.getElementById('paymentConsumer').value);
    if (!consumerId) {
        document.getElementById('consumerInfo').classList.add('hidden');
        document.getElementById('billsList').classList.add('hidden');
        return;
    }
    
    const consumer = window.billingData.consumers.find(c => c.connection_id === consumerId);
    const unpaidBills = window.billingData.waterBillHistory.filter(b => 
        b.connection_id === consumerId && (b.stat_id === 1 || b.stat_id === 4)
    );
    
    document.getElementById('consumerAccount').textContent = consumer.account_no;
    document.getElementById('consumerMeter').textContent = consumer.meter_serial;
    
    const totalOutstanding = unpaidBills.reduce((sum, b) => sum + b.total_amount, 0);
    document.getElementById('consumerOutstanding').textContent = '₱' + totalOutstanding.toFixed(2);
    document.getElementById('consumerInfo').classList.remove('hidden');
    
    if (unpaidBills.length > 0) {
        const billsHtml = unpaidBills.map(bill => `
            <div class="p-3 border-b border-gray-200 dark:border-gray-600 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-600">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">Bill #${bill.bill_id}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Due: ${bill.due_date}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold ${bill.stat_id === 4 ? 'text-red-600' : 'text-gray-900 dark:text-white'}">₱${bill.total_amount.toFixed(2)}</div>
                        <div class="text-xs ${bill.stat_id === 4 ? 'text-red-500' : 'text-yellow-600'}">${bill.stat_id === 4 ? 'Overdue' : 'Active'}</div>
                    </div>
                </div>
            </div>
        `).join('');
        document.getElementById('billsContainer').innerHTML = billsHtml;
        document.getElementById('billsList').classList.remove('hidden');
        document.getElementById('paymentAmount').value = totalOutstanding.toFixed(2);
    } else {
        document.getElementById('billsList').classList.add('hidden');
    }
}

function closeAddPaymentModal() {
    document.getElementById('addPaymentModal').classList.add('hidden');
}

function submitPayment() {
    const consumer = document.getElementById('paymentConsumer').value;
    const amount = document.getElementById('paymentAmount').value;
    const date = document.getElementById('paymentDate').value;
    
    if (!consumer || !amount || !date) {
        showAlert('Please fill all required fields', 'error');
        return;
    }
    
    showAlert('Payment recorded successfully!', 'success');
    closeAddPaymentModal();
}

function showAlert(message, type) {
    const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-blue-500' };
    const alert = document.createElement('div');
    alert.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
    alert.textContent = message;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
}

window.openAddPaymentModal = openAddPaymentModal;
window.closeAddPaymentModal = closeAddPaymentModal;
window.submitPayment = submitPayment;
window.showAlert = showAlert;
window.loadConsumerBills = loadConsumerBills;
window.openPaymentModalForConsumer = function(connectionId) {
    openAddPaymentModal();
    setTimeout(() => {
        document.getElementById('paymentConsumer').value = connectionId;
        loadConsumerBills();
    }, 100);
};
</script>
