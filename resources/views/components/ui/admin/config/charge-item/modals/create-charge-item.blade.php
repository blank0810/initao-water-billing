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
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full">
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Add New Charge Item
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="createChargeItem()" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Name -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Charge Item Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            x-model="form.name"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.name}"
                            placeholder="e.g., Connection Fee"
                            required
                        />
                        <template x-if="errors.name">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    <!-- Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Code <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            x-model="form.code"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white font-mono uppercase"
                            :class="{'border-red-500': errors.code}"
                            placeholder="e.g., CONN_FEE"
                            required
                        />
                        <template x-if="errors.code">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.code[0]"></p>
                        </template>
                    </div>

                    <!-- Default Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Default Amount <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            x-model="form.default_amount"
                            step="0.01"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.default_amount}"
                            placeholder="0.00"
                            required
                        />
                        <template x-if="errors.default_amount">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.default_amount[0]"></p>
                        </template>
                    </div>

                    <!-- Charge Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Charge Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            x-model="form.charge_type"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.charge_type}"
                            required
                        >
                            <option value="">Select Type</option>
                            <option value="one_time">One Time</option>
                            <option value="recurring">Recurring</option>
                            <option value="per_unit">Per Unit</option>
                        </select>
                        <template x-if="errors.charge_type">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.charge_type[0]"></p>
                        </template>
                    </div>

                    <!-- Taxable -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Taxable
                        </label>
                        <div class="flex items-center h-10">
                            <input
                                type="checkbox"
                                x-model="form.is_taxable"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            />
                            <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                This charge is taxable
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea
                            x-model="form.description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.description}"
                            placeholder="Optional description..."
                        ></textarea>
                        <template x-if="errors.description">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.description[0]"></p>
                        </template>
                    </div>
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
                        Create Charge Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
