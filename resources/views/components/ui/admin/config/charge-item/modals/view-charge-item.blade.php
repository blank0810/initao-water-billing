<div x-show="showViewModal"
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
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full">
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Charge Item Details
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Name -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Charge Item Name
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.name || '-'"></p>
                    </div>

                    <!-- Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Code
                        </label>
                        <p class="mt-1">
                            <span class="font-mono text-sm bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded" x-text="selectedItem?.code || '-'"></span>
                        </p>
                    </div>

                    <!-- Default Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Default Amount
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">
                            <span x-text="selectedItem?.default_amount ? '₱' + parseFloat(selectedItem.default_amount).toFixed(2) : '-'"></span>
                        </p>
                    </div>

                    <!-- Charge Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Charge Type
                        </label>
                        <div class="mt-1">
                            <span
                                x-bind:class="{
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': selectedItem?.charge_type === 'one_time',
                                    'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300': selectedItem?.charge_type === 'recurring',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': selectedItem?.charge_type === 'per_unit'
                                }"
                                class="px-2 py-1 text-xs font-medium rounded-full capitalize"
                                x-text="selectedItem?.charge_type ? selectedItem.charge_type.replace('_', ' ') : '-'"
                            ></span>
                        </div>
                    </div>

                    <!-- Taxable -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Taxable
                        </label>
                        <div class="mt-1">
                            <span
                                x-bind:class="selectedItem?.is_taxable ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                                class="px-2 py-1 text-xs font-medium rounded-full"
                                x-text="selectedItem?.is_taxable ? 'Yes' : 'No'"
                            ></span>
                        </div>
                    </div>

                    <!-- Customer Charges Count -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Customer Charges
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.charges_count || 0"></p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Status
                        </label>
                        <div class="mt-1">
                            <span
                                x-bind:class="selectedItem?.stat_id == 2 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                class="px-2 py-1 text-xs font-medium rounded-full"
                                x-text="selectedItem?.stat_id == 2 ? 'ACTIVE' : 'INACTIVE'"
                            ></span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Description
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white whitespace-pre-wrap" x-text="selectedItem?.description || 'No description provided'"></p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 p-6 border-t dark:border-gray-700">
                <button
                    type="button"
                    @click="closeAllModals()"
                    class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
