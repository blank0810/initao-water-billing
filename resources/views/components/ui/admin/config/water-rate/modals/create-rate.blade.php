<!-- Create Water Rate Modal -->
<div x-show="showCreateModal"
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
                    Add Rate Tier
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <form @submit.prevent="createRate()" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Account Type -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Account Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            x-model="form.class_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.class_id}"
                            required
                        >
                            <option value="">Select Account Type</option>
                            <template x-for="accountType in accountTypes" :key="accountType.at_id">
                                <option :value="accountType.at_id" x-text="accountType.at_desc"></option>
                            </template>
                        </select>
                        <template x-if="errors.class_id">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.class_id[0]"></p>
                        </template>
                    </div>

                    <!-- Tier -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tier <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            x-model="form.range_id"
                            min="1"
                            step="1"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.range_id}"
                            placeholder="1"
                            required
                        />
                        <template x-if="errors.range_id">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.range_id[0]"></p>
                        </template>
                    </div>

                    <!-- Period (Optional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Period
                        </label>
                        <select
                            x-model="form.period_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.period_id}"
                        >
                            <option value="">Default Rate</option>
                            <template x-for="period in periods" :key="period.per_id">
                                <option :value="period.per_id" x-text="period.per_name"></option>
                            </template>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Leave as "Default Rate" for general pricing</p>
                        <template x-if="errors.period_id">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.period_id[0]"></p>
                        </template>
                    </div>

                    <!-- Range Minimum -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Range Min (m³) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            x-model="form.range_min"
                            min="0"
                            step="1"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.range_min}"
                            placeholder="0"
                            required
                        />
                        <template x-if="errors.range_min">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.range_min[0]"></p>
                        </template>
                    </div>

                    <!-- Range Maximum -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Range Max (m³) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            x-model="form.range_max"
                            min="0"
                            step="1"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.range_max}"
                            placeholder="10"
                            required
                        />
                        <template x-if="errors.range_max">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.range_max[0]"></p>
                        </template>
                    </div>

                    <!-- Rate per cu.m. -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rate per cu.m. (₱) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            x-model="form.rate_val"
                            min="0"
                            step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.rate_val}"
                            placeholder="15.00"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">Bill = Consumption x Rate per cu.m.</p>
                        <template x-if="errors.rate_val">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.rate_val[0]"></p>
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
                        Create Rate Tier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
