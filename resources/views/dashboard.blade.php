<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
        <div class="lg:col-span-2 flex flex-col gap-6">
            @include('pages.dashboard.stats-cards')
        </div>

        <div class="lg:col-span-3">
            @include('pages.dashboard.revenue-payment-charts')
        </div>
    </div>
    
    @include('pages.dashboard.analytics-chart')
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div>
            @include('pages.dashboard.customer-brgy')
        </div>
        <div>
            @include('pages.dashboard.recent-activities')
        </div>
    </div>

    <div x-data="balanceInquiryModal()">
        @include('pages.dashboard.search.balance-inquiry')
    </div>
</x-app-layout>
