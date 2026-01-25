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
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Add New Purok
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="createPurok()" class="p-6 space-y-4">
                <!-- Purok Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Purok Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        x-model="form.p_desc"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.p_desc}"
                        placeholder="e.g., Purok 1"
                        required
                    />
                    <template x-if="errors.p_desc">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.p_desc[0]"></p>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
                    <button
                        type="button"
                        @click="closeAllModals()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    >
                        Create Purok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
