<!-- Ledger Entry Details Modal -->
<div id="ledger-entry-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity" onclick="closeLedgerEntryModal()"></div>

        <!-- Centering trick -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <!-- Header -->
            <div class="bg-gray-100 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">
                        <i class="fas fa-file-invoice mr-2 text-blue-600"></i>Transaction Details
                    </h3>
                    <button onclick="closeLedgerEntryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-4" id="ledger-entry-modal-content">
                <!-- Loading state -->
                <div id="ledger-modal-loading" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Loading transaction details...</p>
                </div>

                <!-- Entry details (hidden initially) -->
                <div id="ledger-modal-details" class="hidden space-y-6">
                    <!-- Basic Info Section -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Transaction Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Transaction Date</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-txn-date">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Transaction Type</p>
                                <p class="text-sm" id="modal-txn-type">-</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Description</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-description">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Debit Amount</p>
                                <p class="text-sm font-semibold text-red-600" id="modal-debit">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Credit Amount</p>
                                <p class="text-sm font-semibold text-green-600" id="modal-credit">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Source Document Section -->
                    <div id="modal-source-section" class="hidden">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Source Document</h4>
                        <div id="modal-source-content" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <!-- Source document details will be inserted here -->
                        </div>
                    </div>

                    <!-- Connection Info Section -->
                    <div id="modal-connection-section" class="hidden">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Service Connection</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Account Number</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-account-no">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Account Type</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-account-type">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Info Section -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Audit Information</h4>
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Created By</p>
                                <p class="text-gray-900 dark:text-white" id="modal-created-by">-</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Posted At</p>
                                <p class="text-gray-900 dark:text-white" id="modal-post-ts">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-100 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600 flex justify-end gap-3">
                <button onclick="closeLedgerEntryModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    Close
                </button>
                <a id="modal-receipt-btn" href="#" target="_blank"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors hidden inline-flex items-center">
                    <i class="fas fa-receipt mr-2"></i>View Receipt
                </a>
            </div>
        </div>
    </div>
</div>
