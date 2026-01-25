<table class="w-full">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Barangay Name
            </th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
            </th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        <template x-if="items.length === 0">
            <tr>
                <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p>No barangays found</p>
                </td>
            </tr>
        </template>

        <template x-for="barangay in items" :key="barangay.b_id">
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="barangay.b_desc"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span x-bind:class="barangay.stat_id == 2 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="px-2 py-1 text-xs font-medium rounded-full" x-text="barangay.stat_id == 2 ? 'ACTIVE' : 'INACTIVE'"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-2">
                        <button
                            @click="openViewModal(barangay)"
                            class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                            title="View Details"
                        >
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                        <button
                            @click="openEditModal(barangay)"
                            class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                            title="Edit"
                        >
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                        <button
                            @click="openDeleteModal(barangay)"
                            class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                            title="Delete"
                        >
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </template>
    </tbody>
</table>
