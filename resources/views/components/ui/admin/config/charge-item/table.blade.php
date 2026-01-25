<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Name</th>
                    <th scope="col" class="px-6 py-3">Code</th>
                    <th scope="col" class="px-6 py-3">Charge Type</th>
                    <th scope="col" class="px-6 py-3">Default Amount</th>
                    <th scope="col" class="px-6 py-3">Taxable</th>
                    <th scope="col" class="px-6 py-3">Customer Charges</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="chargeItem in items" :key="chargeItem.charge_item_id">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <!-- Name -->
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            <span x-text="chargeItem.name"></span>
                        </td>

                        <!-- Code -->
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded" x-text="chargeItem.code"></span>
                        </td>

                        <!-- Charge Type -->
                        <td class="px-6 py-4">
                            <span
                                x-bind:class="{
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': chargeItem.charge_type === 'one_time',
                                    'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300': chargeItem.charge_type === 'recurring',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': chargeItem.charge_type === 'per_unit'
                                }"
                                class="px-2 py-1 text-xs font-medium rounded-full capitalize"
                                x-text="chargeItem.charge_type?.replace('_', ' ')"
                            ></span>
                        </td>

                        <!-- Default Amount -->
                        <td class="px-6 py-4">
                            <span x-text="'₱' + parseFloat(chargeItem.default_amount).toFixed(2)"></span>
                        </td>

                        <!-- Taxable -->
                        <td class="px-6 py-4">
                            <span
                                x-bind:class="chargeItem.is_taxable ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                                class="px-2 py-1 text-xs font-medium rounded-full"
                                x-text="chargeItem.is_taxable ? 'Yes' : 'No'"
                            ></span>
                        </td>

                        <!-- Customer Charges Count -->
                        <td class="px-6 py-4">
                            <span x-text="chargeItem.charges_count || 0"></span>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4">
                            <span
                                x-bind:class="chargeItem.stat_id == 2 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                class="px-2 py-1 text-xs font-medium rounded-full"
                                x-text="chargeItem.stat_id == 2 ? 'ACTIVE' : 'INACTIVE'"
                            ></span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button
                                    @click="viewItem(chargeItem.charge_item_id)"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="View Details"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button
                                    @click="editItem(chargeItem.charge_item_id)"
                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button
                                    @click="deleteItem(chargeItem.charge_item_id)"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    title="Delete"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                <!-- Empty State -->
                <tr x-show="items.length === 0">
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p>No charge items found</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
