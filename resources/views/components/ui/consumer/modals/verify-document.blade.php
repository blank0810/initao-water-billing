<!-- Verify Document Modal -->
<div x-data="{ open: false }" x-show="open" x-cloak
    class="fixed inset-0 z-50 overflow-y-auto" 
    @verify-document.window="open = true"
    @close-modal.window="open = false">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
         @click="open = false"></div>
    
    <!-- Modal -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full"
             @click.stop>
            
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Verify Document
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Confirm document verification
                    </p>
                </div>
                <button @click="open = false" 
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <p class="text-gray-900 dark:text-white font-medium">
                        Are you sure you want to verify this document?
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        This action will mark the document as verified and cannot be undone.
                    </p>
                </div>
                
                <!-- Document Info -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Document ID:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="verify-doc-id">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Customer ID:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="verify-customer-id">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Document Type:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="verify-doc-type">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">File Name:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" id="verify-filename">-</span>
                        </div>
                    </div>
                </div>
                
                <!-- Verification Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Verification Notes (Optional)
                    </label>
                    <textarea 
                        id="verification-notes"
                        rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        placeholder="Add any notes about the verification..."></textarea>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
                <button @click="open = false" 
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmVerification()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Verify Document
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmVerification() {
    // Get verification notes
    const notes = document.getElementById('verification-notes').value;
    
    // Show success message
    showAlert('Document verified successfully!', 'success');
    
    // Close modal
    window.dispatchEvent(new CustomEvent('close-modal'));
    
    // Refresh table or update UI
    // This would typically make an API call to update the document status
    console.log('Document verified with notes:', notes);
}
</script>