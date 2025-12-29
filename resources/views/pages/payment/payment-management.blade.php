<x-app-layout>
    @php
        $stats = \App\Http\Controllers\PaymentController::calculateStats();
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <x-ui.stat-card 
                    title="Total Billed"
                    :value="$stats['totalBilled']"
                    icon="file-invoice-dollar"
                />
                <x-ui.stat-card 
                    title="Total Paid"
                    :value="$stats['totalPaid']"
                    icon="check-circle"
                />
                <x-ui.stat-card 
                    title="Pending"
                    :value="$stats['totalPending']"
                    icon="clock"
                />
                <x-ui.stat-card 
                    title="Bills Being Processed"
                    :value="$stats['totalTransactions']"
                    icon="receipt"
                />
            </div>

            <!-- Action Functions -->
            <x-ui.action-functions 
                searchPlaceholder="Search by customer, code, or address..."
                filterLabel="All Status"
                :filterOptions="[
                    ['value' => 'Applicant', 'label' => 'Applicant'],
                    ['value' => 'Active / Connected', 'label' => 'Active / Connected'],
                    ['value' => 'Delinquent', 'label' => 'Delinquent'],
                    ['value' => 'Overdue', 'label' => 'Overdue'],
                    ['value' => 'Disconnected', 'label' => 'Disconnected']
                ]"
                :showDateFilter="true"
                :showExport="true"
                tableId="paymentTable"
            />

            <!-- Payment Table -->
            @php
                $headers = [
                    ['key' => 'customer_info', 'label' => 'Customer', 'html' => true],
                    ['key' => 'address', 'label' => 'Address', 'html' => false],
                    ['key' => 'status', 'label' => 'Status', 'html' => true],
                    ['key' => 'date_processed', 'label' => 'Date', 'html' => false],
                    ['key' => 'payment', 'label' => 'Amount', 'html' => true],
                    ['key' => 'actions', 'label' => 'Actions', 'html' => true],
                ];
            @endphp

            <div id="paymentTableWrapper">
                <x-table
                    id="paymentTable"
                    :headers="$headers"
                    :data="[]"
                    :searchable="false"
                    :paginated="true"
                    :pageSize="10"
                    :actions="false"
                />
            </div>
        </div>
    </div>

    @vite(['resources/js/utils/action-functions.js', 'resources/js/data/payment/payment-data.js', 'resources/js/data/payment/payment.js'])
</x-app-layout>
