<div id="deleteCustomerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">This action cannot be undone</p>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded">
                <p class="text-sm text-red-800 dark:text-red-200 font-medium">
                    <i class="fas fa-info-circle mr-2"></i>
                    You are about to permanently delete the following customer record:
                </p>
            </div>

            <!-- Customer Details -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer Name:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="deleteCustomerName">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer Code:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="deleteCustomerCode">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">ID Info:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="deleteIdInfo">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Area:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="deleteArea">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Meter Reader:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="deleteMeterReader">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Account No:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="deleteAccountNo">-</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-600 dark:text-gray-400">Address:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="deleteAddress">-</span>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    All associated data will be permanently removed.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <button onclick="closeDeleteCustomerModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="confirmDeleteCustomer()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-trash mr-2"></i>Delete Permanently
            </button>
        </div>
    </div>
</div>

<script>
let deleteCustomerId = null;

function closeDeleteCustomerModal() {
    document.getElementById('deleteCustomerModal').classList.add('hidden');
    deleteCustomerId = null;
}

function confirmDeleteCustomer() {
    if (deleteCustomerId) {
        window.dispatchEvent(new CustomEvent('confirm-delete-customer', { detail: deleteCustomerId }));
        closeDeleteCustomerModal();
    }
}

window.addEventListener('delete-customer', (e) => {
    deleteCustomerId = e.detail;
    const customer = window.customerAllData?.find(c => c.customer_code === deleteCustomerId);
    
    if (customer) {
        document.getElementById('deleteCustomerName').textContent = `${customer.cust_first_name} ${customer.cust_last_name}`;
        document.getElementById('deleteCustomerCode').textContent = customer.customer_code;
        document.getElementById('deleteIdInfo').textContent = `${customer.id_type || '-'} - ${customer.id_number || '-'}`;
        document.getElementById('deleteArea').textContent = customer.area || '-';
        document.getElementById('deleteMeterReader').textContent = customer.meterReader || '-';
        document.getElementById('deleteAccountNo').textContent = customer.account_no || 'Not Assigned';
        document.getElementById('deleteAddress').textContent = customer.address || '-';
    }
    
    document.getElementById('deleteCustomerModal').classList.remove('hidden');
});
</script>
