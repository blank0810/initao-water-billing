<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <x-ui.page-header
                title="Rate Management"
                subtitle="Manage billing periods, water rates, and pricing structures"
            >
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('rate.overall-data') }}" icon="fas fa-chart-bar">
                        Overall Data
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="switchTab('periods')" id="tabPeriods" class="tab-button border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                            <i class="fas fa-calendar mr-2"></i>Periods
                        </button>
                        <button onclick="switchTab('period-rates')" id="tabPeriodRates" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                            <i class="fas fa-money-bill-wave mr-2"></i>Period Rates
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Periods Section -->
            <div id="periodsSection">
                @include('pages.rate.periods-content')
            </div>

            <!-- Period Rates Section -->
            <div id="periodRatesSection" class="hidden">
                @include('pages.rate.period-rates-content')
            </div>

        </div>
    </div>
</x-app-layout>

@vite(['resources/js/rate.js'])

<script>
let currentTab = 'periods';

function switchTab(tab) {
    currentTab = tab;

    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });

    // Hide all sections
    document.getElementById('periodsSection').classList.add('hidden');
    document.getElementById('periodRatesSection').classList.add('hidden');

    if(tab === 'periods') {
        document.getElementById('tabPeriods').classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        document.getElementById('tabPeriods').classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        document.getElementById('periodsSection').classList.remove('hidden');
    } else if(tab === 'period-rates') {
        document.getElementById('tabPeriodRates').classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        document.getElementById('tabPeriodRates').classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        document.getElementById('periodRatesSection').classList.remove('hidden');
    }
}

window.switchTab = switchTab;
</script>
