<!-- Edit Water Rate Modal -->
<div x-show="showEditModal"
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
                    Edit Rate Tier
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <form @submit.prevent="updateRate()" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Account Type -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Account Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            x-model="form.accounttype"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.accounttype}"
                            required
                        >
                            <option value="">Select Account Type</option>
                            <option value="Residential">Residential</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Industrial">Industrial</option>
                            <option value="Government">Government</option>
                            <option value="Institutional">Institutional</option>
                        </select>
                        <template x-if="errors.accounttype">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.accounttype[0]"></p>
                        </template>
                    </div>

                    <!-- Tier -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tier <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            x-model="form.wr_tier"
                            min="1"
                            step="1"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.wr_tier}"
                            required
                        />
                        <template x-if="errors.wr_tier">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.wr_tier[0]"></p>
                        </template>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            x-model="form.stat_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.stat_id}"
                            required
                        >
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                        </select>
                        <template x-if="errors.stat_id">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.stat_id[0]"></p>
                        </template>
                    </div>

                    <!-- Range Minimum -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Range Min (m³) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            x-model="form.wr_rangemin"
                            min="0"
                            step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.wr_rangemin}"
                            required
                        />
                        <template x-if="errors.wr_rangemin">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.wr_rangemin[0]"></p>
                        </template>
                    </div>

                    <!-- Range Maximum -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Range Max (m³) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            x-model="form.wr_rangemax"
                            min="0"
                            step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.wr_rangemax}"
                            required
                        />
                        <template x-if="errors.wr_rangemax">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.wr_rangemax[0]"></p>
                        </template>
                    </div>

                    <!-- Base Rate -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Base Rate (₱) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            x-model="form.wr_baserate"
                            min="0"
                            step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.wr_baserate}"
                            required
                        />
                        <template x-if="errors.wr_baserate">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.wr_baserate[0]"></p>
                        </template>
                    </div>

                    <!-- Increment Rate -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Increment Rate (₱) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            x-model="form.wr_incrate"
                            min="0"
                            step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            :class="{'border-red-500': errors.wr_incrate}"
                            required
                        />
                        <template x-if="errors.wr_incrate">
                            <p class="mt-1 text-sm text-red-600" x-text="errors.wr_incrate[0]"></p>
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
                        Update Rate Tier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
