<!-- Service Connections Tab -->
<div id="connections-content" class="tab-content hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-plug mr-2 text-green-600"></i>Service Connections
        </h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Meter Number</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Date Installed</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 font-mono text-gray-900 dark:text-gray-100" id="conn-meter">-</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300" id="conn-status">Active</span>
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">2022-03-15</td>
                        <td class="px-4 py-3">
                            <button class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Update connection meter when page loads
    document.addEventListener('DOMContentLoaded', function() {
        if (window.currentConsumer) {
            const meterCell = document.getElementById('conn-meter');
            if (meterCell) {
                meterCell.textContent = window.currentConsumer.meter_no;
            }
        }
    });
</script>