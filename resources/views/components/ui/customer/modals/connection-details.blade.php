<div id="connectionDetailsModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Connection Details</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Service connection information</p>
                </div>
            </div>
            <button onclick="closeConnectionDetailsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Connection Information -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Connection Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Account No:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="detailAccountNo">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Connection ID:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="detailConnectionId">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer Type:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="detailCustomerType">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                        <span class="ml-2 font-medium" id="detailStatus">-</span>
                    </div>
                </div>
            </div>

            <!-- Meter Information -->
            <div class="bg-teal-50 dark:bg-teal-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Meter Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Meter No:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="detailMeterNo">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Date Installed:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="detailDateInstalled">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Meter Reader:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="detailMeterReader">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Area:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="detailArea">-</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end">
            <button onclick="closeConnectionDetailsModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-times mr-2"></i>Close
            </button>
        </div>
    </div>
</div>

<script>
let selectedConnectionDetails = null;

function openConnectionDetailsModal(connection) {
    selectedConnectionDetails = connection;
    
    document.getElementById('detailAccountNo').textContent = connection.account_no || 'N/A';
    document.getElementById('detailConnectionId').textContent = connection.connection_id || 'N/A';
    document.getElementById('detailCustomerType').textContent = connection.customer_type || 'Residential';
    
    const statusEl = document.getElementById('detailStatus');
    statusEl.textContent = connection.connection_status || 'N/A';
    statusEl.className = 'ml-2 font-medium inline-flex px-2 py-1 text-xs rounded-full ' + getStatusColorClass(connection.connection_status);
    
    document.getElementById('detailMeterNo').textContent = connection.meter_no || 'N/A';
    document.getElementById('detailDateInstalled').textContent = connection.date_installed ? new Date(connection.date_installed).toLocaleDateString() : 'N/A';
    document.getElementById('detailMeterReader').textContent = connection.meterReader || 'N/A';
    document.getElementById('detailArea').textContent = connection.area || 'N/A';
    
    document.getElementById('connectionDetailsModal').classList.remove('hidden');
}

function closeConnectionDetailsModal() {
    document.getElementById('connectionDetailsModal').classList.add('hidden');
    selectedConnectionDetails = null;
}

function getStatusColorClass(status) {
    const colors = {
        'COMPLETED': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
        'SCHEDULED': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
        'PENDING': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200'
    };
    return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
}

// Make function globally accessible
window.openConnectionDetailsModal = openConnectionDetailsModal;
window.closeConnectionDetailsModal = closeConnectionDetailsModal;
</script>
