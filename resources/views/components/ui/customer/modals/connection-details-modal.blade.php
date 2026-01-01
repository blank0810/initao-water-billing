<!-- Connection Details Modal -->
<div id="connectionDetailsModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-plug text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Service Connection Details</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Connection Information</p>
                </div>
            </div>
            <button onclick="document.getElementById('connectionDetailsModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Connection Info -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Connection Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Connection ID:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="connId">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Account No:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="connAccount">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                        <span class="ml-2 font-medium text-green-600" id="connStatus">Active</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Date Connected:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="connDate">-</span>
                    </div>
                </div>
            </div>

            <!-- Service Location -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Location Details</h4>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Service Address:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="connAddress">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Service Type:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="connType">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end">
            <button onclick="document.getElementById('connectionDetailsModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    window.showConnectionDetails = function(data) {
        document.getElementById('connId').textContent = data.connection_id || '-';
        document.getElementById('connAccount').textContent = data.account_no || '-';
        document.getElementById('connDate').textContent = data.connection_date ? new Date(data.connection_date).toLocaleDateString() : '-';
        document.getElementById('connAddress').textContent = data.address || '-';
        document.getElementById('connType').textContent = data.service_type || '-';
        document.getElementById('connectionDetailsModal').classList.remove('hidden');
    };
</script>