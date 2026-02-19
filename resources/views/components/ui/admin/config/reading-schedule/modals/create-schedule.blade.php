<!-- Create Schedule Modal -->
<div x-show="showCreateModal"
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
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Add Reading Schedule</h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="createSchedule()" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Period -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Period <span class="text-red-500">*</span>
                        </label>
                        <select x-model="form.period_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Period</option>
                            <template x-for="period in periods" :key="period.id">
                                <option :value="period.id" x-text="period.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Area -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Area <span class="text-red-500">*</span>
                        </label>
                        <select x-model="form.area_id" @change="onAreaChange()" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Area</option>
                            <template x-for="area in areas" :key="area.id">
                                <option :value="area.id" x-text="area.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Meter Reader -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Meter Reader <span class="text-red-500">*</span>
                    </label>
                    <select x-model="form.reader_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Reader</option>
                        <template x-for="reader in meterReaders" :key="reader.id">
                            <option :value="reader.id" x-text="reader.label"></option>
                        </template>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" x-model="form.scheduled_start_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" />
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" x-model="form.scheduled_end_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" />
                    </div>
                </div>

                <!-- Total Meters -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Meters (Optional)</label>
                    <input type="number" x-model="form.total_meters" min="0" placeholder="Auto-calculated if empty" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" />
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                    <textarea x-model="form.notes" rows="2" placeholder="Additional notes..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="closeAllModals()" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Create Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>
