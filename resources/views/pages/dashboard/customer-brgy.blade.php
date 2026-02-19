<!-- Customer Breakdown by Barangay -->
<div x-data="{
    customers: [
        { barangay: 'Poblacion', total: 487, active: 452, pending: 18, inactive: 17 },
        { barangay: 'Tubod', total: 356, active: 328, pending: 15, inactive: 13 },
        { barangay: 'Kamansi', total: 298, active: 275, pending: 12, inactive: 11 },
        { barangay: 'Natubo', total: 412, active: 385, pending: 14, inactive: 13 },
        { barangay: 'Buru-un', total: 267, active: 248, pending: 10, inactive: 9 },
        { barangay: 'Lagtang', total: 189, active: 175, pending: 8, inactive: 6 },
        { barangay: 'Bonbon', total: 234, active: 218, pending: 9, inactive: 7 },
        { barangay: 'Dalakit', total: 178, active: 165, pending: 7, inactive: 6 },
        { barangay: 'Sinaloc', total: 156, active: 142, pending: 8, inactive: 6 },
        { barangay: 'Langcangan', total: 203, active: 189, pending: 8, inactive: 6 },
        { barangay: 'Mahayahay', total: 145, active: 134, pending: 6, inactive: 5 },
        { barangay: 'Punta Silum', total: 196, active: 182, pending: 8, inactive: 6 }
    ],
    getPercentage(active, total) {
        return ((active / total) * 100).toFixed(1) + '%';
    }
}" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 flex flex-col" style="height: 500px;">
    <div class="px-6 pt-4 pb-3 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Customers by Barangay</h3>
    </div>
    <div class="flex-1 overflow-y-auto">
        <table class="min-w-full">
            <thead class="sticky top-0 bg-white dark:bg-gray-900 z-10">
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Barangay</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Active</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Pending</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Inactive</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">% Active</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="customer in customers" :key="customer.barangay">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="customer.barangay"></div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300" x-text="customer.total"></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 dark:bg-green-900 dark:text-green-300" x-text="customer.active"></span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300" x-text="customer.pending"></span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 dark:bg-red-900 dark:text-red-300" x-text="customer.inactive"></span>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="getPercentage(customer.active, customer.total)"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
