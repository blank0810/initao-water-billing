<div x-show="showDeleteModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAllModals()"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-500"></i>
                    </div>
                    <h3 class="ml-4 text-xl font-semibold text-gray-900 dark:text-white">
                        Delete Charge Item
                    </h3>
                </div>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6">
                <p class="text-gray-700 dark:text-gray-300">
                    Are you sure you want to delete this charge item?
                </p>
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Name:</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="selectedItem?.name"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Code:</span>
                        <span class="font-mono font-medium text-gray-900 dark:text-white" x-text="selectedItem?.code"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Default Amount:</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            <span x-text="selectedItem?.default_amount ? '₱' + parseFloat(selectedItem.default_amount).toFixed(2) : '-'"></span>
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Customer Charges:</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="selectedItem?.charges_count || 0"></span>
                    </div>
                </div>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    This action cannot be undone. This charge item will be permanently removed from the system.
                </p>
            </div>

            <div class="flex justify-end gap-3 p-6 border-t dark:border-gray-700">
                <button
                    type="button"
                    @click="closeAllModals()"
                    class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    @click="deleteChargeItem()"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                >
                    Delete Charge Item
                </button>
            </div>
        </div>
    </div>
</div>
