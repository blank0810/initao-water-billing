<!-- View Document Modal -->
<div x-data="{ open: false }" x-show="open" x-cloak
    class="fixed inset-0 z-50 overflow-y-auto" 
    @view-document.window="open = true"
    @close-modal.window="open = false">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
         @click="open = false"></div>
    
    <!-- Modal -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden"
             @click.stop>
            
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Document Viewer
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        View consumer document details
                    </p>
                </div>
                <button @click="open = false" 
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Content -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Document Info -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Document Information</h4>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Document ID:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" id="doc-id">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Customer ID:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" id="doc-customer-id">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Document Type:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" id="doc-type">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">File Name:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" id="doc-filename">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Uploaded:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" id="doc-uploaded">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                                    <span class="text-sm font-medium" id="doc-status">-</span>
                                </div>
                                <div class="flex justify-between" id="doc-verified-section" style="display: none;">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Verified By:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" id="doc-verified-by">-</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-download mr-2"></i>
                                Download
                            </button>
                            <button class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors" 
                                    id="verify-btn" style="display: none;">
                                <i class="fas fa-check mr-2"></i>
                                Verify Document
                            </button>
                        </div>
                    </div>
                    
                    <!-- Document Preview -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3">Document Preview</h4>
                        
                        <div class="bg-white dark:bg-gray-600 rounded border-2 border-dashed border-gray-300 dark:border-gray-500 p-8 text-center">
                            <div id="doc-preview">
                                <i class="fas fa-file-alt text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">Document preview not available</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Click download to view the full document</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
                <button @click="open = false" 
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>