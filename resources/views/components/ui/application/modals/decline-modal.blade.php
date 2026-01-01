<div id="declineModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Decline Application</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Provide reason for declining</p>
                </div>
            </div>
            <button onclick="window.approvalManager?.closeDeclineModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded">
                <p class="text-sm text-red-800 dark:text-red-200 font-medium">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    You are about to decline the following customer application
                </p>
            </div>

            <!-- Customer Information -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Customer Details</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Full Name:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="declineCustomerName">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer Code:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="declineCustomerCode">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">ID Info:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="declineIdInfo">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Area:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="declineArea">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Meter Reader:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="declineMeterReader">-</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-600 dark:text-gray-400">Address:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="declineAddress">-</span>
                    </div>
                </div>
            </div>

            <!-- Decline Reason -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Reason for Declining <span class="text-red-500">*</span>
                </label>
                <textarea id="declineReason" rows="4" required placeholder="Please provide a detailed reason..." class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <button onclick="window.approvalManager?.closeDeclineModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="window.approvalManager?.confirmDecline()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-times mr-2"></i>Decline Application
            </button>
        </div>
    </div>
</div>
