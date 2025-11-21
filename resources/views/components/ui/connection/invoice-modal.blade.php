<!-- Invoice Modal -->
<div id="invoiceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <i class="fas fa-file-invoice text-blue-600 dark:text-blue-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Invoice Details</h3>
            </div>
            <button onclick="closeInvoiceModal()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 mb-6">
                <div class="text-center">
                    <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">MEEDO Water Services</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Service Connection Invoice</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <h5 class="font-semibold text-gray-900 dark:text-white mb-2">Bill To:</h5>
                    <p id="invoiceCustomerName" class="text-gray-700 dark:text-gray-300"></p>
                    <p id="invoiceAccountNo" class="text-sm text-gray-600 dark:text-gray-400"></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Invoice #: <span id="invoiceNumber" class="font-medium"></span></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Date: <span id="invoiceDate" class="font-medium"></span></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status: <span id="invoiceStatus" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200">PAID</span></p>
                </div>
            </div>
            
            <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-700 dark:text-gray-300">Service Connection Fee</span>
                    <span id="invoiceAmount" class="font-semibold text-gray-900 dark:text-white"></span>
                </div>
                <div class="flex justify-between items-center text-lg font-bold border-t border-gray-200 dark:border-gray-600 pt-2">
                    <span class="text-gray-900 dark:text-white">Total Amount</span>
                    <span id="invoiceTotal" class="text-gray-900 dark:text-white"></span>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-green-600 dark:text-green-400 font-medium">
                    <i class="fas fa-check-circle mr-1"></i>Payment Received
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Thank you for your payment!</p>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <button onclick="closeInvoiceModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                Close
            </button>
            <button onclick="printInvoice()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium shadow-sm">
                <i class="fas fa-print mr-2"></i>Print Invoice
            </button>
        </div>
    </div>
</div>