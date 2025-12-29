<div id="approveModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Approve Application</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Review and confirm approval</p>
                </div>
            </div>
            <button onclick="window.approvalManager?.closeApproveModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded">
                <p class="text-sm text-green-800 dark:text-green-200 font-medium">
                    <i class="fas fa-info-circle mr-2"></i>
                    You are about to approve the following customer application
                </p>
            </div>

            <!-- Customer Information -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Customer Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Full Name:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="approveCustomerName">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer Code:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="approveCustomerCode">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">ID Info:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="approveIdInfo">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Registration Type:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="approveRegType">-</span>
                    </div>
                </div>
            </div>

            <!-- Service Location -->
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Service Location</h4>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Address:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="approveAddress">-</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Area:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white" id="approveArea">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Meter Reader:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white" id="approveMeterReader">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Reading Schedule:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white" id="approveReadingSchedule">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Payment Information</h4>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Invoice ID:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="approveInvoiceId">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Amount Paid:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="approveAmount">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Application Date:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="approveAppDate">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <button onclick="window.approvalManager?.closeApproveModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="window.approvalManager?.confirmApprove()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-check mr-2"></i>Approve Application
            </button>
        </div>
    </div>
</div>
