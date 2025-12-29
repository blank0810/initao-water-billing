<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
            <i class="fas fa-file-invoice text-purple-600 dark:text-purple-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white" x-text="paymentType === 'document' ? 'Document Checklist' : 'Invoice Details'">Invoice Details</h3>
    </div>
    <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl" x-show="paymentType === 'application'">
        <div class="flex justify-between mb-2">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Invoice Number:</span>
            <span class="text-sm font-mono font-bold text-gray-900 dark:text-white" id="invoiceNumber"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Invoice Status:</span>
            <span class="text-sm font-bold text-orange-600 dark:text-orange-400" id="invoiceStatus">PENDING</span>
        </div>
    </div>
    <div class="border-t-2 border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
            <i class="fas fa-list-ul text-gray-400"></i>
            <span x-text="paymentType === 'document' ? 'Required Documents' : 'Charges Breakdown'">Charges Breakdown</span>
        </h4>
        <div id="chargesBreakdown" class="space-y-2"></div>
        <div class="border-t-2 border-gray-200 dark:border-gray-700 mt-4 pt-4">
            <div class="flex justify-between items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                <span class="text-lg font-bold text-gray-900 dark:text-white" id="totalLabel">Total Amount:</span>
                <span class="text-2xl font-bold text-green-600 dark:text-green-400" id="totalAmount"></span>
            </div>
        </div>
    </div>
</div>

