<x-app-layout>
    <div class="min-h-[80vh] bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <x-ui.page-header
                title="Ledger Management"
                subtitle="Track financial transactions, payments, and account balances"
            />

            <!-- Under Development Card -->
            <div class="relative overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">

                <!-- Subtle diagonal stripes background -->
                <div class="absolute inset-0 opacity-[0.015] dark:opacity-[0.03]"
                     style="background-image: repeating-linear-gradient(
                         -45deg,
                         transparent,
                         transparent 10px,
                         currentColor 10px,
                         currentColor 11px
                     );">
                </div>

                <div class="relative flex flex-col items-center justify-center py-20 px-6 text-center"
                     x-data="{ show: false }"
                     x-init="setTimeout(() => show = true, 100)">

                    <!-- Animated Icon -->
                    <div class="mb-8"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="relative">
                            <!-- Outer ring pulse -->
                            <div class="absolute inset-0 w-28 h-28 rounded-full bg-amber-400/10 dark:bg-amber-400/5 animate-ping" style="animation-duration: 3s;"></div>
                            <!-- Icon container -->
                            <div class="relative w-28 h-28 rounded-full bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-2 border-amber-200 dark:border-amber-700/50 flex items-center justify-center">
                                <svg class="w-12 h-12 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.049.58.025 1.193-.14 1.743" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Text Content -->
                    <div x-show="show"
                         x-transition:enter="transition ease-out duration-500 delay-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <!-- Status Badge -->
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 mb-5">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                            </span>
                            <span class="text-xs font-semibold tracking-wide uppercase text-amber-700 dark:text-amber-300">In Development</span>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                            This module is currently being built
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed mb-8">
                            The Ledger Management system is under active development. This will include consumer ledger tracking, transaction history, and account balance management.
                        </p>
                    </div>

                    <!-- Feature Preview Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl w-full"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-500 delay-[400ms]"
                         x-transition:enter-start="opacity-0 translate-y-3"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="flex flex-col items-center p-5 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-3">
                                <i class="fas fa-book-open text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Consumer Ledgers</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Account histories</span>
                        </div>

                        <div class="flex flex-col items-center p-5 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700">
                            <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-3">
                                <i class="fas fa-exchange-alt text-green-600 dark:text-green-400"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Transactions</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Bills, payments, charges</span>
                        </div>

                        <div class="flex flex-col items-center p-5 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-3">
                                <i class="fas fa-chart-line text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Balance Reports</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">Outstanding balances</span>
                        </div>
                    </div>

                    <!-- Back to Dashboard -->
                    <div class="mt-10"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-500 delay-[600ms]"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                            <i class="fas fa-arrow-left text-xs"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
