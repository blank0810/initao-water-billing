<!-- View Schedule Modal -->
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
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full">
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Schedule Details</h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Period</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="selectedItem?.period_name || '-'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Area</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="selectedItem?.area_name || '-'"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Meter Reader</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="selectedItem?.reader_name || '-'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <div class="mt-1">
                            <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getStatusClass(selectedItem?.status)" x-text="getStatusLabel(selectedItem?.status)"></span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Scheduled Start</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="selectedItem?.scheduled_start_date || '-'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Scheduled End</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="selectedItem?.scheduled_end_date || '-'"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Actual Start</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="selectedItem?.actual_start_date || '-'"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Actual End</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="selectedItem?.actual_end_date || '-'"></p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Total Meters</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="selectedItem?.total_meters || 0"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Meters Read</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="selectedItem?.meters_read || 0"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Meters Missed</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="selectedItem?.meters_missed || 0"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Completion</label>
                    <div class="mt-1 flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2.5 rounded-full" :style="'width: ' + (selectedItem?.completion_percentage || 0) + '%'"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="(selectedItem?.completion_percentage || 0) + '%'"></span>
                    </div>
                </div>

                <template x-if="selectedItem?.notes">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Notes</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-line" x-text="selectedItem?.notes"></p>
                    </div>
                </template>

                <div class="grid grid-cols-2 gap-4 text-xs text-gray-400">
                    <div>
                        <span>Created by: </span>
                        <span x-text="selectedItem?.creator_name || '-'"></span>
                    </div>
                    <div>
                        <span>Created: </span>
                        <span x-text="selectedItem?.created_at || '-'"></span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 p-6 border-t dark:border-gray-700">
                <button type="button" @click="closeAllModals()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">Close</button>
            </div>
        </div>
    </div>
</div>
