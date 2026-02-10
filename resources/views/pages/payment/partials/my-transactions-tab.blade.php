<div x-show="activeTab === 'my-transactions'" x-cloak>
    <!-- Date Selector & Summary Cards -->
    <div class="mb-6">
        <!-- Date Navigation -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-calendar-day text-gray-500 dark:text-gray-400"></i>
                <span class="text-lg font-semibold text-gray-900 dark:text-white" x-text="myTransactions.date_display || 'Today'"></span>
            </div>
            <div class="flex items-center gap-2">
                <button @click="loadMyTransactions()"
                        class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors">
                    Today
                </button>
                <input type="date"
                       x-model="selectedDate"
                       @change="loadMyTransactions(selectedDate)"
                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">

                <!-- Export Dropdown -->
                <div class="relative" x-data="{ exportOpen: false }">
                    <button @click="exportOpen = !exportOpen"
                            @click.away="exportOpen = false"
                            class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-download"></i>
                        Export
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div x-show="exportOpen"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                        <a :href="`/api/payments/my-transactions/export/pdf?date=${selectedDate || ''}`"
                           target="_blank"
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-t-lg transition-colors">
                            <i class="fas fa-file-pdf text-red-500"></i>
                            Export as PDF
                        </a>
                        <a :href="`/api/payments/my-transactions/export/csv?date=${selectedDate || ''}`"
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-b-lg transition-colors">
                            <i class="fas fa-file-csv text-green-500"></i>
                            Export as CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total Collected -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <i class="fas fa-coins text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Collected</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="myTransactions.summary?.total_collected_formatted || '₱ 0.00'"></p>
                        <template x-if="myTransactions.summary?.cancelled_count > 0">
                            <p class="text-xs text-red-500 mt-1">
                                <i class="fas fa-ban mr-1"></i>
                                <span x-text="myTransactions.summary?.cancelled_count"></span> cancelled
                                (<span x-text="myTransactions.summary?.cancelled_amount_formatted"></span>)
                            </p>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Transaction Count -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <i class="fas fa-receipt text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Transactions</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="myTransactions.summary?.transaction_count || 0"></p>
                    </div>
                </div>
            </div>

            <!-- Breakdown by Type -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-start">
                    <div class="flex-shrink-0 p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <i class="fas fa-chart-pie text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Breakdown by Type</p>
                        <div class="space-y-1">
                            <template x-for="item in (myTransactions.summary?.by_type || [])" :key="item.type">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400" x-text="item.type"></span>
                                    <span class="font-medium text-gray-900 dark:text-white" x-text="item.amount_formatted"></span>
                                </div>
                            </template>
                            <template x-if="!myTransactions.summary?.by_type?.length">
                                <p class="text-sm text-gray-400 dark:text-gray-500">No transactions</p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text"
                   x-model="myTransactionsSearch"
                   @input.debounce.300ms="filterMyTransactions()"
                   placeholder="Search receipt or customer..."
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>

    <!-- Transaction List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Loading State -->
        <div x-show="myTransactionsLoading" class="p-8 text-center">
            <i class="fas fa-spinner fa-spin text-3xl text-blue-600 dark:text-blue-400 mb-4"></i>
            <p class="text-gray-600 dark:text-gray-400">Loading transactions...</p>
        </div>

        <!-- Empty State -->
        <div x-show="!myTransactionsLoading && filteredMyTransactions.length === 0" class="p-8 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-inbox text-2xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No transactions found</h3>
            <p class="text-gray-600 dark:text-gray-400">You haven't processed any payments for this date.</p>
        </div>

        <!-- Table -->
        <div x-show="!myTransactionsLoading && filteredMyTransactions.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Receipt #</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="tx in filteredMyTransactions" :key="tx.payment_id">
                        <tr class="transition-colors"
                            :class="tx.is_cancelled ? 'bg-red-50/50 dark:bg-red-900/10 opacity-75' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono font-medium"
                                      :class="tx.is_cancelled ? 'text-gray-400 dark:text-gray-500 line-through' : 'text-gray-900 dark:text-white'"
                                      x-text="tx.receipt_no"></span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium"
                                   :class="tx.is_cancelled ? 'text-gray-400 dark:text-gray-500 line-through' : 'text-gray-900 dark:text-white'"
                                   x-text="tx.customer_name"></p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="tx.is_cancelled
                                              ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                                              : (tx.payment_type === 'APPLICATION_FEE'
                                                  ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
                                                  : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400')"
                                          x-text="tx.is_cancelled ? 'CANCELLED' : tx.payment_type_label">
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold"
                                      :class="tx.is_cancelled ? 'text-red-400 dark:text-red-500 line-through' : 'text-gray-900 dark:text-white'"
                                      x-text="tx.amount_formatted"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700 dark:text-gray-300" x-text="tx.time"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <template x-if="!tx.is_cancelled">
                                        <div class="flex items-center gap-2">
                                            <button @click="viewTransaction(tx)"
                                                    class="p-2 text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a :href="tx.receipt_url"
                                               target="_blank"
                                               class="p-2 text-gray-600 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition-colors"
                                               title="Print Receipt">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @can('payments.void')
                                            <button @click="openCancelPaymentModal(tx)"
                                                    class="p-2 text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors"
                                                    title="Cancel Payment">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </template>
                                    <template x-if="tx.is_cancelled">
                                        <div class="text-xs text-red-500 dark:text-red-400 text-center">
                                            <p x-text="'By: ' + tx.cancelled_by_name"></p>
                                            <p x-text="tx.cancelled_at" class="text-gray-400"></p>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div x-show="!myTransactionsLoading && filteredMyTransactions.length > 0"
             class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span class="font-medium" x-text="filteredMyTransactions.length"></span> transaction(s)
            </p>
        </div>
    </div>
</div>
