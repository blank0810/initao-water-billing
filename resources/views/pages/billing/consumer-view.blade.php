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

            <!-- Loading State -->
            <div id="loadingState" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-gray-600 dark:text-gray-400">Loading billing details...</span>
            </div>

            <!-- 2x3 Grid -->
            <div id="contentArea" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 hidden">
                @include('pages.billing.consumer-billing-data')
            </div>

        </div>
    </div>

    @vite(['resources/js/data/billing/billing.js'])

    <script>
    const connectionId = {{ $connectionId ?? 0 }};
    window.currentConnectionId = connectionId;

    document.addEventListener('DOMContentLoaded', function() {
        loadBillingDetails();
    });

    async function loadBillingDetails() {
        const loadingState = document.getElementById('loadingState');
        const contentArea = document.getElementById('contentArea');

        // Validate connectionId
        if (!connectionId || connectionId === 0) {
            showErrorState('Invalid connection ID. Please go back and select a valid consumer.');
            loadingState.classList.add('hidden');
            contentArea.classList.remove('hidden');
            return;
        }

        try {
            const response = await fetch(`/water-bills/connection/${connectionId}`);

            // Check if response is ok (status 200-299)
            if (!response.ok) {
                const errorText = await response.text();
                console.error('API Error:', response.status, errorText);
                showErrorState(`Server error (${response.status}). Please check if you have the required permissions.`);
                return;
            }

            const result = await response.json();

            if (result.success) {
                // Map API data to expected format
                const details = mapApiDataToView(result.data);
                updateConsumerView(details);

                // Store data for payment modal
                window.connectionBillingData = result.data;
            } else {
                console.error('Failed to load billing details:', result.message);
                showErrorState(result.message || 'Failed to load billing details');
            }
        } catch (error) {
            console.error('Error loading billing details:', error);
            showErrorState(`Failed to load billing details: ${error.message}. Please try again.`);
        } finally {
            loadingState.classList.add('hidden');
            contentArea.classList.remove('hidden');
        }
    }

    function mapApiDataToView(data) {
        const currentBill = data.current_bill;

        // Helper to format numbers safely
        const formatNumber = (value, decimals = 3) => {
            const num = parseFloat(value);
            return isNaN(num) ? '0.000' : num.toFixed(decimals);
        };

        return {
            name: data.customer_name,
            id: data.account_no,
            class: data.account_type,
            meterNo: data.meter_serial,
            status: data.status,
            email: data.email,
            phone: data.phone,
            location: data.barangay,
            overdueDays: data.overdue_days,
            currentAmountDue: parseFloat(currentBill?.total_amount) || 0,
            billingPeriod: currentBill?.period || 'N/A',
            billNo: currentBill?.bill_id || 'N/A',
            billingStatus: currentBill?.status || 'N/A',
            issuedDate: currentBill?.bill_date || 'N/A',
            dueDate: currentBill?.due_date || 'N/A',
            totalMonthBills: data.total_bills,
            unpaidMonthBills: data.unpaid_bills,
            totalUnpaidAmount: parseFloat(data.total_unpaid_amount) || 0,
            overallBillingStatus: (data.overall_status || 'Current').toUpperCase(),
            consumption: currentBill ? `${formatNumber(currentBill.consumption)} m³` : '0.000 m³',
            currentUsage: currentBill ? `${formatNumber(currentBill.consumption)} m³` : '0.000 m³',
            meterReading: formatNumber(currentBill?.curr_reading),
            dateRead: currentBill?.reading_date || 'N/A',
            activities: [], // Will be populated from API if available
            billingHistory: data.billing_history || [],
            monthlyTrend: data.monthly_trend || { labels: [], data: [] }
        };
    }

    function showErrorState(message) {
        const contentArea = document.getElementById('contentArea');
        contentArea.innerHTML = `
            <div class="col-span-2 text-center py-12">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Error Loading Data</h3>
                <p class="text-gray-600 dark:text-gray-400">${message}</p>
                <button onclick="loadBillingDetails()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-refresh mr-2"></i>Try Again
                </button>
            </div>
        `;
    }

    function updateConsumerView(details) {
        if (window.updateCustomerProfile) updateCustomerProfile(details);
        if (window.updateBillOverview) updateBillOverview(details);
        if (window.updateWaterUsage) updateWaterUsage(details);
        if (window.updateBillSummary) updateBillSummary(details);
        if (window.updateRecentActivities) updateRecentActivities(details);
        if (window.updateBillingHistoryFromApi) {
            updateBillingHistoryFromApi(details.billingHistory);
        } else if (window.updateBillingHistory) {
            updateBillingHistory(details);
        }
        if (window.updateBillTrendGraphFromApi) {
            updateBillTrendGraphFromApi(details.monthlyTrend);
        } else if (window.updateBillTrendGraph) {
            updateBillTrendGraph(details);
        }
    }
    </script>
</x-app-layout>
