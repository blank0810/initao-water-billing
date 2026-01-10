<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header with Back Button -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <a href="/rate" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 mb-2 inline-block">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Rates
                    </a>
                    <x-ui.page-header 
                        title="Billing Period (Rate Parent)" 
                        subtitle="Manage rate assignments for billing periods"
                    />
                </div>
                <div class="flex gap-2">
                    <x-ui.button variant="secondary" size="md" onclick="downloadPeriod()" icon="fas fa-download">
                        Download
                    </x-ui.button>
                    <x-ui.button variant="primary" size="md" onclick="window.print()" icon="fas fa-print">
                        Print
                    </x-ui.button>
                </div>
            </div>

            <!-- Period Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Period ID</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">BP-2024-01</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Period Name</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">January 2024</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-orange-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Associated Rates</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">3</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Status</p>
                    <p class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold">Open</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Period Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-calendar mr-2"></i>Period Information
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Period ID</span>
                                <span class="font-semibold text-gray-900 dark:text-white">BP-2024-01</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Period Name</span>
                                <span class="font-semibold text-gray-900 dark:text-white">January 2024</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Start Date</span>
                                <span class="font-semibold text-gray-900 dark:text-white">January 1, 2024</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">End Date</span>
                                <span class="font-semibold text-gray-900 dark:text-white">January 31, 2024</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Status</span>
                                <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold">Open</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 dark:text-gray-400">Description</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Standard monthly billing period</span>
                            </div>
                        </div>
                    </div>

                    <!-- Associated Rates -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-link mr-2"></i>Associated Rates
                        </h3>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Rates defined and active for this billing period. Consumers are assigned to specific rates within this period.
                        </p>

                        <div class="space-y-3">
                            <!-- Rate 1 -->
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Residential Standard</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">RT-2024-001</p>
                                    </div>
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-xs font-semibold">245 Consumers</span>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <a href="/rate/show/RT-2024-001" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium text-sm">
                                        <i class="fas fa-eye mr-1"></i>View Rate
                                    </a>
                                    <a href="/rate?rate=RT-2024-001&period=BP-2024-01" class="text-green-600 dark:text-green-400 hover:text-green-800 font-medium text-sm">
                                        <i class="fas fa-users mr-1"></i>Consumers
                                    </a>
                                </div>
                            </div>

                            <!-- Rate 2 -->
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Commercial</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">RT-2024-002</p>
                                    </div>
                                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded text-xs font-semibold">45 Consumers</span>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <a href="/rate/show/RT-2024-002" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium text-sm">
                                        <i class="fas fa-eye mr-1"></i>View Rate
                                    </a>
                                    <a href="/rate?rate=RT-2024-002&period=BP-2024-01" class="text-green-600 dark:text-green-400 hover:text-green-800 font-medium text-sm">
                                        <i class="fas fa-users mr-1"></i>Consumers
                                    </a>
                                </div>
                            </div>

                            <!-- Rate 3 -->
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Institutional</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">RT-2024-003</p>
                                    </div>
                                    <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded text-xs font-semibold">12 Consumers</span>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <a href="/rate/show/RT-2024-003" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium text-sm">
                                        <i class="fas fa-eye mr-1"></i>View Rate
                                    </a>
                                    <a href="/rate?rate=RT-2024-003&period=BP-2024-01" class="text-green-600 dark:text-green-400 hover:text-green-800 font-medium text-sm">
                                        <i class="fas fa-users mr-1"></i>Consumers
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Total Consumers</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">302</p>
                            <p class="text-xs text-gray-500 mt-2">Across all rates</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Active Rates</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">3</p>
                            <p class="text-xs text-gray-500 mt-2">In this period</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-orange-500">
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Total Bills Generated</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">302</p>
                            <p class="text-xs text-gray-500 mt-2">One per consumer</p>
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
                            <button onclick="downloadPeriod()" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                                <i class="fas fa-download mr-2"></i>Download
                            </button>
                            <button onclick="window.print()" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                            <a href="/rate" class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition text-center">
                                <i class="fas fa-list mr-2"></i>All Periods
                            </a>
                        </div>
                    </div>

                    <!-- Period Info -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>About Rate Parent
                        </h4>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Rate Parent = Billing Period. This represents one billing cycle with all its associated rates and consumer assignments.
                        </p>
                    </div>

                    <!-- Period Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Period Status</h4>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Period Active</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Rates Configured</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Consumers Assigned</span>
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
    function downloadPeriod() {
        alert('Download period information as PDF');
    }

    window.downloadPeriod = downloadPeriod;
    </script>
</x-app-layout>
