<!-- Total Customers Card -->
<div
    class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition-shadow p-6">

    <!-- Header with Icon -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                Total Customers
            </h3>
        </div>
        <a href="/connection/service-application/create"
            class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
            title="Add New Customer">
            <i class="fas fa-plus text-sm"></i>
        </a>
    </div>

    <!-- Total Number + Date -->
    <div class="flex items-baseline justify-between gap-4">
        <p id="totalCustomers"
            class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white transition-all duration-300">
            <span class="inline-block animate-pulse">...</span>
        </p>
        <span
            class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2.5 py-1 rounded-full whitespace-nowrap">
            as of <span id="currentDate"></span>
        </span>
    </div>

    <!-- Divider -->
    <div class="border-t border-gray-100 dark:border-gray-800 mt-4 mb-4"></div>

    <!-- Status Section -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <!-- Label -->
        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            STATUS:
        </span>

        <!-- Badges (aligned right) -->
        <div id="statusBadges" class="flex items-center gap-2 flex-wrap opacity-0 transition-opacity duration-300">
            <div
                class="flex items-center gap-1.5 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-300 px-3 py-1.5 rounded-full text-xs font-medium">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span id="activeCount">2,800</span> Active
            </div>
            <div
                class="flex items-center gap-1.5 bg-orange-50 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300 px-3 py-1.5 rounded-full text-xs font-medium">
                <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                <span id="pendingCount">200</span> Pending
            </div>
            <div
                class="flex items-center gap-1.5 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-300 px-3 py-1.5 rounded-full text-xs font-medium">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span id="inactiveCount">121</span> Inactive
            </div>
        </div>
    </div>


</div>



<!-- Balance Inquiry Card -->
<div
    class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition-shadow flex flex-col">

    <!-- Main Content -->
    <div class="p-6">

        <!-- Header with Icon -->
        <div class="flex items-center gap-2 mb-6">
            <i class="fas fa-wallet text-blue-600 dark:text-blue-400"></i>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                    Total Receivables
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Outstanding customer balances</p>
            </div>
        </div>

        <!-- Balance + Percent -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <!-- Balance -->
            <p id="totalBalance"
                class="text-4xl font-extrabold tracking-tight leading-none
              text-gray-900 dark:text-white
              transition-all duration-300">
                <span class="inline-block animate-pulse">...</span>
            </p>

            <!-- Percent Badge -->
            <span id="balancePercent"
                class="inline-flex items-center
                 text-xs font-semibold
                 text-gray-600 dark:text-gray-300
                 bg-gray-100 dark:bg-gray-800
                 px-3 py-1.5
                 rounded-full
                 whitespace-nowrap
                 opacity-0 transition-opacity duration-300">
                78% Paid • 22% Unpaid
            </span>

        </div>


        <!-- Progress Bar -->
        <div class="mt-6 h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div id="progressBar" class="flex h-full opacity-0 transition-all duration-500">
                <div id="progressPaid" class="bg-green-500 h-full transition-all duration-500" style="width: 0%"></div>
                <div id="progressUnpaid" class="bg-red-500 h-full transition-all duration-500" style="width: 0%"></div>
            </div>
        </div>

    </div>

    <!-- Action Button -->
    <div class="border-t border-gray-100 dark:border-gray-800">
        <button x-data @click="$dispatch('open-modal', 'balance-modal')" aria-label="Open customer balance search"
            class="w-full flex items-center justify-center gap-2 py-4 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition rounded-b-2xl focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
            <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
            Search Customer
        </button>
    </div>

</div>

<script>
    const STATS_API = {
        customers: '/api/dashboard/customers',
        receivables: '/api/dashboard/receivables'
    };

    document.addEventListener('DOMContentLoaded', async function() {
        document.getElementById("currentDate").textContent = new Date().toLocaleDateString('en-PH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        try {
            // Replace with real API calls
            // const customersRes = await fetch(STATS_API.customers);
            // const customersData = await customersRes.json();
            // const receivablesRes = await fetch(STATS_API.receivables);
            // const receivablesData = await receivablesRes.json();

            // Simulated data
            await new Promise(resolve => setTimeout(resolve, 300));
            const customersData = {
                total: 3121,
                active: 2800,
                pending: 200,
                inactive: 121
            };

            document.getElementById('totalCustomers').textContent = customersData.total.toLocaleString();
            document.getElementById('activeCount').textContent = customersData.active.toLocaleString();
            document.getElementById('pendingCount').textContent = customersData.pending.toLocaleString();
            document.getElementById('inactiveCount').textContent = customersData.inactive.toLocaleString();
            document.getElementById('statusBadges').classList.remove('opacity-0');

            await new Promise(resolve => setTimeout(resolve, 200));
            const receivablesData = {
                total: 5501.25,
                paid: 4289.98,
                unpaid: 1211.27
            };
            const paidPercent = Math.round((receivablesData.paid / receivablesData.total) * 100);
            const unpaidPercent = 100 - paidPercent;

            document.getElementById('totalBalance').textContent = '₱' + receivablesData.total
                .toLocaleString('en-PH', {
                    minimumFractionDigits: 2
                });
            document.getElementById('balancePercent').textContent =
                `${paidPercent}% Paid • ${unpaidPercent}% Unpaid`;
            document.getElementById('progressPaid').style.width = paidPercent + '%';
            document.getElementById('progressUnpaid').style.width = unpaidPercent + '%';
            document.getElementById('balancePercent').classList.remove('opacity-0');
            document.getElementById('progressBar').classList.remove('opacity-0');
        } catch (error) {
            console.error('Failed to load dashboard stats:', error);
            document.getElementById('totalCustomers').textContent = 'Error';
            document.getElementById('totalBalance').textContent = 'Error';
        }
    });
</script>
