@props(['accountType'])

<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Tier</th>
                <th scope="col" class="px-6 py-3">Range (m³)</th>
                <th scope="col" class="px-6 py-3">Base Rate</th>
                <th scope="col" class="px-6 py-3">Increment Rate</th>
                <th scope="col" class="px-6 py-3">Status</th>
                <th scope="col" class="px-6 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <template x-for="rate in items['{{ $accountType }}']" :key="rate.wr_id">
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <!-- Tier -->
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                        <span x-text="rate.wr_tier"></span>
                    </td>

                    <!-- Range -->
                    <td class="px-6 py-4">
                        <span x-text="rate.wr_rangemin + ' - ' + rate.wr_rangemax"></span>
                    </td>

                    <!-- Base Rate -->
                    <td class="px-6 py-4">
                        <span x-text="'₱' + parseFloat(rate.wr_baserate).toFixed(2)"></span>
                    </td>

                    <!-- Increment Rate -->
                    <td class="px-6 py-4">
                        <span x-text="'₱' + parseFloat(rate.wr_incrate).toFixed(2)"></span>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4">
                        <span
                            x-bind:class="rate.stat_id == 2 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                            class="px-2 py-1 text-xs font-medium rounded-full"
                            x-text="rate.stat_id == 2 ? 'ACTIVE' : 'INACTIVE'"
                        ></span>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <!-- View Button -->
                            <button
                                @click="openViewModal(rate)"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                title="View Details"
                            >
                                <i class="fas fa-eye"></i>
                            </button>

                            <!-- Edit Button -->
                            <button
                                @click="openEditModal(rate)"
                                class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 transition-colors"
                                title="Edit"
                            >
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Delete Button -->
                            <button
                                @click="openDeleteModal(rate)"
                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                title="Delete"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </template>

            <!-- Empty State -->
            <template x-if="!items['{{ $accountType }}'] || items['{{ $accountType }}'].length === 0">
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p>No rate tiers configured for {{ $accountType }}</p>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
</div>
