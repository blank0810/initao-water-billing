@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css" />
<style>
    /* DataTables dark mode overrides */
    .dark .dataTables_wrapper .dataTables_length select,
    .dark .dataTables_wrapper .dataTables_filter input {
        background-color: rgb(55 65 81);
        border-color: rgb(75 85 99);
        color: rgb(229 231 235);
    }
    .dark table.dataTable tbody tr {
        background-color: rgb(31 41 55);
        color: rgb(229 231 235);
    }
    .dark table.dataTable tbody tr:hover {
        background-color: rgb(55 65 81) !important;
    }
    .dark table.dataTable thead th {
        background-color: rgb(31 41 55);
        color: rgb(229 231 235);
        border-bottom-color: rgb(75 85 99);
    }
    .dark .dataTables_wrapper .dataTables_info,
    .dark .dataTables_wrapper .dataTables_length label,
    .dark .dataTables_wrapper .dataTables_filter label {
        color: rgb(156 163 175);
    }
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: rgb(156 163 175) !important;
    }
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: rgb(37 99 235) !important;
        color: white !important;
        border-color: rgb(37 99 235) !important;
    }
    /* Hide default DataTables search — we use custom filters */
    .dataTables_filter {
        display: none !important;
    }
</style>
@endpush

<div x-data="collectionsTab()" x-init="init()">
    <!-- Custom Filters -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" x-model="searchQuery" @input.debounce.400ms="applyFilters()"
                        placeholder="Search by receipt, consumer, or amount..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="sm:w-48">
                <select x-model="statusFilter" @change="applyFilters()"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="sm:w-44">
                <input type="date" x-model="dateFrom" @change="applyFilters()"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    title="Date from" />
            </div>
            <div class="sm:w-44">
                <input type="date" x-model="dateTo" @change="applyFilters()"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                    title="Date to" />
            </div>
        </div>
    </div>

    <!-- Collection Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Today's Collection</p>
                    <p class="text-3xl font-bold mt-2" x-text="stats.today_collection_formatted || '₱ 0.00'"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-calendar-day text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">This Month</p>
                    <p class="text-3xl font-bold mt-2" x-text="stats.month_collection_formatted || '₱ 0.00'"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Transactions</p>
                    <p class="text-3xl font-bold mt-2" x-text="stats.total_transactions || '0'"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-receipt text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Avg Payment</p>
                    <p class="text-3xl font-bold mt-2" x-text="stats.average_payment_formatted || '₱ 0.00'"></p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable -->
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
        <div class="overflow-x-auto p-4">
            <table id="collectionsTable" class="min-w-full stripe hover" style="width:100%">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Receipt No.</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Cashier</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700"></tbody>
            </table>
        </div>
    </div>

    <!-- Cancel Payment Modal -->
    <div x-show="showCancelModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="fixed inset-0 bg-black/50" @click="showCancelModal = false"></div>

        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md z-10"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform scale-95 opacity-0" x-transition:enter-end="transform scale-100 opacity-100">

            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-ban text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cancel Payment</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">This action cannot be undone</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Receipt #</span>
                        <span class="font-mono font-medium text-gray-900 dark:text-white" x-text="cancelData?.receipt_no"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Consumer</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="cancelData?.consumer_name"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Amount</span>
                        <span class="font-bold text-red-600 dark:text-red-400" x-text="cancelData?.amount_formatted"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Reason for Cancellation <span class="text-red-500">*</span>
                    </label>
                    <textarea x-model="cancelReason" rows="3" maxlength="500"
                        placeholder="Explain why this payment is being cancelled..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-red-500 focus:border-red-500"
                        :class="cancelError ? 'border-red-500' : ''"></textarea>
                    <div class="flex justify-between mt-1">
                        <p x-show="cancelError" class="text-xs text-red-500" x-text="cancelError"></p>
                        <p class="text-xs text-gray-400 ml-auto" x-text="(cancelReason?.length || 0) + '/500'"></p>
                    </div>
                </div>

                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-3">
                    <div class="flex gap-2">
                        <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                        <div class="text-xs text-amber-700 dark:text-amber-300">
                            <p class="font-medium mb-1">This will:</p>
                            <ul class="list-disc ml-4 space-y-0.5">
                                <li>Cancel this payment and all its allocations</li>
                                <li>Create reversal entries in the customer ledger</li>
                                <li>Make the associated bills/charges available for payment again</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                <button @click="showCancelModal = false; cancelReason = ''; cancelError = '';"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Keep Payment
                </button>
                <button @click="confirmCancel()" :disabled="cancelLoading"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors flex items-center gap-2">
                    <i x-show="cancelLoading" class="fas fa-spinner fa-spin"></i>
                    <span x-text="cancelLoading ? 'Cancelling...' : 'Cancel Payment'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery (required by DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables core -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
function collectionsTab() {
    return {
        searchQuery: '',
        statusFilter: 'all',
        dateFrom: '',
        dateTo: '',
        stats: {},
        dataTable: null,
        initialized: false,

        // Cancel modal state
        showCancelModal: false,
        cancelData: null,
        cancelReason: '',
        cancelError: '',
        cancelLoading: false,

        init() {
            // DataTable will be initialized when the tab is shown
            window.renderCollections = () => {
                if (!this.initialized) {
                    this.initDataTable();
                    this.initialized = true;
                } else if (this.dataTable) {
                    this.dataTable.ajax.reload(null, false);
                }
                this.loadStats();
            };
        },

        initDataTable() {
            const self = this;

            this.dataTable = $('#collectionsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("api.billing.collections") }}',
                    type: 'GET',
                    data: function(d) {
                        d.status = self.statusFilter;
                        d.date_from = self.dateFrom;
                        d.date_to = self.dateTo;
                    },
                    error: function(xhr) {
                        console.error('Collections DataTable error:', xhr);
                        if (window.showToast) {
                            showToast('Error', 'Failed to load collections data.', 'error');
                        }
                    }
                },
                columns: [
                    {
                        data: 'receipt_no',
                        render: function(data) {
                            return '<span class="font-mono text-sm text-gray-900 dark:text-gray-100">' + data + '</span>';
                        }
                    },
                    {
                        data: 'payment_date',
                        render: function(data) {
                            return '<span class="text-sm text-gray-900 dark:text-gray-100">' + (data || '-') + '</span>';
                        }
                    },
                    {
                        data: 'consumer_name',
                        render: function(data) {
                            return '<span class="text-sm text-gray-900 dark:text-gray-100">' + data + '</span>';
                        }
                    },
                    {
                        data: 'amount_formatted',
                        className: 'text-right',
                        render: function(data) {
                            return '<span class="text-sm font-bold text-green-600 dark:text-green-400">' + data + '</span>';
                        }
                    },
                    {
                        data: 'cashier',
                        render: function(data) {
                            return '<span class="text-sm text-gray-900 dark:text-gray-100">' + data + '</span>';
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (row.is_cancelled) {
                                return '<span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200">' +
                                    '<i class="fas fa-ban mr-1"></i>Cancelled</span>';
                            }
                            return '<span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">' +
                                '<i class="fas fa-check-circle mr-1"></i>Active</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            let actions = '<div class="flex items-center justify-center gap-1">';

                            // View button
                            actions += '<a href="' + row.receipt_url + '" class="p-2 text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors" title="View Receipt">' +
                                '<i class="fas fa-eye"></i></a>';

                            if (!row.is_cancelled) {
                                // Print button
                                actions += '<a href="' + row.receipt_url + '" target="_blank" class="p-2 text-gray-600 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition-colors" title="Print Receipt">' +
                                    '<i class="fas fa-print"></i></a>';

                                // Cancel button (permission checked server-side via Blade)
                                @can('payments.void')
                                actions += '<button onclick="window.collectionsOpenCancel(' + row.payment_id + ')" class="p-2 text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors" title="Cancel Payment">' +
                                    '<i class="fas fa-ban"></i></button>';
                                @endcan
                            } else {
                                // Show cancellation info on hover
                                actions += '<span class="text-xs text-red-400 dark:text-red-500" title="' +
                                    (row.cancellation_reason || 'No reason') + '">' +
                                    '<i class="fas fa-info-circle"></i></span>';
                            }

                            actions += '</div>';
                            return actions;
                        }
                    }
                ],
                order: [[1, 'desc']],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                language: {
                    emptyTable: '<div class="py-4 text-center text-gray-500 dark:text-gray-400"><i class="fas fa-inbox text-3xl mb-2 opacity-50"></i><p>No collections found</p></div>',
                    processing: '<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><i class="fas fa-spinner fa-spin"></i> Loading...</div>',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    lengthMenu: 'Show _MENU_ entries',
                },
                dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
                drawCallback: function() {
                    // Re-apply dark mode classes after each draw
                    if (document.documentElement.classList.contains('dark')) {
                        $('#collectionsTable tbody tr').addClass('dark-row');
                    }
                }
            });

            // Expose cancel function globally for DataTables render callback
            window.collectionsOpenCancel = (paymentId) => {
                const tableData = this.dataTable.rows().data().toArray();
                const row = tableData.find(r => r.payment_id === paymentId);
                if (row) {
                    this.cancelData = row;
                    this.cancelReason = '';
                    this.cancelError = '';
                    this.showCancelModal = true;
                }
            };
        },

        applyFilters() {
            if (this.dataTable) {
                this.dataTable.search(this.searchQuery).draw();
            }
        },

        async loadStats() {
            try {
                const response = await fetch('{{ route("api.payments.statistics") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await response.json();
                if (result.success) {
                    this.stats = result.data;
                }
            } catch (error) {
                console.error('Failed to load stats:', error);
            }
        },

        async confirmCancel() {
            if (!this.cancelReason.trim()) {
                this.cancelError = 'Please provide a reason for cancellation.';
                return;
            }

            this.cancelLoading = true;
            this.cancelError = '';

            try {
                const response = await fetch(`/payment/${this.cancelData.payment_id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ reason: this.cancelReason.trim() }),
                });
                const result = await response.json();

                if (result.success) {
                    this.showCancelModal = false;
                    this.cancelReason = '';
                    this.dataTable.ajax.reload(null, false);
                    this.loadStats();
                    if (window.showToast) showToast('Success', 'Payment cancelled successfully.', 'success');
                } else {
                    this.cancelError = result.message || 'Failed to cancel payment.';
                }
            } catch (error) {
                this.cancelError = 'Network error. Please try again.';
            } finally {
                this.cancelLoading = false;
            }
        }
    };
}
</script>
@endpush
