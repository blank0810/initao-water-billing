<div class="mb-6">
    <x-ui.button variant="outline" icon="fas fa-arrow-left" onclick="showRateTable()">
        Back to List
    </x-ui.button>
</div>

<div class="space-y-6">
    <!-- Consumer Profile -->
    <x-ui.card id="rateConsumerProfile" title="Consumer Profile">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center">
                <i class="fas fa-user text-blue-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Name</span>
                    <div id="consumer_name" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-id-card text-purple-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Consumer ID</span>
                    <div id="consumer_id" class="font-semibold text-gray-900 dark:text-white font-mono">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-tachometer-alt text-green-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Meter No.</span>
                    <div id="consumer_meter_no" class="font-semibold text-gray-900 dark:text-white font-mono">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-tag text-orange-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Account Type</span>
                    <div id="consumer_account_type" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
            <div class="flex items-center md:col-span-2">
                <i class="fas fa-map-marker-alt text-red-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Address</span>
                    <div id="consumer_address" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-calendar text-indigo-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Billing Period</span>
                    <div id="consumer_billing_period" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-check-circle text-teal-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Status</span>
                    <div id="consumer_status" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="flex items-center">
                    <i class="fas fa-water text-cyan-500 w-5 mr-3"></i>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Current Consumption</span>
                        <div id="consumer_consumption" class="font-semibold text-cyan-600 dark:text-cyan-400 text-lg">-</div>
                    </div>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-arrow-left text-gray-400 w-5 mr-3"></i>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Previous Reading</span>
                        <div id="consumer_prev_reading" class="font-semibold text-gray-700 dark:text-gray-300">-</div>
                    </div>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-arrow-right text-gray-400 w-5 mr-3"></i>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Current Reading</span>
                        <div id="consumer_curr_reading" class="font-semibold text-gray-700 dark:text-gray-300">-</div>
                    </div>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-dollar-sign text-red-500 w-5 mr-3"></i>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Amount Due</span>
                        <div id="consumer_amount_due" class="font-bold text-red-600 dark:text-red-400 text-xl">-</div>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>

    <!-- Rate Cards -->
    <div id="rateCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900 dark:to-red-800 rounded-lg shadow p-4 border border-red-200 dark:border-red-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-red-600 dark:text-red-300 font-medium uppercase">Total Penalty</div>
                    <div id="cardTotalPenalty" class="text-2xl font-bold text-red-700 dark:text-red-200">₱ 0.00</div>
                </div>
                <i class="fas fa-exclamation-triangle text-3xl text-red-400 dark:text-red-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg shadow p-4 border border-blue-200 dark:border-blue-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-blue-600 dark:text-blue-300 font-medium uppercase">Rate Charge</div>
                    <div id="cardRateCharge" class="text-2xl font-bold text-blue-700 dark:text-blue-200">₱ 0.00</div>
                </div>
                <i class="fas fa-file-invoice-dollar text-3xl text-blue-400 dark:text-blue-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900 dark:to-cyan-800 rounded-lg shadow p-4 border border-cyan-200 dark:border-cyan-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-cyan-600 dark:text-cyan-300 font-medium uppercase">Consumption</div>
                    <div id="cardConsumption" class="text-2xl font-bold text-cyan-700 dark:text-cyan-200">0 m³</div>
                </div>
                <i class="fas fa-tint text-3xl text-cyan-400 dark:text-cyan-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg shadow p-4 border border-green-200 dark:border-green-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-green-600 dark:text-green-300 font-medium uppercase">Discount</div>
                    <div id="cardDiscount" class="text-2xl font-bold text-green-700 dark:text-green-200">₱ 0.00</div>
                </div>
                <i class="fas fa-percent text-3xl text-green-400 dark:text-green-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg shadow p-4 border border-purple-200 dark:border-purple-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-purple-600 dark:text-purple-300 font-medium uppercase">VAT (12%)</div>
                    <div id="cardVAT" class="text-2xl font-bold text-purple-700 dark:text-purple-200">₱ 0.00</div>
                </div>
                <i class="fas fa-receipt text-3xl text-purple-400 dark:text-purple-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900 dark:to-orange-800 rounded-lg shadow p-4 border border-orange-200 dark:border-orange-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-orange-600 dark:text-orange-300 font-medium uppercase">Fixed Charges</div>
                    <div id="cardFixedCharges" class="text-2xl font-bold text-orange-700 dark:text-orange-200">₱ 0.00</div>
                </div>
                <i class="fas fa-coins text-3xl text-orange-400 dark:text-orange-500"></i>
            </div>
        </div>
    </div>

    <!-- Tier Breakdown -->
    <x-ui.card title="Current Month Tier Breakdown">
        <div class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tier</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumption</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rate per m³</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Charge</th>
                    </tr>
                </thead>
                <tbody id="tierBreakdownTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900 rounded-lg">
            <p class="text-xs text-blue-700 dark:text-blue-300">
                <i class="fas fa-info-circle mr-1"></i>
                Tiered pricing encourages water conservation. Lower consumption tiers have lower rates.
            </p>
        </div>
    </x-ui.card>

    <!-- Billing History -->
    <x-ui.card title="Billing History">
        <div class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Month</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumption</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rate Charge</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fixed Charges</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">VAT</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Penalty</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Discount</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Amount</th>
                    </tr>
                </thead>
                <tbody id="consumerRateTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <!-- Billing Trend Chart -->
    <x-ui.card title="Billing Trend Analysis">
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6 text-xs">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-indigo-600 rounded-full mr-2"></div>
                        <span class="text-gray-600 dark:text-gray-400">Total Amount</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-gray-600 dark:text-gray-400">Rate Charge</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-cyan-500 rounded-full mr-2"></div>
                        <span class="text-gray-600 dark:text-gray-400">Consumption</span>
                    </div>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chart-line mr-1"></i>Last 6 Months
                </div>
            </div>
        </div>
        <div id="rateChart" class="w-full h-56 flex items-end justify-center bg-gradient-to-b from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-lg p-4">
        </div>
    </x-ui.card>
</div>
