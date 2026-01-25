<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Purok Name</th>
                    <th scope="col" class="px-6 py-3 text-center">Status</th>
                    <th scope="col" class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="purok in items" :key="purok.p_id">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <!-- Purok Name -->
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            <span x-text="purok.p_desc"></span>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 text-center">
                            <span
                                x-bind:class="purok.stat_id == 2 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                class="px-2 py-1 text-xs font-medium rounded-full"
                                x-text="purok.stat_id == 2 ? 'ACTIVE' : 'INACTIVE'"
                            ></span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <button
                                    @click="viewItem(purok.p_id)"
                                    class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                    title="View Details"
                                >
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                                <button
                                    @click="editItem(purok.p_id)"
                                    class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button
                                    @click="deleteItem(purok.p_id)"
                                    class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                    title="Delete"
                                >
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                <!-- Empty State -->
                <tr x-show="items.length === 0">
                    <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p>No puroks found</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
