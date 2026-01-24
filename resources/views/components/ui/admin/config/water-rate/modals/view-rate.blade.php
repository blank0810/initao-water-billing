<!-- View Water Rate Modal -->
<div x-show="showViewModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAllModals()"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Rate Tier Details
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Account Type -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Account Type
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.accounttype || '-'"></p>
                    </div>

                    <!-- Tier -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Tier
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.wr_tier || '-'"></p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Status
                        </label>
                        <div class="mt-1">
                            <span
                                x-bind:class="selectedItem?.stat_id == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                class="px-2 py-1 text-xs font-medium rounded-full"
                                x-text="selectedItem?.stat_id == 1 ? 'ACTIVE' : 'INACTIVE'"
                            ></span>
                        </div>
                    </div>

                    <!-- Range Minimum -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Range Min (m³)
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.wr_rangemin || '-'"></p>
                    </div>

                    <!-- Range Maximum -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Range Max (m³)
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.wr_rangemax || '-'"></p>
                    </div>

                    <!-- Range Display -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Consumption Range
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="(selectedItem?.wr_rangemin || 0) + ' - ' + (selectedItem?.wr_rangemax || 0) + ' m³'"></p>
                    </div>

                    <!-- Base Rate -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Base Rate
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="'₱' + parseFloat(selectedItem?.wr_baserate || 0).toFixed(2)"></p>
                    </div>

                    <!-- Increment Rate -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Increment Rate
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="'₱' + parseFloat(selectedItem?.wr_incrate || 0).toFixed(2)"></p>
                    </div>

                    <!-- Period -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            Period
                        </label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.p_id ? 'Period-specific rate' : 'Default rate'"></p>
                    </div>
                </div>

                <!-- Rate Calculation Example -->
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-2">
                        <i class="fas fa-calculator mr-2"></i>Rate Calculation Example
                    </h4>
                    <p class="text-sm text-blue-800 dark:text-blue-400">
                        For consumption within <span x-text="selectedItem?.wr_rangemin || 0"></span>-<span x-text="selectedItem?.wr_rangemax || 0"></span> m³:
                    </p>
                    <p class="text-sm text-blue-800 dark:text-blue-400 mt-1">
                        Bill = ₱<span x-text="parseFloat(selectedItem?.wr_baserate || 0).toFixed(2)"></span> (base) +
                        (consumption - <span x-text="selectedItem?.wr_rangemin || 0"></span>) ×
                        ₱<span x-text="parseFloat(selectedItem?.wr_incrate || 0).toFixed(2)"></span>
                    </p>
                </div>
            </div>

            <!-- Footer -->
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
