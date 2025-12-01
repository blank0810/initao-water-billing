<div id="rateSummaryWrapper" class="mb-6">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Overall Rate</h3>
        <a href="{{ route('rate.overall-data') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View Overall Data</a>
    </div>

    <div id="mainRateCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Consumers</div>
            <div id="cardTotalConsumers" class="text-2xl font-bold text-gray-800 dark:text-gray-100">0</div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Consumption (m³)</div>
            <div id="cardTotalConsumption" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">0</div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Rate Charge</div>
            <div id="cardTotalRateCharge" class="text-2xl font-bold text-gray-800 dark:text-gray-100">₱ 0.00</div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Penalty</div>
            <div id="cardTotalPenaltyMain" class="text-2xl font-bold text-red-600 dark:text-red-400">₱ 0.00</div>
        </div>
    </div>
</div>
