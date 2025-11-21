<!-- Restore Application Modal -->
<div x-data="{ 
    open: false, 
    customerCode: null,
    customerName: '',
    declineReason: '',
    notes: ''
}" 
x-show="open" 
x-cloak
@restore-application.window="
    open = true; 
    customerCode = $event.detail;
    const customer = window.declinedCustomers?.find(c => c.customer_code === $event.detail);
    if (customer) {
        customerName = customer.customer_name;
        customerCode = customer.customer_code;
        declineReason = customer.reason;
    }
"
@close-modal.window="open = false; notes = ''"
class="fixed inset-0 z-50 overflow-y-auto">
    
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="open = false"></div>
    
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full" @click.stop>
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Restore Application</h3>
            </div>
            <div class="p-6">
                <div class="text-center mb-4">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
                        <i class="fas fa-undo text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <p class="text-gray-900 dark:text-white font-medium">
                        Restore this application to the approval queue?
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        This will move the customer back to pending approval status.
                    </p>
                </div>
                
                <!-- Customer Info -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Customer:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="customerName">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Customer Code:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="customerCode">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Decline Reason:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="declineReason">-</span>
                        </div>
                    </div>
                </div>
                
                <!-- Restoration Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Restoration Notes (Optional)
                    </label>
                    <textarea 
                        x-model="notes"
                        rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        placeholder="Add any notes about the restoration..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
                <button @click="open = false; notes = ''" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
                <button @click="
                    window.dispatchEvent(new CustomEvent('confirm-restore', { 
                        detail: { customerCode: customerCode, notes: notes } 
                    }));
                    open = false;
                    notes = '';
                " class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                    <i class="fas fa-undo mr-2"></i>Restore Application
                </button>
            </div>
        </div>
    </div>
</div>
