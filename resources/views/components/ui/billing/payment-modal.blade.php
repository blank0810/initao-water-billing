<div id="processPaymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-hand-holding-dollar mr-2"></i>Process Payment
            </h3>
            <button onclick="closeProcessPaymentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="paymentForm" class="space-y-5">
            <!-- Customer Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Customer Account <span class="text-red-500">*</span>
                </label>
                <select id="paymentCustomer" onchange="loadCustomerBills()" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select customer account...</option>
                    <option value="1">ACC-2024-001 - Juan Dela Cruz</option>
                    <option value="2">ACC-2024-002 - Maria Santos</option>
                    <option value="3">ACC-2024-003 - Pedro Garcia</option>
                </select>
            </div>

            <!-- Customer Info Card -->
            <div id="customerInfoCard" class="hidden bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-4 border border-blue-200 dark:border-gray-500">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Customer Name</div>
                        <div id="customerName" class="text-sm font-semibold text-gray-900 dark:text-white">-</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Account Number</div>
                        <div id="customerAccount" class="text-sm font-mono font-semibold text-gray-900 dark:text-white">-</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Meter Number</div>
                        <div id="customerMeter" class="text-sm font-mono text-gray-900 dark:text-white">-</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total Outstanding</div>
                        <div id="customerOutstanding" class="text-lg font-bold text-red-600 dark:text-red-400">₱0.00</div>
                    </div>
                </div>
            </div>

            <!-- Unpaid Bills List -->
            <div id="billsSection" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-file-invoice mr-1"></i>Unpaid Bills
                </label>
                <div class="border border-gray-300 dark:border-gray-600 rounded-lg max-h-64 overflow-y-auto">
                    <div id="billsList" class="divide-y divide-gray-200 dark:divide-gray-600">
                        <!-- Bills will be loaded here -->
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Payment Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Amount to Pay <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">₱</span>
                        <input type="number" id="paymentAmount" required placeholder="0.00" min="0.01" step="0.01" class="w-full pl-8 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select id="paymentMethod" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="cash">Cash</option>
                        <option value="check">Check</option>
                        <option value="gcash">GCash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="online">Online Payment</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Payment Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Payment Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="paymentDate" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- OR Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        OR Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="orNumber" required placeholder="e.g., OR-2024-001" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Reference Number (Optional for online payments) -->
            <div id="referenceSection" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Reference Number (for online/bank payments)
                </label>
                <input type="text" id="referenceNumber" placeholder="e.g., Transaction ID, Check Number" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Remarks -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Remarks / Notes
                </label>
                <textarea id="paymentRemarks" rows="3" placeholder="Add any additional notes..." class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            </div>

            <!-- Payment Summary -->
            <div id="paymentSummary" class="hidden bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-green-600 dark:text-green-400 mt-0.5 mr-2"></i>
                    <div class="text-sm text-green-800 dark:text-green-300">
                        <strong>Payment Summary:</strong> You are about to process a payment of <span id="summaryAmount" class="font-bold">₱0.00</span> for <span id="summaryCustomer">-</span> via <span id="summaryMethod">-</span>.
                    </div>
                </div>
            </div>
        </form>
        
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
            <button onclick="closeProcessPaymentModal()" class="px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
            <button onclick="processPaymentSubmit()" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg transition shadow-md hover:shadow-lg">
                <i class="fas fa-check-circle mr-2"></i>Process Payment
            </button>
        </div>
    </div>
</div>

<script>
const customerData = {
    1: { name: 'Juan Dela Cruz', account: 'ACC-2024-001', meter: 'MTR-001', bills: [
        { id: 'BILL-2024-001', period: 'Jan 2024', amount: 739.20, dueDate: '2024-01-25', status: 'Active' }
    ]},
    2: { name: 'Maria Santos', account: 'ACC-2024-002', meter: 'MTR-002', bills: [
        { id: 'BILL-2024-002', period: 'Jan 2024', amount: 535.80, dueDate: '2024-01-25', status: 'Overdue' }
    ]},
    3: { name: 'Pedro Garcia', account: 'ACC-2024-003', meter: 'MTR-003', bills: [
        { id: 'BILL-2024-003', period: 'Feb 2024', amount: 965.60, dueDate: '2024-02-25', status: 'Overdue' }
    ]}
};

function openProcessPaymentModal() {
    document.getElementById('processPaymentModal').classList.remove('hidden');
    document.getElementById('paymentDate').valueAsDate = new Date();
    document.getElementById('paymentForm').reset();
    document.getElementById('customerInfoCard').classList.add('hidden');
    document.getElementById('billsSection').classList.add('hidden');
    document.getElementById('paymentSummary').classList.add('hidden');
    document.getElementById('referenceSection').classList.add('hidden');
}

function loadCustomerBills() {
    const customerId = document.getElementById('paymentCustomer').value;
    if (!customerId) {
        document.getElementById('customerInfoCard').classList.add('hidden');
        document.getElementById('billsSection').classList.add('hidden');
        return;
    }
    
    const customer = customerData[customerId];
    document.getElementById('customerName').textContent = customer.name;
    document.getElementById('customerAccount').textContent = customer.account;
    document.getElementById('customerMeter').textContent = customer.meter;
    
    const totalOutstanding = customer.bills.reduce((sum, b) => sum + b.amount, 0);
    document.getElementById('customerOutstanding').textContent = '₱' + totalOutstanding.toFixed(2);
    document.getElementById('paymentAmount').value = totalOutstanding.toFixed(2);
    
    document.getElementById('customerInfoCard').classList.remove('hidden');
    
    const billsHtml = customer.bills.map(bill => `
        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">${bill.id}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${bill.period} | Due: ${bill.dueDate}</div>
                </div>
                <div class="text-right">
                    <div class="text-base font-bold ${bill.status === 'Overdue' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'}">₱${bill.amount.toFixed(2)}</div>
                    <div class="text-xs px-2 py-1 rounded ${bill.status === 'Overdue' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'}">${bill.status}</div>
                </div>
            </div>
        </div>
    `).join('');
    
    document.getElementById('billsList').innerHTML = billsHtml;
    document.getElementById('billsSection').classList.remove('hidden');
    updatePaymentSummary();
}

function closeProcessPaymentModal() {
    document.getElementById('processPaymentModal').classList.add('hidden');
}

// Show/hide reference section based on payment method
document.addEventListener('DOMContentLoaded', () => {
    const methodSelect = document.getElementById('paymentMethod');
    if (methodSelect) {
        methodSelect.addEventListener('change', function() {
            const refSection = document.getElementById('referenceSection');
            if (['check', 'bank_transfer', 'online', 'gcash'].includes(this.value)) {
                refSection.classList.remove('hidden');
            } else {
                refSection.classList.add('hidden');
            }
            updatePaymentSummary();
        });
    }
    
    const amountInput = document.getElementById('paymentAmount');
    if (amountInput) {
        amountInput.addEventListener('input', updatePaymentSummary);
    }
});

function updatePaymentSummary() {
    const customerId = document.getElementById('paymentCustomer').value;
    const amount = document.getElementById('paymentAmount').value;
    const method = document.getElementById('paymentMethod').selectedOptions[0]?.text;
    
    if (customerId && amount && method) {
        const customer = customerData[customerId];
        document.getElementById('summaryAmount').textContent = '₱' + parseFloat(amount).toFixed(2);
        document.getElementById('summaryCustomer').textContent = customer.name;
        document.getElementById('summaryMethod').textContent = method;
        document.getElementById('paymentSummary').classList.remove('hidden');
    } else {
        document.getElementById('paymentSummary').classList.add('hidden');
    }
}

function processPaymentSubmit() {
    const customer = document.getElementById('paymentCustomer').value;
    const amount = document.getElementById('paymentAmount').value;
    const method = document.getElementById('paymentMethod').value;
    const date = document.getElementById('paymentDate').value;
    const orNumber = document.getElementById('orNumber').value;
    
    if (!customer || !amount || !method || !date || !orNumber) {
        showAlert('Please fill all required fields', 'error');
        return;
    }
    
    if (parseFloat(amount) <= 0) {
        showAlert('Payment amount must be greater than zero', 'error');
        return;
    }
    
    // Simulate payment processing
    showAlert('Payment processed successfully! OR Number: ' + orNumber, 'success');
    closeProcessPaymentModal();
    
    // In real implementation, this would call an API to record the payment
    console.log('Payment processed:', { customer, amount, method, date, orNumber });
}

function showAlert(message, type) {
    const colors = { 
        success: 'bg-green-500', 
        error: 'bg-red-500', 
        info: 'bg-blue-500' 
    };
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        info: 'fa-info-circle'
    };
    
    const alert = document.createElement('div');
    alert.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center gap-3 animate-fade-in`;
    alert.innerHTML = `<i class="fas ${icons[type]}"></i><span>${message}</span>`;
    document.body.appendChild(alert);
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

// Export functions to global scope
window.openProcessPaymentModal = openProcessPaymentModal;
window.closeProcessPaymentModal = closeProcessPaymentModal;
window.loadCustomerBills = loadCustomerBills;
window.processPaymentSubmit = processPaymentSubmit;
</script>
