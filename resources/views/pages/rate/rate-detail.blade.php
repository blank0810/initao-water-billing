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
                        title="Rate Detail (Increment Range)" 
                        subtitle="Water consumption tier and pricing information"
                    />
                </div>
                <div class="flex gap-2">
                    <x-ui.button variant="secondary" size="md" onclick="downloadDetail()" icon="fas fa-download">
                        Download
                    </x-ui.button>
                    <x-ui.button variant="primary" size="md" onclick="window.print()" icon="fas fa-print">
                        Print
                    </x-ui.button>
                </div>
            </div>

            <!-- Rate Detail Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Detail ID</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">RD-2024-001-T1</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Tier</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">Tier 1</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-orange-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Range</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">0 - 10 m³</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Rate per m³</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">₱10.00</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-red-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Status</p>
                    <p class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold">Active</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Rate Detail Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-sliders-h mr-2"></i>Rate Detail Information
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Detail ID</span>
                                <span class="font-semibold text-gray-900 dark:text-white">RD-2024-001-T1</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Rate Parent (Period)</span>
                                <span class="font-semibold text-gray-900 dark:text-white">BP-2024-01 (January 2024)</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Associated Rate</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Residential Standard (RT-2024-001)</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Tier Level</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Tier 1 (First Tier)</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 dark:text-gray-400">Status</span>
                                <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold">Active</span>
                            </div>
                        </div>
                    </div>

                    <!-- Increment Range Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-chart-bar mr-2"></i>Increment Range Details
                        </h3>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-900/50 rounded-lg p-4">
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">Minimum (m³)</p>
                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">0</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-900/50 rounded-lg p-4">
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">Maximum (m³)</p>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-400">10</p>
                            </div>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <p class="text-yellow-800 dark:text-yellow-200 font-semibold mb-2">
                                <i class="fas fa-info-circle mr-2"></i>Range Interpretation
                            </p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                Consumers in the <strong>Residential Standard</strong> rate class will pay <strong>₱10.00 per m³</strong> for water consumption between <strong>0 and 10 m³</strong> within the billing period.
                            </p>
                        </div>
                    </div>

                    <!-- Pricing Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-tag mr-2"></i>Pricing Details
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Rate per m³</span>
                                <span class="font-bold text-gray-900 dark:text-white text-lg">₱10.00</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Effective From</span>
                                <span class="font-semibold text-gray-900 dark:text-white">January 1, 2024</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Effective To</span>
                                <span class="font-semibold text-gray-900 dark:text-white">January 31, 2024</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 dark:text-gray-400">Last Updated</span>
                                <span class="font-semibold text-gray-900 dark:text-white">December 28, 2023</span>
                            </div>
                        </div>
                    </div>

                    <!-- Calculation Examples -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-calculator mr-2"></i>Calculation Examples
                        </h3>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Showing how charges are calculated for this tier:
                        </p>

                        <div class="space-y-3">
                            <!-- Example 1 -->
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <p class="font-semibold text-gray-900 dark:text-white mb-2">Example 1: Consumer uses 5 m³</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">5 m³ × ₱10.00/m³ = <span class="font-bold text-blue-600 dark:text-blue-400">₱50.00</span></p>
                                <p class="text-xs text-gray-500">Falls within Tier 1 range (0-10 m³)</p>
                            </div>

                            <!-- Example 2 -->
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <p class="font-semibold text-gray-900 dark:text-white mb-2">Example 2: Consumer uses 8 m³</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">8 m³ × ₱10.00/m³ = <span class="font-bold text-blue-600 dark:text-blue-400">₱80.00</span></p>
                                <p class="text-xs text-gray-500">Falls within Tier 1 range (0-10 m³)</p>
                            </div>

                            <!-- Example 3 -->
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-orange-50 dark:bg-orange-900/10">
                                <p class="font-semibold text-gray-900 dark:text-white mb-2">Example 3: Consumer uses 10 m³</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">10 m³ × ₱10.00/m³ = <span class="font-bold text-orange-600 dark:text-orange-400">₱100.00</span></p>
                                <p class="text-xs text-gray-500">At maximum of Tier 1 range</p>
                            </div>

                            <!-- Example 4 -->
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-purple-50 dark:bg-purple-900/10">
                                <p class="font-semibold text-gray-900 dark:text-white mb-2">Example 4: Consumer uses 15 m³</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Tier 1: 10 m³ × ₱10.00/m³ = ₱100.00<br/>
                                    Tier 2: 5 m³ × ₱12.00/m³ = ₱60.00 <span class="font-bold text-purple-600 dark:text-purple-400">(applies next tier)</span><br/>
                                    <strong>Total: ₱160.00</strong>
                                </p>
                                <p class="text-xs text-gray-500">Excess consumption charged at Tier 2 rate</p>
                            </div>
                        </div>
                    </div>

                    <!-- Related Tiers -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-layer-group mr-2"></i>Complete Rate Structure
                        </h3>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            All tiers for the Residential Standard rate in January 2024:
                        </p>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-300 dark:border-gray-600">
                                        <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Tier</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Range (m³)</th>
                                        <th class="text-right py-3 px-4 font-semibold text-gray-900 dark:text-white">Rate/m³</th>
                                        <th class="text-center py-3 px-4 font-semibold text-gray-900 dark:text-white">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Tier 1 (Current) -->
                                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/10">
                                        <td class="py-3 px-4">
                                            <span class="font-semibold text-gray-900 dark:text-white">Tier 1 (Current)</span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">0 - 10 m³</td>
                                        <td class="py-3 px-4 text-right font-bold text-blue-600 dark:text-blue-400">₱10.00</td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-block px-2 py-1 bg-blue-200 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-semibold">Viewing</span>
                                        </td>
                                    </tr>
                                    <!-- Tier 2 -->
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <td class="py-3 px-4">
                                            <span class="font-semibold text-gray-900 dark:text-white">Tier 2</span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">11 - 20 m³</td>
                                        <td class="py-3 px-4 text-right font-bold text-gray-900 dark:text-white">₱12.00</td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="/rate/detail/RD-2024-001-T2" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium text-sm">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    <!-- Tier 3 -->
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <td class="py-3 px-4">
                                            <span class="font-semibold text-gray-900 dark:text-white">Tier 3</span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">21+ m³</td>
                                        <td class="py-3 px-4 text-right font-bold text-gray-900 dark:text-white">₱15.00</td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="/rate/detail/RD-2024-001-T3" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium text-sm">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                            <button onclick="downloadDetail()" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                                <i class="fas fa-download mr-2"></i>Download
                            </button>
                            <button onclick="window.print()" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                            <a href="/rate/show/RT-2024-001" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition text-center">
                                <i class="fas fa-arrow-up mr-2"></i>Parent Rate
                            </a>
                            <a href="/rate" class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition text-center">
                                <i class="fas fa-list mr-2"></i>All Rates
                            </a>
                        </div>
                    </div>

                    <!-- Rate Detail Info -->
                    <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-lg p-4">
                        <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>About Rate Detail
                        </h4>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            Rate Detail = Increment Range. Shows one tier within a rate structure. Multiple tiers define progressive pricing.
                        </p>
                    </div>

                    <!-- Tier Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Tier Information</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-blue-600 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">First tier (base)</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Lowest rate per m³</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-purple-600 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">245 consumers assigned</span>
                            </div>
                        </div>
                    </div>

                    <!-- Related Links -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Related Links</h4>
                        <div class="space-y-2">
                            <a href="/rate/parent/BP-2024-01" class="block text-blue-600 dark:text-blue-400 hover:text-blue-800 text-sm">
                                <i class="fas fa-calendar mr-2"></i>Billing Period (January 2024)
                            </a>
                            <a href="/rate/show/RT-2024-001" class="block text-blue-600 dark:text-blue-400 hover:text-blue-800 text-sm">
                                <i class="fas fa-link mr-2"></i>Parent Rate (Residential)
                            </a>
                            <a href="/rate/history" class="block text-blue-600 dark:text-blue-400 hover:text-blue-800 text-sm">
                                <i class="fas fa-history mr-2"></i>Rate Change History
                            </a>
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
    function downloadDetail() {
        alert('Download rate detail as PDF');
    }

    window.downloadDetail = downloadDetail;
    </script>
</x-app-layout>
