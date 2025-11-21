<!-- Receipt Modal -->
<div x-data="{ open: false }" x-show="open" x-cloak
    class="fixed inset-0 z-50 overflow-y-auto" 
    @show-receipt.window="open = true; populateReceiptModal($event.detail)"
    @close-modal.window="open = false">
    
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="open = false"></div>
    
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full" @click.stop>
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Receipt</h3>
            </div>
            <div class="p-6">
                <div id="receipt-details" class="space-y-3">
                    <div class="text-center mb-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Water Service Payment</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Receipt #<span id="receipt-number">-</span></p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Customer:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="receipt-customer">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Invoice:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="receipt-invoice">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Amount:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="receipt-amount">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Method:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="receipt-method">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Date:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="receipt-date">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
                <button @click="open = false" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Close
                </button>
                <button onclick="printReceipt()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function populateReceiptModal(data) {
    document.getElementById('receipt-number').textContent = data.InvoiceNumber || 'N/A';
    document.getElementById('receipt-customer').textContent = data.CustomerName || 'N/A';
    document.getElementById('receipt-invoice').textContent = data.InvoiceNumber || 'N/A';
    document.getElementById('receipt-amount').textContent = data.paymentAmount ? `₱${data.paymentAmount.toLocaleString()}` : 'N/A';
    document.getElementById('receipt-method').textContent = data.PaymentMethod || 'N/A';
    document.getElementById('receipt-date').textContent = data.DateApplied ? new Date(data.DateApplied).toLocaleDateString() : 'N/A';
}

function printReceipt() {
    window.print();
}
</script>