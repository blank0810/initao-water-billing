<x-ui.modal name="balance-modal" title="Customer Balance Inquiry" maxWidth="3xl">
    <div class="p-6 space-y-6">

        <!-- Search Section (hidden when customer selected) -->
        <div x-show="!selectedCustomer" class="space-y-6">
            <div class="relative">
                <input type="text" x-model="query" @input.debounce.300ms="search()"
                    placeholder="Search by name or account number..." aria-label="Search customers"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg 
                              bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 
                              focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                <div x-show="loading" class="absolute right-3 top-3.5">
                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                </div>
            </div>

            <!-- Error Message -->
            <div x-show="error" x-cloak
                class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span x-text="error"></span>
            </div>

            <!-- Loading Skeleton -->
            <template x-if="loading">
                <div class="space-y-3">
                    <div
                        class="animate-pulse flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                        </div>
                        <div class="h-8 w-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                </div>
            </template>

            <!-- Empty State: No Search -->
            <div x-show="!loading && results.length === 0 && query.length === 0" x-cloak
                class="text-center py-12 text-gray-500 dark:text-gray-400">
                <i class="fas fa-search text-4xl mb-3 opacity-30"></i>
                <p class="text-base font-medium">Find a customer</p>
                <p class="text-sm mt-1">Enter name or account number to view balance</p>
            </div>

            <!-- Empty State: No Results -->
            <div x-show="!loading && results.length === 0 && query.length >= 2" x-cloak
                class="text-center py-10 text-gray-500 dark:text-gray-400">
                <i class="fas fa-user-slash text-3xl mb-2 opacity-50"></i>
                <p class="text-sm">No customers found for "<span x-text="query" class="font-medium"></span>"</p>
            </div>

            <!-- Search Results -->
            <div x-show="!loading && results.length > 0" x-cloak class="space-y-2 max-h-96 overflow-y-auto pr-1">
                <template x-for="customer in results" :key="customer.customer_id">
                    <button @click="selectCustomer(customer)"
                        class="w-full p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 text-left transition">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="font-semibold text-gray-900 dark:text-white"
                                        x-text="customer.name"></span>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full"
                                        :class="customer.status === 'ACTIVE' ?
                                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                                            'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'"
                                        x-text="customer.status"></span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="customer.account_no"></p>
                            </div>
                            <div class="text-left sm:text-right">
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-400"
                                    x-text="'₱' + (customer.balance || 0).toLocaleString()"></p>
                                <span
                                    class="text-xs font-medium px-2 py-0.5 bg-gray-100 dark:bg-gray-800 rounded-full">Balance</span>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- Selected Customer Details (visible only when customer selected) -->
        <div x-show="selectedCustomer" x-cloak class="space-y-6">
            <!-- Header with Back Button -->
            <div class="flex justify-between items-start border-b border-gray-200 dark:border-gray-700 pb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="selectedCustomer?.name">
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedCustomer?.account_no"></p>
                </div>
                <button @click="backToSearch()"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white flex items-center gap-1 px-3 py-1.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left text-xs"></i> Back
                </button>
            </div>

            <!-- Loading Skeleton for Details -->
            <div x-show="loadingDetails" x-cloak class="space-y-4">
                <div class="animate-pulse bg-gray-100 dark:bg-gray-800 p-6 rounded-lg">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24 mb-2"></div>
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-36"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-28 mt-3"></div>
                </div>
                <div class="animate-pulse h-24 bg-gray-100 dark:bg-gray-800 rounded-lg"></div>
            </div>

            <!-- Balance Highlight (only shown when details loaded) -->
            <div x-show="!loadingDetails" x-cloak>
                <!-- Main Balance Card -->
                <div
                    class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-lg p-6 text-center mx-auto">
                    <p class="text-sm text-blue-700 dark:text-blue-300 font-medium mb-1">Current Balance</p>
                    <p class="text-4xl font-bold text-blue-900 dark:text-blue-100"
                        x-text="'₱' + (balanceDetails?.current_balance || 0).toLocaleString()"></p>
                    <p class="text-sm text-blue-600 dark:text-blue-300 mt-2" x-show="balanceDetails?.due_date">
                        Due: <span x-text="balanceDetails.due_date"></span>
                    </p>
                </div>


                <!-- Latest Document Info -->
                <div class="mt-6 border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <i class="fas fa-file-invoice text-blue-600 dark:text-blue-400"></i>
                        Latest Invoice / Receipt
                    </h4>

                    <template x-if="balanceDetails?.latest_document">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white"
                                    x-text="balanceDetails.latest_document.or_number"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400"
                                    x-text="balanceDetails.latest_document.date_issued"></p>
                            </div>
                            <div class="flex gap-2">
                                <button @click.once="viewDocument()"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-eye mr-1"></i> View
                                </button>
                                <button @click.once="downloadDocument()"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-gray-400">
                                    <i class="fas fa-download mr-1"></i> Download
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="!balanceDetails?.latest_document">
                        <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-2xl mb-2 opacity-30"></i>
                            <p class="text-sm">No recent documents available</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-ui.modal>
<!-- Alpine Component Registration (place after modal or in a separate script) -->
<script>
    const API_ENDPOINTS = {
        searchCustomers: '/api/search/customers',
        customerBalance: '/api/customer/balance',
        viewReceipt: '/payment/receipt',
        downloadReceipt: '/payment/receipt'
    };

    function balanceInquiryModal() {
        return {
            query: '',
            results: [],
            loading: false,
            error: '',
            selectedCustomer: null,
            loadingDetails: false,
            balanceDetails: null,

            async search() {
                if (this.query.length < 2) {
                    this.results = [];
                    this.error = '';
                    return;
                }

                this.loading = true;
                this.error = '';
                this.results = [];

                try {
                    const response = await fetch(
                        `${API_ENDPOINTS.searchCustomers}?q=${encodeURIComponent(this.query)}`);
                    if (!response.ok) throw new Error('Search failed');
                    this.results = await response.json();
                } catch (error) {
                    console.error('Search failed:', error);
                    this.error = 'Unable to search. Please try again.';
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            },

            async selectCustomer(customer) {
                this.selectedCustomer = customer;
                this.loadingDetails = true;
                this.balanceDetails = {
                    current_balance: customer.balance || 0,
                    due_date: null,
                    latest_document: null
                };

                try {
                    // Simulate API call – replace with real fetch
                    await new Promise(resolve => setTimeout(resolve, 500));
                    this.balanceDetails = {
                        current_balance: customer.balance || 1250.50,
                        due_date: 'March 15, 2025',
                        latest_document: {
                            or_number: 'OR-2025-00123',
                            date_issued: 'January 15, 2025',
                            document_id: 123
                        }
                    };
                } catch (error) {
                    console.error('Failed to load balance:', error);
                    this.error = 'Unable to load customer details.';
                } finally {
                    this.loadingDetails = false;
                }
            },

            backToSearch() {
                this.selectedCustomer = null;
                this.balanceDetails = null;
                this.error = '';
            },

            viewDocument() {
                if (this.balanceDetails?.latest_document) {
                    window.open(`${API_ENDPOINTS.viewReceipt}/${this.balanceDetails.latest_document.document_id}`,
                        '_blank');
                }
            },

            downloadDocument() {
                if (this.balanceDetails?.latest_document) {
                    window.location.href =
                        `${API_ENDPOINTS.downloadReceipt}/${this.balanceDetails.latest_document.document_id}/download`;
                }
            }
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('balanceInquiryModal', balanceInquiryModal);
    });
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
