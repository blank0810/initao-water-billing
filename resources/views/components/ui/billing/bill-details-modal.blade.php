<div id="billDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Consumer Bill Details</h3>
            <button onclick="closeBillDetailsModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Consumer Info</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Name:</span>
                        <span id="detail-name" class="font-medium text-gray-900 dark:text-white">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Account:</span>
                        <span id="detail-account" class="font-medium text-gray-900 dark:text-white">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Meter:</span>
                        <span id="detail-meter" class="font-medium text-gray-900 dark:text-white">-</span>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Billing Summary</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Total Due:</span>
                        <span id="detail-due" class="font-medium text-red-600 dark:text-red-400">₱0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Unpaid Bills:</span>
                        <span id="detail-unpaid" class="font-medium text-gray-900 dark:text-white">0</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Bill History</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Period</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300">Consumption</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300">Amount</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody id="detail-bills" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="flex justify-end">
            <button onclick="closeBillDetailsModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function openBillDetailsModal(consumer) {
    document.getElementById('billDetailsModal').classList.remove('hidden');
    
    document.getElementById('detail-name').textContent = consumer.name;
    document.getElementById('detail-account').textContent = consumer.account_no;
    document.getElementById('detail-meter').textContent = consumer.meter_serial;
    document.getElementById('detail-due').textContent = '₱' + consumer.total_due.toFixed(2);
    document.getElementById('detail-unpaid').textContent = consumer.unpaid_bills;
    
    const bills = window.billingData.waterBillHistory.filter(b => b.connection_id === consumer.connection_id);
    const tbody = document.getElementById('detail-bills');
    tbody.innerHTML = bills.map(bill => {
        const statusMap = { 1: 'Unpaid', 2: 'Paid', 3: 'Overdue', 4: 'Partially Paid' };
        const statusClass = { 1: 'bg-yellow-100 text-yellow-800', 2: 'bg-green-100 text-green-800', 3: 'bg-red-100 text-red-800', 4: 'bg-orange-100 text-orange-800' };
        
        return `
            <tr>
                <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">Period ${bill.period_id}</td>
                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-100">${bill.consumption.toFixed(2)} m³</td>
                <td class="px-4 py-2 text-sm text-right font-medium text-gray-900 dark:text-gray-100">₱${bill.total_amount.toFixed(2)}</td>
                <td class="px-4 py-2 text-center">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass[bill.stat_id]}">${statusMap[bill.stat_id]}</span>
                </td>
            </tr>
        `;
    }).join('');
}

function closeBillDetailsModal() {
    document.getElementById('billDetailsModal').classList.add('hidden');
}

window.openBillDetailsModal = openBillDetailsModal;
window.closeBillDetailsModal = closeBillDetailsModal;
</script>
