<!-- Rate Tabs Component -->
<div class="mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 overflow-x-auto">
            <button onclick="switchRateTab('rate-parents')" id="tab-rate-parents" class="rate-tab border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400 whitespace-nowrap">
                <i class="fas fa-calendar mr-2"></i>Rate Parents (Periods)
            </button>
            <button onclick="switchRateTab('rate-details')" id="tab-rate-details" class="rate-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                <i class="fas fa-layer-group mr-2"></i>Rate Details (Increments)
            </button>
            <button onclick="switchRateTab('consumer-rates')" id="tab-consumer-rates" class="rate-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                <i class="fas fa-users mr-2"></i>Consumer Rates
            </button>
            <button onclick="switchRateTab('rate-history')" id="tab-rate-history" class="rate-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 whitespace-nowrap">
                <i class="fas fa-history mr-2"></i>Rate History
            </button>
        </nav>
    </div>
</div>
