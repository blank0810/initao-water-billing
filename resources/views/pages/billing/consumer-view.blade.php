<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <x-ui.page-header 
                title="Consumer Billing Details" 
                subtitle="View detailed billing information">
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('billing.management') }}" icon="fas fa-arrow-left">
                        Back to List
                    </x-ui.button>
                    <x-ui.button variant="primary" icon="fas fa-download" onclick="billing.exportToExcel('consumerDetailsTable', 'consumer-billing')">
                        Export
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- 2x3 Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @include('pages.billing.consumer-billing-data')
            </div>

        </div>
    </div>

    @vite(['resources/js/data/billing/billing.js'])
    
    <script>
    const connectionId = {{ $connectionId ?? 1001 }};
    
    document.addEventListener('DOMContentLoaded', function() {
        if (window.billing) {
            const details = billing.getConsumerDetails(connectionId);
            updateConsumerView(details);
        }
    });
    
    function updateConsumerView(details) {
        if (window.updateCustomerProfile) updateCustomerProfile(details);
        if (window.updateBillOverview) updateBillOverview(details);
        if (window.updateWaterUsage) updateWaterUsage(details);
        if (window.updateBillSummary) updateBillSummary(details);
        if (window.updateRecentActivities) updateRecentActivities(details);
        if (window.updateBillingHistory) updateBillingHistory(details);
        if (window.updateBillTrendGraph) updateBillTrendGraph(details);
    }
    </script>
</x-app-layout>
