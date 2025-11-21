<div class="mb-6">
    <x-ui.button variant="outline" icon="fas fa-arrow-left" onclick="showMeterTable()">
        Back to List
    </x-ui.button>
</div>

<div class="space-y-6">
    <!-- Consumer & Meter Profile -->
    <x-ui.card title="Consumer & Meter Information">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center">
                <i class="fas fa-user text-blue-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Consumer Name</span>
                    <div id="meter_consumer_name" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-id-card text-purple-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Consumer ID</span>
                    <div id="meter_consumer_id" class="font-semibold text-gray-900 dark:text-white font-mono">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-tachometer-alt text-green-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Meter No.</span>
                    <div id="meter_no" class="font-semibold text-gray-900 dark:text-white font-mono">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-barcode text-orange-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Serial No.</span>
                    <div id="meter_serial" class="font-semibold text-gray-900 dark:text-white font-mono">-</div>
                </div>
            </div>
            <div class="flex items-center md:col-span-2">
                <i class="fas fa-map-marker-alt text-red-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Address</span>
                    <div id="meter_consumer_address" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-industry text-indigo-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Brand & Model</span>
                    <div id="meter_brand" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-check-circle text-teal-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Status</span>
                    <div id="meter_status" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="flex items-center">
                    <i class="fas fa-calendar text-gray-400 w-5 mr-3"></i>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Install Date</span>
                        <div id="meter_install_date" class="font-semibold text-gray-700 dark:text-gray-300">-</div>
                    </div>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-wrench text-gray-400 w-5 mr-3"></i>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Last Maintenance</span>
                        <div id="meter_last_maintenance" class="font-semibold text-gray-700 dark:text-gray-300">-</div>
                    </div>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-map-pin text-gray-400 w-5 mr-3"></i>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Zone</span>
                        <div id="meter_zone" class="font-semibold text-gray-700 dark:text-gray-300">-</div>
                    </div>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-tag text-gray-400 w-5 mr-3"></i>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Account Type</span>
                        <div id="meter_account_type" class="font-semibold text-gray-700 dark:text-gray-300">-</div>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>

    <!-- Reading Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg shadow p-4 border border-blue-200 dark:border-blue-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-blue-600 dark:text-blue-300 font-medium uppercase">Current Reading</div>
                    <div id="meter_current_reading" class="text-2xl font-bold text-blue-700 dark:text-blue-200">0 m³</div>
                </div>
                <i class="fas fa-tachometer-alt text-3xl text-blue-400 dark:text-blue-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg shadow p-4 border border-purple-200 dark:border-purple-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-purple-600 dark:text-purple-300 font-medium uppercase">Previous Reading</div>
                    <div id="meter_previous_reading" class="text-2xl font-bold text-purple-700 dark:text-purple-200">0 m³</div>
                </div>
                <i class="fas fa-history text-3xl text-purple-400 dark:text-purple-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg shadow p-4 border border-green-200 dark:border-green-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-green-600 dark:text-green-300 font-medium uppercase">Consumption</div>
                    <div id="meter_consumption" class="text-2xl font-bold text-green-700 dark:text-green-200">0 m³</div>
                </div>
                <i class="fas fa-water text-3xl text-green-400 dark:text-green-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900 dark:to-orange-800 rounded-lg shadow p-4 border border-orange-200 dark:border-orange-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-orange-600 dark:text-orange-300 font-medium uppercase">Avg Daily</div>
                    <div id="meter_avg_daily" class="text-2xl font-bold text-orange-700 dark:text-orange-200">0 m³</div>
                </div>
                <i class="fas fa-chart-line text-3xl text-orange-400 dark:text-orange-500"></i>
            </div>
        </div>
    </div>

    <!-- Reading History -->
    <x-ui.card title="Reading History">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Reading</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Consumption</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Reader</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                    </tr>
                </thead>
                <tbody id="readingHistoryTable" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <!-- Consumption Chart -->
    <x-ui.card title="Consumption Trend">
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chart-bar mr-1"></i>Last 6 Months
                </div>
            </div>
        </div>
        <div id="consumptionChart" class="w-full h-56 flex items-end justify-center bg-gradient-to-b from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-lg p-4">
        </div>
    </x-ui.card>
</div>
