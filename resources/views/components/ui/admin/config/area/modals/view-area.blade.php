<!-- View Area Modal -->
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
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Area Details
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <!-- Area Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Area Name
                    </label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.a_desc || '-'"></p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Status
                    </label>
                    <div class="mt-1">
                        <span x-bind:class="selectedItem?.stat_id == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="px-2 py-1 text-xs font-medium rounded-full" x-text="selectedItem?.stat_id == 1 ? 'ACTIVE' : 'INACTIVE'"></span>
                    </div>
                </div>

                <!-- Meter Readers Count -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Assigned Meter Readers
                    </label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.meter_readers_count || 0"></p>
                </div>

                <!-- Service Connections Count -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Service Connections
                    </label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.service_connections_count || 0"></p>
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
