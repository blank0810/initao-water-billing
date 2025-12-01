<div id="invoiceViewModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class="fas fa-file-invoice text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Invoice Details</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View invoice information</p>
                </div>
            </div>
            <button onclick="closeInvoiceViewModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Invoice Header -->
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white">MEEDO Water Services</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Water Service Connection Invoice</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Invoice Number</p>
                    <p class="text-lg font-mono font-bold text-purple-600 dark:text-purple-400" id="invoiceViewNumber">-</p>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Customer Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer Name:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="invoiceViewCustomerName">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer Code:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="invoiceViewCustomerCode">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Invoice Date:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="invoiceViewDate">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                        <span class="ml-2"><span id="invoiceViewStatus" class="px-2 py-1 rounded-full text-xs font-semibold">-</span></span>
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Charges Breakdown</h4>
                <div class="space-y-2" id="invoiceViewItems"></div>
                <div class="border-t border-gray-300 dark:border-gray-600 mt-4 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-base font-bold text-gray-900 dark:text-white">Total Amount</span>
                        <span class="text-xl font-bold text-purple-600 dark:text-purple-400" id="invoiceViewTotal">₱0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <button onclick="closeInvoiceViewModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Close
            </button>
            <button onclick="window.print()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-print mr-2"></i>Print
            </button>
        </div>
    </div>
</div>

<script>
function closeInvoiceViewModal() {
    document.getElementById('invoiceViewModal').classList.add('hidden');
}

window.addEventListener('show-invoice', (e) => {
    const data = e.detail;
    
    document.getElementById('invoiceViewNumber').textContent = data.invoice_id || '-';
    document.getElementById('invoiceViewCustomerName').textContent = data.customer_name || '-';
    document.getElementById('invoiceViewCustomerCode').textContent = data.customer_code || '-';
    document.getElementById('invoiceViewDate').textContent = data.invoice_date ? new Date(data.invoice_date).toLocaleDateString() : '-';
    document.getElementById('invoiceViewTotal').textContent = `₱${(data.amount || 0).toLocaleString()}`;
    
    const statusEl = document.getElementById('invoiceViewStatus');
    statusEl.textContent = data.status || '-';
    statusEl.className = data.status === 'PAID' 
        ? 'px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
        : 'px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200';
    
    const itemsContainer = document.getElementById('invoiceViewItems');
    if (data.items && Array.isArray(data.items)) {
        itemsContainer.innerHTML = data.items.map(item => `
            <div class="flex justify-between items-center py-2 text-sm">
                <span class="text-gray-700 dark:text-gray-300">${item.description}</span>
                <span class="font-semibold text-gray-900 dark:text-white">₱${item.amount.toLocaleString()}</span>
            </div>
        `).join('');
    }
    
    document.getElementById('invoiceViewModal').classList.remove('hidden');
});
</script>
