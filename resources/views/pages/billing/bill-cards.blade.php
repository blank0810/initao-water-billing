{{-- Customer Info Card --}}
<div id="customerInfoCard"
    class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 space-y-6 hidden">

    <div class="flex justify-between items-start">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white tracking-tight">
            Consumer Information
        </h3>

        <span id="overdueBadge"
              class="hidden px-3 py-1 bg-red-500 text-white rounded-full text-sm font-semibold shadow-sm">
            Overdue by <span id="overdueDays">0</span> day(s)
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Left Info --}}
        <div class="space-y-4">
            <h4 id="customerName"
                class="text-2xl font-bold text-gray-900 dark:text-white leading-tight"></h4>

            <div class="space-y-3 text-sm">
                <div class="flex items-center">
                    <span class="text-gray-500 dark:text-gray-400 w-28">ID:</span>
                    <span id="customerId"
                          class="text-gray-900 dark:text-white font-mono"></span>
                </div>

                <div class="flex items-center">
                    <span class="text-gray-500 dark:text-gray-400 w-28">Class:</span>
                    <span id="customerClass"
                          class="text-gray-900 dark:text-white"></span>
                </div>

                <div class="flex items-center">
                    <span class="text-gray-500 dark:text-gray-400 w-28">Meter No:</span>
                    <span id="meterNo"
                          class="text-gray-900 dark:text-white font-mono"></span>
                </div>
            </div>
        </div>

        {{-- Right Amount Card --}}
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-5 text-white flex flex-col justify-between shadow-inner">
            <div>
                <div class="text-sm opacity-90">Current Amount Due</div>
                <div id="currentAmount" class="text-3xl font-bold mt-2 tracking-tight"></div>
            </div>

            <button class="mt-4 w-full bg-white text-blue-700 py-2.5 rounded-lg font-semibold
                           hover:bg-blue-50 transition duration-200 shadow">
                Process Payment
            </button>
        </div>
    </div>
</div>


{{-- Current Bill Overview Card --}}
<div id="billOverviewCard"
    class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 space-y-6 hidden">

    <h3 class="text-xl font-semibold text-gray-900 dark:text-white tracking-tight">
        Current Bill Overview
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="space-y-1">
            <div class="text-sm text-gray-500 dark:text-gray-400">Billing Period</div>
            <div id="billingPeriod"
                 class="text-lg font-semibold text-gray-900 dark:text-white"></div>
        </div>

        <div class="space-y-1">
            <div class="text-sm text-gray-500 dark:text-gray-400">Bill No.</div>
            <div class="flex items-center gap-2">
                <span id="billNo"
                      class="text-lg font-semibold text-gray-900 dark:text-white"></span>

                <span id="billingStatus"
                      class="px-2 py-1 rounded-full text-xs font-semibold shadow"></span>
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Issued: <span id="issuedDate"></span>
                <span class="mx-1">•</span>
                Due: <span id="dueDate"></span>
            </div>
        </div>

        <div class="text-right space-y-1">
            <div class="text-sm text-gray-500 dark:text-gray-400">Amount</div>
            <div id="billAmount"
                 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight"></div>
        </div>
    </div>
</div>


{{-- Recent Activities Card --}}
<div id="recentActivitiesCard"
    class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 space-y-4">

    <h3 class="text-xl font-semibold text-gray-900 dark:text-white tracking-tight">
        Recent Activities
    </h3>

    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach($recentActivities ?? [] as $activity)
        <div class="flex justify-between items-center py-3">
            <span class="text-gray-800 dark:text-gray-300">{{ $activity['action'] }}</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $activity['date'] }}</span>
        </div>
        @endforeach
    </div>
</div>


{{-- Water Usage Details Card --}}
<div id="waterUsageCard"
    class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 space-y-6 hidden">

    <h3 class="text-xl font-semibold text-gray-900 dark:text-white tracking-tight">
        Water Usage Details
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- Left --}}
        <div class="space-y-4">
            <h4 class="font-medium text-gray-900 dark:text-white text-lg">Consumer Billing</h4>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Consumption</span>
                    <span id="consumption"
                          class="text-gray-900 dark:text-white font-semibold"></span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Meter Reading</span>
                    <span id="meterReading"
                          class="text-gray-900 dark:text-white font-semibold"></span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Date Read</span>
                    <span id="dateRead"
                          class="text-gray-900 dark:text-white font-semibold"></span>
                </div>
            </div>
        </div>

        {{-- Right --}}
        <div class="space-y-4">
            <h4 class="font-medium text-gray-900 dark:text-white text-lg">Summary</h4>

            <div class="space-y-3 text-sm">

                <div>
                    <div class="text-gray-500 dark:text-gray-400">Total Month Bills</div>
                    <div class="flex justify-between items-center">
                        <span id="totalMonthBills"
                              class="text-lg font-semibold text-gray-900 dark:text-white"></span>
                        <span id="totalConsumption"
                              class="text-sm text-gray-500 dark:text-gray-400"></span>
                    </div>
                </div>

                <div>
                    <div class="text-gray-500 dark:text-gray-400">Total Unpaid Amount</div>
                    <div id="totalUnpaidAmount"
                         class="text-xl font-bold text-red-600 dark:text-red-400"></div>
                </div>

                <div>
                    <div class="text-gray-500 dark:text-gray-400">Overall Billing Status</div>
                    <div class="flex justify-between items-center">
                        <span id="currentUsage"
                              class="text-sm text-gray-500 dark:text-gray-400"></span>

                        <span id="overallStatus"
                              class="px-2 py-1 rounded-full text-xs font-semibold shadow"></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{-- Billing History Card --}}
<div id="billingHistoryCard"
    class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 space-y-4">

    <h3 class="text-xl font-semibold text-gray-900 dark:text-white tracking-tight">
        Billing History
    </h3>

    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach($billingHistory ?? [] as $bill)
        <div class="flex justify-between items-center py-3">
            <div>
                <div class="text-gray-900 dark:text-white font-medium">{{ $bill['month'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Due Read: {{ $bill['dueRead'] }}</div>
            </div>

            <div class="text-right space-y-1">
                <div class="font-semibold text-gray-900 dark:text-white">{{ $bill['amount'] }}</div>

                <span class="px-2 py-1 bg-red-200 dark:bg-red-600 text-red-800 dark:text-white
                             rounded-full text-xs font-semibold shadow">
                    {{ $bill['status'] }}
                </span>
            </div>
        </div>
        @endforeach
    </div>

    <button class="w-full mt-4 text-blue-600 dark:text-blue-400 hover:text-blue-800
                   dark:hover:text-blue-300 transition font-medium">
        View Complete History
    </button>
</div>


{{-- Monthly Bill Trend --}}
<div id="monthlyTrendCard"
    class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 space-y-4">

    <h3 class="text-xl font-semibold text-gray-900 dark:text-white tracking-tight">
        Monthly Bill Trend
    </h3>

    <div class="h-64">
        <canvas id="monthlyTrendChart"></canvas>
    </div>

    <button class="w-full mt-4 text-blue-600 dark:text-blue-400 hover:text-blue-800
                   dark:hover:text-blue-300 transition font-medium">
        View Complete History
    </button>
</div>
