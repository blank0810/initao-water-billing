<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <x-ui.page-header 
                title="Rate Management" 
                subtitle="Manage water rates, billing periods, and pricing structures. Consumers are assigned rates through billing periods."
            >
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="#" icon="fas fa-chart-bar">
                        Overall Data
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Tab Navigation -->
            <x-ui.rate.rate-tabs :activeTab="'rate-parents'" />

            <!-- Tab Content: Rate Parents (Billing Periods) -->
            <div id="content-rate-parents" class="rate-tab-content">
                @include('pages.rate.tabs.rate-parents')
            </div>

            <!-- Tab Content: Rate Details -->
            <div id="content-rate-details" class="rate-tab-content hidden">
                @include('pages.rate.tabs.rate-details')
            </div>

            <!-- Tab Content: Consumer Rates -->
            <div id="content-consumer-rates" class="rate-tab-content hidden">
                @include('pages.rate.tabs.consumer-rates')
            </div>

            <!-- Tab Content: Rate History -->
            <div id="content-rate-history" class="rate-tab-content hidden">
                @include('pages.rate.tabs.rate-history')
            </div>
        </div>
    </div>

    @vite([
        'resources/js/utils/action-functions.js',
        'resources/js/data/rate/rate-data.js'
    ])

    <script>
    function switchRateTab(tab) {
        // Hide all tab contents
        document.querySelectorAll('.rate-tab-content').forEach(el => el.classList.add('hidden'));
        
        // Remove active classes from all tabs
        document.querySelectorAll('.rate-tab').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });
        
        // Show selected tab content
        document.getElementById('content-' + tab).classList.remove('hidden');
        
        // Add active classes to selected tab
        const activeTab = document.getElementById('tab-' + tab);
        activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        activeTab.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
    }

    window.switchRateTab = switchRateTab;

    // Initialize first tab on load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const tableIds = ['rateParentsTable', 'rateDetailsTable', 'consumerRatesTable', 'rateHistoryTable'];
            tableIds.forEach(tableId => {
                if (document.getElementById(tableId)) {
                    new ActionFunctionsManager(tableId);
                }
            });
        }, 100);
        
        switchRateTab('rate-parents');
    });
    </script>
</x-app-layout>
