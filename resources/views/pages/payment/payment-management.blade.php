<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="paymentManagement()">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Payment Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Process payments and view your transactions</p>
            </div>

            <!-- Tab Navigation -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex gap-4">
                        <button @click="activeTab = 'pending'"
                                :class="activeTab === 'pending'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-clock mr-2"></i>Pending Payments
                            <span x-show="stats.pending_count > 0"
                                  class="ml-2 px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400"
                                  x-text="stats.pending_count"></span>
                        </button>
                        <button @click="activeTab = 'my-transactions'; loadMyTransactions()"
                                :class="activeTab === 'my-transactions'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-user-check mr-2"></i>My Transactions
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Pending Payments Tab -->
            <div x-show="activeTab === 'pending'">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                                <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Payments</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.pending_amount_formatted">₱ 0.00</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><span x-text="stats.pending_count">0</span> items</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Today's Collection</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.today_collection_formatted">₱ 0.00</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><span x-text="stats.today_count">0</span> transactions</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <i class="fas fa-calendar-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Month</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.month_collection_formatted">₱ 0.00</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ now()->format('F Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                <i class="fas fa-receipt text-purple-600 dark:text-purple-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Queue</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="filteredPayments.length">0</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">in current view</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Search -->
                        <div class="flex-1 min-w-[200px]">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" x-model="searchQuery" @input.debounce.300ms="filterPayments()"
                                    placeholder="Search by name, application #, or resolution #..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Type Filter -->
                        <div class="w-48">
                            <select x-model="typeFilter" @change="filterPayments()"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @foreach($paymentTypes as $type)
                                    <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Refresh Button -->
                        <button @click="loadPayments()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                            <i class="fas fa-sync-alt" :class="isLoading && 'fa-spin'"></i>
                        </button>
                    </div>
                </div>

                <!-- Payment Queue Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Loading State -->
                    <div x-show="isLoading" class="p-8 text-center">
                        <i class="fas fa-spinner fa-spin text-3xl text-blue-600 dark:text-blue-400 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400">Loading payments...</p>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!isLoading && filteredPayments.length === 0" class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-inbox text-2xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No pending payments</h3>
                        <p class="text-gray-600 dark:text-gray-400">All payments have been processed or no applications match your search.</p>
                    </div>

                    <!-- Table -->
                    <div x-show="!isLoading && filteredPayments.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="payment in filteredPayments" :key="payment.id + '-' + payment.type">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="payment.customer_name"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono" x-text="payment.reference_number"></p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="payment.type === 'APPLICATION_FEE' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'">
                                                <span x-text="payment.type_label"></span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="payment.address"></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="payment.date_formatted"></p>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="payment.amount_formatted"></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a x-show="payment.print_url" :href="payment.print_url" target="_blank"
                                                    class="p-2 text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                                                    title="Print Order of Payment">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a :href="payment.action_url"
                                                    class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg font-medium transition-colors">
                                                    <i class="fas fa-money-bill-wave mr-1"></i>Process
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- My Transactions Tab -->
            @include('pages.payment.partials.my-transactions-tab')

        </div>

        <!-- Transaction Detail Modal -->
        @include('components.ui.payment.transaction-detail-modal')
    </div>

    <script>
    function paymentManagement() {
        return {
            // Tab state
            activeTab: 'pending',

            // Pending payments state
            payments: [],
            filteredPayments: [],
            stats: @json($stats),
            searchQuery: '',
            typeFilter: '',
            isLoading: true,

            // My transactions state
            myTransactions: {},
            filteredMyTransactions: [],
            myTransactionsSearch: '',
            myTransactionsLoading: false,
            selectedDate: '',
            selectedTransaction: null,
            showDetailModal: false,

            async init() {
                await this.loadPayments();

                // Check URL for tab param
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('tab') === 'my-transactions') {
                    this.activeTab = 'my-transactions';
                    await this.loadMyTransactions();
                }
            },

            async loadPayments() {
                this.isLoading = true;

                try {
                    const params = new URLSearchParams();
                    if (this.typeFilter) params.append('type', this.typeFilter);
                    if (this.searchQuery) params.append('search', this.searchQuery);

                    const response = await fetch(`/api/payments/pending?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.payments = result.data;
                        this.filteredPayments = result.data;
                    }

                    // Also refresh stats
                    await this.loadStats();
                } catch (error) {
                    console.error('Failed to load payments:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            async loadStats() {
                try {
                    const response = await fetch('/api/payments/statistics', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.stats = result.data;
                    }
                } catch (error) {
                    console.error('Failed to load stats:', error);
                }
            },

            filterPayments() {
                this.loadPayments();
            },

            async loadMyTransactions(date = null) {
                this.myTransactionsLoading = true;

                try {
                    const params = new URLSearchParams();
                    if (date) params.append('date', date);

                    const response = await fetch(`/api/payments/my-transactions?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.myTransactions = result.data;
                        this.filteredMyTransactions = result.data.transactions || [];
                        this.selectedDate = result.data.date || '';
                    }
                } catch (error) {
                    console.error('Failed to load my transactions:', error);
                } finally {
                    this.myTransactionsLoading = false;
                }
            },

            filterMyTransactions() {
                if (!this.myTransactionsSearch) {
                    this.filteredMyTransactions = this.myTransactions.transactions || [];
                    return;
                }

                const search = this.myTransactionsSearch.toLowerCase();
                this.filteredMyTransactions = (this.myTransactions.transactions || []).filter(tx =>
                    tx.receipt_no.toLowerCase().includes(search) ||
                    tx.customer_name.toLowerCase().includes(search)
                );
            },

            viewTransaction(tx) {
                this.selectedTransaction = tx;
                this.showDetailModal = true;
            }
        };
    }
    </script>
</x-app-layout>
