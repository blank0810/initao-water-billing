<div class="mb-4 flex gap-4 items-center">
    <input type="text" id="paymentSearch" placeholder="Search by receipt no or payer..." class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    <div class="flex gap-2">
        <button onclick="billing.exportToExcel('paymentsTableWrapper', 'payments')" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm" title="Export to Excel">
            <i class="fas fa-file-excel"></i>
        </button>
        <button onclick="billing.exportToPDF('paymentsTableWrapper', 'Payments')" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" title="Export to PDF">
            <i class="fas fa-file-pdf"></i>
        </button>
        <button onclick="billing.printTable('paymentsTableWrapper')" class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm" title="Print">
            <i class="fas fa-print"></i>
        </button>
    </div>
</div>

<x-ui.card>
    <div id="paymentsTableWrapper" class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                <tr>
                    <th onclick="billing.sortData('payments', 'receipt_no'); renderPayments();" class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600">Receipt No <i class="fas fa-sort"></i></th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Payer</th>
                    <th onclick="billing.sortData('payments', 'amount_received'); renderPayments();" class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600">Amount <i class="fas fa-sort"></i></th>
                    <th onclick="billing.sortData('payments', 'payment_date'); renderPayments();" class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600">Date <i class="fas fa-sort"></i></th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="paymentsTable" class="divide-y divide-gray-200 dark:divide-gray-700">
            </tbody>
        </table>
    </div>
    <div id="paymentsPagination"></div>
</x-ui.card>

{{-- View Payment Modal - Invoice Style --}}
<div id="viewPaymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="border-b-2 border-blue-600 pb-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payment Receipt</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Official Receipt</p>
                </div>
                <button onclick="closeViewPaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">RECEIPT NUMBER</div>
                <div id="viewPaymentReceipt" class="text-xl font-bold text-blue-600 dark:text-blue-400 font-mono">--</div>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">PAYMENT DATE</div>
                <div id="viewPaymentDate" class="text-lg font-semibold text-gray-900 dark:text-white">--</div>
            </div>
        </div>
        
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase">Payer Information</h3>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Name:</span>
                    <span id="viewPaymentPayer" class="text-sm font-medium text-gray-900 dark:text-white">--</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Account:</span>
                    <span id="viewPaymentAccount" class="text-sm font-medium text-gray-900 dark:text-white">--</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Meter:</span>
                    <span id="viewPaymentMeter" class="text-sm font-medium text-gray-900 dark:text-white">--</span>
                </div>
            </div>
        </div>
        
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase">Payment Details</h3>
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Description</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="viewPaymentAllocations" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>
        
        <div class="border-t-2 border-gray-200 dark:border-gray-600 pt-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">Total Amount Received:</span>
                <span id="viewPaymentAmount" class="text-2xl font-bold text-green-600 dark:text-green-400">₱0.00</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600 dark:text-gray-400">Processed By:</span>
                <span id="viewPaymentUser" class="font-medium text-gray-900 dark:text-white">--</span>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <button onclick="closeViewPaymentModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function renderPayments() {
    if (!window.billingData) {
        setTimeout(renderPayments, 100);
        return;
    }
    const { payments, consumers } = window.billingData;
    const state = billing.paginationState.payments;
    
    if (state.data.length === 0) {
        state.data = [...payments];
    }
    
    const paginatedData = billing.paginate(state.data, state.page, state.perPage);
    const tbody = document.getElementById('paymentsTable');
    tbody.innerHTML = paginatedData.map(payment => {
        const payer = consumers.find(c => c.connection_id === payment.payer_id);
        
        return `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 text-sm font-mono text-gray-900 dark:text-gray-100">${payment.receipt_no}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">${payer?.name || 'N/A'}</td>
                <td class="px-6 py-4 text-sm text-right font-medium text-green-600 dark:text-green-400">₱${payment.amount_received.toFixed(2)}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">${payment.payment_date}</td>
                <td class="px-6 py-4 text-center">
                    <button onclick="viewPaymentDetails(${payment.payment_id})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
    
    billing.changePage('payments', state.page);
}

function viewPaymentDetails(paymentId) {
    const payment = window.billingData.payments.find(p => p.payment_id === paymentId);
    if (!payment) return;
    
    const payer = window.billingData.consumers.find(c => c.connection_id === payment.payer_id);
    const allocations = window.billingData.paymentAllocations.filter(a => a.payment_id === paymentId);
    
    document.getElementById('viewPaymentReceipt').textContent = payment.receipt_no;
    document.getElementById('viewPaymentPayer').textContent = payer?.name || 'N/A';
    document.getElementById('viewPaymentAccount').textContent = payer?.account_no || 'N/A';
    document.getElementById('viewPaymentMeter').textContent = payer?.meter_serial || 'N/A';
    document.getElementById('viewPaymentAmount').textContent = '₱' + payment.amount_received.toFixed(2);
    document.getElementById('viewPaymentDate').textContent = payment.payment_date;
    document.getElementById('viewPaymentUser').textContent = 'Admin User';
    
    const allocTable = document.getElementById('viewPaymentAllocations');
    allocTable.innerHTML = allocations.map(alloc => `
        <tr>
            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">Payment for Bill #${alloc.target_id}</td>
            <td class="px-4 py-2 text-sm text-right font-medium text-gray-900 dark:text-white">₱${alloc.amount_applied.toFixed(2)}</td>
        </tr>
    `).join('');
    
    document.getElementById('viewPaymentModal').classList.remove('hidden');
}

function closeViewPaymentModal() {
    document.getElementById('viewPaymentModal').classList.add('hidden');
}

window.viewPaymentDetails = viewPaymentDetails;
window.renderPayments = renderPayments;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderPayments);
} else {
    setTimeout(renderPayments, 200);
}
</script>
