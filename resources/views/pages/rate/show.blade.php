<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px:6 lg:px-8">
            
            <!-- Page Header with Back Button -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <a href="/rate" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 mb-2 inline-block">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Rates
                    </a>
                    <x-ui.page-header 
                        title="Rate Details" 
                        subtitle="View rate structure and consumer assignments"
                    />
                </div>
                <div class="flex gap-2">
                    <x-ui.button variant="secondary" size="md" onclick="downloadRate()" icon="fas fa-download">
                        Download
                    </x-ui.button>
                    <x-ui.button variant="primary" size="md" onclick="window.print()" icon="fas fa-print">
                        Print
                    </x-ui.button>
                </div>
            </div>

            <!-- Rate Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Rate ID</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">RT-2024-001</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Category</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">Residential</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-orange-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Consumers Assigned</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">245</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Status</p>
                    <p class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold">Active</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-info-circle mr-2"></i>Basic Information
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Rate ID</span>
                                <span class="font-semibold text-gray-900 dark:text-white">RT-2024-001</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Rate Name</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Residential Standard Rate</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Category</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Residential</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Effective From</span>
                                <span class="font-semibold text-gray-900 dark:text-white">January 1, 2024</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 dark:text-gray-400">Status</span>
                                <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold">Active</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rate Structure (Rate Parent concept) -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-layer-group mr-2"></i>Rate Structure (Billing Period: January 2024)
                        </h3>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Tiered pricing applied based on consumption increments. Each range has its own rate per cubic meter.
                        </p>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                                        <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold">Range</th>
                                        <th class="text-center py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold">From (m³)</th>
                                        <th class="text-center py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold">To (m³)</th>
                                        <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold">Rate/m³</th>
                                        <th class="text-center py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="py-3 px-4 text-gray-900 dark:text-white font-semibold">Tier 1</td>
                                        <td class="text-center py-3 px-4 text-gray-900 dark:text-white">0</td>
                                        <td class="text-center py-3 px-4 text-gray-900 dark:text-white">10</td>
                                        <td class="text-right py-3 px-4"><span class="font-semibold text-blue-600 dark:text-blue-400">₱10.00</span></td>
                                        <td class="text-center py-3 px-4">
                                            <a href="/rate/rate-detail/1" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium text-sm">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="py-3 px-4 text-gray-900 dark:text-white font-semibold">Tier 2</td>
                                        <td class="text-center py-3 px-4 text-gray-900 dark:text-white">11</td>
                                        <td class="text-center py-3 px-4 text-gray-900 dark:text-white">20</td>
                                        <td class="text-right py-3 px-4"><span class="font-semibold text-green-600 dark:text-green-400">₱12.00</span></td>
                                        <td class="text-center py-3 px-4">
                                            <a href="/rate/rate-detail/2" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium text-sm">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="py-3 px-4 text-gray-900 dark:text-white font-semibold">Tier 3</td>
                                        <td class="text-center py-3 px-4 text-gray-900 dark:text-white">21+</td>
                                        <td class="text-center py-3 px-4 text-gray-900 dark:text-white">Unlimited</td>
                                        <td class="text-right py-3 px-4"><span class="font-semibold text-orange-600 dark:text-orange-400">₱15.00</span></td>
                                        <td class="text-center py-3 px-4">
                                            <a href="/rate/rate-detail/3" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium text-sm">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Additional Charges -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-receipt mr-2"></i>Additional Charges
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Basic Service Charge</span>
                                <span class="font-semibold text-gray-900 dark:text-white">₱500.00</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Environmental Fee</span>
                                <span class="font-semibold text-gray-900 dark:text-white">₱0.00</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 dark:text-gray-400">Other Charges</span>
                                <span class="font-semibold text-gray-900 dark:text-white">None</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-bolt mr-2"></i>Quick Actions
                        </h3>
                        
                        <div class="space-y-2">
                            <button onclick="downloadRate()" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                                <i class="fas fa-download mr-2"></i>Download
                            </button>
                            <button onclick="window.print()" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                            <a href="/rate" class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition text-center">
                                <i class="fas fa-list mr-2"></i>All Rates
                            </a>
                        </div>
                    </div>

                    <!-- Rate Info -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>About This Rate
                        </h4>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            This is the active residential rate for January 2024. Consumers assigned to this rate will have bills computed using this tiered pricing structure.
                        </p>
                    </div>

                    <!-- Consumers Count -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Assigned Consumers</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Active Consumers</span>
                                <span class="text-xl font-bold text-gray-900 dark:text-white">245</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">View All</span>
                                <a href="/rate?filter=RT-2024-001" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite([
        'resources/js/utils/action-functions.js'
    ])

    <script>
    function downloadRate() {
        alert('Download rate structure as PDF');
    }

    window.downloadRate = downloadRate;
    </script>
</x-app-layout>
