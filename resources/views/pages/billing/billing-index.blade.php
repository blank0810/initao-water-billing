<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <x-ui.page-header title="Billing & Payments" subtitle="Manage water bills, payments, and adjustments">
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('billing.overall-data') }}" icon="fas fa-chart-bar">
                        Overall Data
                    </x-ui.button>
                    <x-ui.button variant="primary" icon="fas fa-plus" onclick="openAddPaymentModal()">
                        Process a Payment
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Summary Cards -->
            @include('components.ui.billing.summary-cards')

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="showBillingTab('consumer')" id="tab-consumer"
                            class="billing-tab border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                            <i class="fas fa-users mr-2"></i>Consumer Billing
                        </button>
                        <button onclick="showBillingTab('collections')" id="tab-collections"
                            class="billing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400">
                            <i class="fas fa-hand-holding-usd mr-2"></i>Collections
                        </button>
                        <button onclick="showBillingTab('details')" id="tab-details"
                            class="billing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>Bill Generation
                        </button>
                        <button onclick="showBillingTab('adjustments')" id="tab-adjustments"
                            class="billing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400">
                            <i class="fas fa-edit mr-2"></i>Adjustments
                        </button>
                        <button onclick="showBillingTab('areas')" id="tab-areas"
                            class="billing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400">
                            <i class="fas fa-map-marker-alt mr-2"></i>Areas & Assignments
                        </button>
                        <button onclick="showBillingTab('schedules')" id="tab-schedules"
                            class="billing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400">
                            <i class="fas fa-calendar-alt mr-2"></i>Reading Schedules
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Content -->
            <div id="content-consumer" class="billing-tab-content">
                @include('pages.billing.consumer-billing')
            </div>

            <div id="content-collections" class="billing-tab-content hidden">
                @include('pages.billing.collections-tab')
            </div>

            <div id="content-details" class="billing-tab-content hidden">
                @include('pages.billing.billing-details-tab')
            </div>

            <div id="content-adjustments" class="billing-tab-content hidden">
                @include('pages.billing.bill-adjustments')
            </div>

            <div id="content-areas" class="billing-tab-content hidden">
                @include('pages.billing.areas-assignments-tab')
            </div>

            <div id="content-schedules" class="billing-tab-content hidden">
                @include('pages.billing.reading-schedule-tab')
            </div>

        </div>
    </div>

    @include('components.ui.billing.payment-modal')
    @include('components.ui.billing.adjustment-modal')
    @include('components.ui.billing.bill-details-modal')
    @include('components.ui.billing.generate-bill-modal')

    @vite(['resources/js/data/billing/billing.js'])

    <script>
        function showBillingTab(tab) {
            document.querySelectorAll('.billing-tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.billing-tab').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            document.getElementById('content-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            document.getElementById('tab-' + tab).classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');

            // Trigger render for the active tab
            setTimeout(() => {
                if (tab === 'consumer' && window.billing) window.billing.renderConsumerBilling();
                if (tab === 'collections' && window.renderCollections) window.renderCollections();
                if (tab === 'details' && window.renderBillingDetails) window.renderBillingDetails();
                if (tab === 'adjustments' && window.renderAdjustments) window.renderAdjustments();
                if (tab === 'areas' && window.initAreasTab) window.initAreasTab();
                if (tab === 'schedules' && window.initSchedulesTab) window.initSchedulesTab();
            }, 100);
        }

        window.showBillingTab = showBillingTab;
    </script>
</x-app-layout>