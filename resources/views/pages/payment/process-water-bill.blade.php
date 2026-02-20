<x-app-layout>
    @php
        $billData = $bill ?? null;
        $customer = $billData?->serviceConnection?->customer;
        $connectionData = $connection ?? $billData?->serviceConnection;
        $customerName = $customer
            ? trim(($customer->cust_first_name ?? '') . ' ' . ($customer->cust_middle_name ? $customer->cust_middle_name[0] . '. ' : '') . ($customer->cust_last_name ?? ''))
            : '-';

        $addressParts = array_filter([
            $connectionData?->address?->purok?->purok_name ?? '',
            $connectionData?->address?->barangay?->b_name ?? '',
        ]);
        $fullAddress = count($addressParts) > 0 ? implode(', ', $addressParts) : '';
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
        x-data="waterBillPayment(@js([
            'connection_id' => $connectionData?->connection_id,
            'customer_name' => $customerName,
            'account_no' => $connectionData?->account_no,
            'resolution_no' => $customer?->resolution_no,
            'full_address' => $fullAddress,
            'barangay' => $connectionData?->address?->barangay?->b_name,
            'account_type' => $connectionData?->accountType?->at_desc ?? 'N/A',
            'selected_bill_id' => $selectedBillId ?? null,
            'bills' => $outstandingItems['bills'] ?? [],
            'charges' => $outstandingItems['charges'] ?? [],
        ]))">

        <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <a href="{{ route('payment.management') }}"
                        class="p-2.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-white dark:hover:bg-gray-800 rounded-xl transition-all shadow-sm border border-gray-200 dark:border-gray-700">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-tint text-white"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Process Payment</h1>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Cashier Payment Portal</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left Column - Outstanding Items (3 cols) -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Connection Info Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-cyan-100 text-xs font-medium uppercase tracking-wider">Connection Payment</p>
                                    <p class="text-white text-lg font-bold" x-text="data.account_no"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-cyan-100 text-xs">Account Holder</p>
                                    <p class="text-white font-medium" x-text="data.customer_name"></p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Address</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300" x-text="data.full_address || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Account Type</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300" x-text="data.account_type"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Resolution No.</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 font-mono" x-text="data.resolution_no || '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Items Header -->
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Outstanding Items</h2>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400" x-text="totalItems + ' item(s)'"></span>
                            <span x-show="totalDue > 0" class="px-2.5 py-1 text-xs font-medium rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400">Partial payment accepted</span>
                        </div>
                    </div>

                    <!-- Water Bills with Nested Charges -->
                    <template x-if="billsWithCharges.length > 0">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-3 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-800">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-tint text-blue-600 dark:text-blue-400"></i>
                                    <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-300 uppercase tracking-wider">Water Bills</h3>
                                    <span class="ml-auto text-xs text-blue-600 dark:text-blue-400" x-text="billsWithCharges.length + ' bill(s)'"></span>
                                </div>
                            </div>
                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="billGroup in billsWithCharges" :key="'bill-' + billGroup.id">
                                    <div class="px-6 py-4">
                                        <!-- Bill Row -->
                                        <div class="flex items-center gap-4">
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-file-invoice text-blue-600 dark:text-blue-400 text-xs"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="billGroup.description"></p>
                                                    <span x-show="billGroup.is_overdue" class="px-1.5 py-0.5 text-[10px] font-bold uppercase rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Overdue</span>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'Due: ' + (billGroup.due_date || 'N/A')"></p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="formatCurrency(billGroup.amount)"></p>
                                                <p x-show="billGroup.is_partially_paid" class="text-[10px] text-blue-600 dark:text-blue-400">
                                                    Paid: <span x-text="formatCurrency(billGroup.paid_amount)"></span> of <span x-text="formatCurrency(billGroup.original_amount)"></span>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Nested Charges for this Bill -->
                                        <template x-if="billGroup.charges.length > 0">
                                            <div class="ml-12 mt-3 space-y-2">
                                                <template x-for="charge in billGroup.charges" :key="'charge-' + charge.id">
                                                    <div class="flex items-center gap-3 py-2 px-3 bg-orange-50 dark:bg-orange-900/10 rounded-lg border border-orange-100 dark:border-orange-800">
                                                        <div class="w-6 h-6 rounded bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center flex-shrink-0">
                                                            <i class="fas fa-receipt text-orange-600 dark:text-orange-400 text-[10px]"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300" x-text="charge.description"></p>
                                                            <p class="text-[10px] text-gray-500 dark:text-gray-400" x-text="'Due: ' + (charge.due_date || 'N/A')"></p>
                                                        </div>
                                                        <p class="text-xs font-semibold text-orange-700 dark:text-orange-400" x-text="formatCurrency(charge.amount)"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Unassociated Charges (null period_id — legacy or misc) -->
                    <template x-if="unassociatedCharges.length > 0">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-3 bg-orange-50 dark:bg-orange-900/20 border-b border-orange-100 dark:border-orange-800">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file-invoice text-orange-600 dark:text-orange-400"></i>
                                    <h3 class="text-sm font-semibold text-orange-800 dark:text-orange-300 uppercase tracking-wider">Other Charges</h3>
                                    <span class="ml-auto text-xs text-orange-600 dark:text-orange-400" x-text="unassociatedCharges.length + ' charge(s)'"></span>
                                </div>
                            </div>
                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="charge in unassociatedCharges" :key="'unassoc-charge-' + charge.id">
                                    <div class="flex items-center gap-4 px-6 py-4">
                                        <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-receipt text-orange-600 dark:text-orange-400 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="charge.description"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'Due: ' + (charge.due_date || 'N/A')"></p>
                                        </div>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="formatCurrency(charge.amount)"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="data.bills.length === 0 && data.charges.length === 0">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8 text-center">
                            <div class="w-16 h-16 mx-auto bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-check text-2xl text-green-600 dark:text-green-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">All Settled</h3>
                            <p class="text-gray-600 dark:text-gray-400">No outstanding bills or charges for this connection.</p>
                        </div>
                    </template>
                </div>

                <!-- Right Column - Payment Form (2 cols) -->
                <div class="lg:col-span-2">
                    <div class="sticky top-6">
                        <!-- Payment Form Card -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden"
                            x-show="!paymentSuccess">
                            <!-- Header -->
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-peso-sign text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Collect Payment</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Full or partial payment accepted</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Body -->
                            <div class="p-6 space-y-5">
                                <!-- Total Due Summary -->
                                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 rounded-xl p-4 border border-cyan-100 dark:border-cyan-800">
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-xs font-medium text-cyan-600 dark:text-cyan-400 uppercase tracking-wider">Total Amount Due</p>
                                        <p class="text-xs text-cyan-600 dark:text-cyan-400" x-text="totalItems + ' item(s)'"></p>
                                    </div>
                                    <p class="text-2xl font-bold text-cyan-900 dark:text-cyan-100" x-text="formatCurrency(totalDue)"></p>

                                    <!-- Breakdown -->
                                    <div class="mt-3 pt-3 border-t border-cyan-200 dark:border-cyan-700 space-y-1" x-show="data.bills.length > 0 || data.charges.length > 0">
                                        <div class="flex justify-between text-xs" x-show="data.bills.length > 0">
                                            <span class="text-cyan-700 dark:text-cyan-300" x-text="data.bills.length + ' bill(s)'"></span>
                                            <span class="text-cyan-800 dark:text-cyan-200 font-medium" x-text="formatCurrency(billsTotal)"></span>
                                        </div>
                                        <div class="flex justify-between text-xs" x-show="data.charges.length > 0">
                                            <span class="text-cyan-700 dark:text-cyan-300" x-text="data.charges.length + ' charge(s)'"></span>
                                            <span class="text-cyan-800 dark:text-cyan-200 font-medium" x-text="formatCurrency(chargesTotal)"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Amount Received Input -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Amount Received
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">PHP</span>
                                        <input type="number"
                                            x-model.number="amountReceived"
                                            @input="calculateChange()"
                                            step="0.01"
                                            min="0"
                                            class="w-full pl-14 pr-4 py-3.5 text-xl font-semibold border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all"
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <!-- Change / Remaining Balance Display -->
                                <div class="rounded-xl p-4" x-show="amountReceived > 0 && totalItems > 0"
                                    :class="amountReceived >= totalDue ? 'bg-green-50 dark:bg-green-900/20' : 'bg-amber-50 dark:bg-amber-900/20'">
                                    <!-- Full payment or overpayment: show change -->
                                    <div x-show="amountReceived >= totalDue">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-green-700 dark:text-green-400">Change</span>
                                            <span class="text-xl font-bold text-green-600 dark:text-green-400"
                                                x-text="formatCurrency(amountReceived - totalDue)"></span>
                                        </div>
                                    </div>
                                    <!-- Partial payment: show remaining balance -->
                                    <div x-show="amountReceived < totalDue">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-sm font-medium text-amber-800 dark:text-amber-200">Partial Payment</span>
                                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Remaining balance</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-amber-700 dark:text-amber-300">Balance after payment</span>
                                            <span class="text-xl font-bold text-amber-600 dark:text-amber-400"
                                                x-text="formatCurrency(totalDue - amountReceived)"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Method -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Payment Method
                                    </label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button"
                                            @click="paymentMethod = 'CASH'"
                                            :class="paymentMethod === 'CASH' ? 'bg-cyan-50 dark:bg-cyan-900/30 border-cyan-500 text-cyan-700 dark:text-cyan-300' : 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300'"
                                            class="flex items-center justify-center gap-2 px-4 py-3 border-2 rounded-xl font-medium transition-all">
                                            <i class="fas fa-money-bill-wave"></i>
                                            Cash
                                        </button>
                                        <button type="button"
                                            @click="paymentMethod = 'CHECK'"
                                            :class="paymentMethod === 'CHECK' ? 'bg-cyan-50 dark:bg-cyan-900/30 border-cyan-500 text-cyan-700 dark:text-cyan-300' : 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300'"
                                            class="flex items-center justify-center gap-2 px-4 py-3 border-2 rounded-xl font-medium transition-all">
                                            <i class="fas fa-money-check"></i>
                                            Check
                                        </button>
                                    </div>
                                </div>

                                <!-- Process Button -->
                                <button @click="processPayment()"
                                    :disabled="!canProcess || isProcessing"
                                    :class="canProcess && !isProcessing ? 'bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 shadow-lg shadow-green-500/25' : 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed'"
                                    class="w-full py-4 text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2">
                                    <template x-if="!isProcessing">
                                        <span><i class="fas fa-check-circle mr-2"></i>Confirm Payment</span>
                                    </template>
                                    <template x-if="isProcessing">
                                        <span><i class="fas fa-spinner fa-spin mr-2"></i>Processing...</span>
                                    </template>
                                </button>

                                <!-- Security Note -->
                                <p class="text-xs text-center text-gray-400 dark:text-gray-500">
                                    <i class="fas fa-lock mr-1"></i>
                                    Secure transaction - Receipt will be generated automatically
                                </p>
                            </div>
                        </div>

                        <!-- Success Card -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden"
                            x-show="paymentSuccess"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-8 text-center">
                                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-check text-white text-3xl"></i>
                                </div>
                                <h3 class="text-white text-xl font-bold">Payment Successful!</h3>
                                <p class="text-green-100 text-sm mt-1">Transaction completed</p>
                            </div>

                            <div class="p-6 space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Receipt No.</span>
                                        <span class="font-mono font-semibold text-gray-900 dark:text-white" x-text="receipt.receipt_no"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Applied</span>
                                        <span class="font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(receipt.total_paid)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Amount Received</span>
                                        <span class="font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(receipt.amount_received)"></span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600" x-show="receipt.change > 0">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Change</span>
                                        <span class="font-bold text-green-600 dark:text-green-400" x-text="formatCurrency(receipt.change)"></span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600" x-show="receipt.remaining_balance > 0">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Remaining Balance</span>
                                        <span class="font-bold text-amber-600 dark:text-amber-400" x-text="formatCurrency(receipt.remaining_balance)"></span>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <a :href="'/payment/receipt/' + receipt.payment.payment_id"
                                        target="_blank"
                                        class="w-full py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2">
                                        <i class="fas fa-print"></i>
                                        View & Print Receipt
                                    </a>
                                    <a href="{{ route('payment.management') }}"
                                        class="block w-full py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-colors text-center">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Back to Payment Queue
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function waterBillPayment(initialData) {
        return {
            data: initialData,
            amountReceived: null,
            change: null,
            paymentMethod: 'CASH',
            isProcessing: false,
            paymentSuccess: false,
            receipt: {},

            init() {
                // Pre-fill amount with total due
                this.$nextTick(() => {
                    this.amountReceived = this.totalDue;
                });
            },

            get billsWithCharges() {
                return this.data.bills.map(bill => ({
                    ...bill,
                    charges: this.data.charges.filter(c => c.period_id && c.period_id === bill.period_id),
                }));
            },

            get unassociatedCharges() {
                const billPeriodIds = this.data.bills.map(b => b.period_id);
                return this.data.charges.filter(c =>
                    !c.period_id || !billPeriodIds.includes(c.period_id)
                );
            },

            get billsTotal() {
                return this.data.bills.reduce((sum, b) => sum + b.amount, 0);
            },

            get chargesTotal() {
                return this.data.charges.reduce((sum, c) => sum + c.amount, 0);
            },

            get totalDue() {
                return this.billsTotal + this.chargesTotal;
            },

            get totalItems() {
                return this.data.bills.length + this.data.charges.length;
            },

            get canProcess() {
                return this.data.selected_bill_id
                    && this.totalItems > 0
                    && this.amountReceived
                    && this.amountReceived > 0;
            },

            calculateChange() {
                if (this.amountReceived && this.amountReceived > 0 && this.totalItems > 0) {
                    this.change = this.amountReceived - this.totalDue;
                } else {
                    this.change = null;
                }
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('en-PH', {
                    style: 'currency',
                    currency: 'PHP'
                }).format(amount || 0);
            },

            async processPayment() {
                if (!this.canProcess || this.isProcessing) return;

                this.isProcessing = true;

                try {
                    const response = await fetch(`/payment/bill/${this.data.selected_bill_id}/process`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            connection_id: this.data.connection_id,
                            amount_received: this.amountReceived,
                            payment_method: this.paymentMethod
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.receipt = result.data;
                        this.paymentSuccess = true;
                    } else {
                        alert(result.message || 'Payment processing failed');
                    }
                } catch (error) {
                    console.error('Payment failed:', error);
                    alert('Payment processing failed. Please try again.');
                } finally {
                    this.isProcessing = false;
                }
            }
        };
    }
    </script>
</x-app-layout>
