<x-app-layout>
    @php
        $appData = $application ?? null;
        $chargesInfo = $chargesData ?? ['charges' => collect(), 'total_amount' => 0, 'paid_amount' => 0, 'remaining_amount' => 0, 'is_fully_paid' => false];
        $customerName = $appData?->customer
            ? trim(($appData->customer->cust_first_name ?? '') . ' ' . ($appData->customer->cust_middle_name ? $appData->customer->cust_middle_name[0] . '. ' : '') . ($appData->customer->cust_last_name ?? ''))
            : '-';
        $fullAddress = $appData?->address
            ? trim(($appData->address->purok?->p_desc ?? '') . ', ' . ($appData->address->barangay->b_desc ?? ''))
            : '-';
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
        x-data="paymentProcessor(@js([
            'id' => $appData?->application_id,
            'application_number' => $appData?->application_number,
            'customer_name' => $customerName,
            'resolution_no' => $appData?->customer?->resolution_no,
            'contact_number' => $appData?->customer?->contact_number,
            'full_address' => $fullAddress,
            'barangay' => $appData?->address?->barangay?->b_desc,
            'charges' => $chargesInfo['charges']->map(fn($c) => [
                'id' => $c->charge_id,
                'description' => $c->description,
                'quantity' => $c->quantity,
                'unit_amount' => $c->unit_amount,
                'total_amount' => $c->total_amount,
            ])->values()->toArray(),
            'total_amount' => $chargesInfo['total_amount'],
            'remaining_amount' => $chargesInfo['remaining_amount'],
            'is_fully_paid' => $chargesInfo['is_fully_paid'],
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
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-credit-card text-white"></i>
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
                <!-- Left Column - Invoice Details (3 cols) -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Invoice Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <!-- Invoice Header -->
                        <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-purple-600 px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-indigo-100 text-xs font-medium uppercase tracking-wider">Invoice</p>
                                    <p class="text-white text-lg font-bold font-mono" x-text="application.application_number"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-indigo-100 text-xs">Issue Date</p>
                                    <p class="text-white font-medium">{{ now()->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Bill To</p>
                                    <p class="text-gray-900 dark:text-white font-semibold" x-text="application.customer_name"></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="application.full_address"></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="application.barangay + ', Initao'"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Customer ID</p>
                                    <p class="text-gray-900 dark:text-white font-mono text-sm" x-text="application.resolution_no || '-'"></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2" x-text="application.contact_number || '-'"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Line Items -->
                        <div class="px-6 py-4">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                        <th class="text-left pb-3">Description</th>
                                        <th class="text-center pb-3">Qty</th>
                                        <th class="text-right pb-3">Rate</th>
                                        <th class="text-right pb-3">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                    <template x-for="item in application.charges" :key="item.id">
                                        <tr class="group">
                                            <td class="py-3">
                                                <p class="text-gray-800 dark:text-gray-200 font-medium" x-text="item.description"></p>
                                            </td>
                                            <td class="py-3 text-center text-gray-600 dark:text-gray-400" x-text="item.quantity"></td>
                                            <td class="py-3 text-right text-gray-600 dark:text-gray-400" x-text="formatCurrency(item.unit_amount)"></td>
                                            <td class="py-3 text-right font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(item.total_amount)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals -->
                        <div class="bg-gray-50 dark:bg-gray-700/30 px-6 py-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Service Application Fee</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Full payment required</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Due</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white" x-text="formatCurrency(application.total_amount)"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Print Section -->
                    <div class="flex gap-3">
                        <a :href="'/connection/service-application/' + application.id + '/order-of-payment'"
                            target="_blank"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm">
                            <i class="fas fa-print text-gray-400"></i>
                            Print Order of Payment
                        </a>
                    </div>
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
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Enter amount received</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Body -->
                            <div class="p-6 space-y-5">
                                <!-- Amount Due Display -->
                                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-xl p-4 border border-indigo-100 dark:border-indigo-800">
                                    <p class="text-xs font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Amount Due</p>
                                    <p class="text-2xl font-bold text-indigo-900 dark:text-indigo-100" x-text="formatCurrency(application.total_amount)"></p>
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
                                            class="w-full pl-14 pr-4 py-3.5 text-xl font-semibold border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <!-- Change Display -->
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4" x-show="change !== null">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Change</span>
                                        <span class="text-xl font-bold"
                                            :class="change >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                                            x-text="formatCurrency(change)"></span>
                                    </div>
                                </div>

                                <!-- Error Message -->
                                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4"
                                    x-show="amountReceived > 0 && amountReceived < application.total_amount">
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                                        <div>
                                            <p class="text-sm font-medium text-red-800 dark:text-red-200">Insufficient Amount</p>
                                            <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">Full payment of <span x-text="formatCurrency(application.total_amount)"></span> is required.</p>
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
                                            :class="paymentMethod === 'CASH' ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-500 text-indigo-700 dark:text-indigo-300' : 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300'"
                                            class="flex items-center justify-center gap-2 px-4 py-3 border-2 rounded-xl font-medium transition-all">
                                            <i class="fas fa-money-bill-wave"></i>
                                            Cash
                                        </button>
                                        <button type="button"
                                            @click="paymentMethod = 'CHECK'"
                                            :class="paymentMethod === 'CHECK' ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-500 text-indigo-700 dark:text-indigo-300' : 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300'"
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
                            <!-- Success Header -->
                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-8 text-center">
                                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-check text-white text-3xl"></i>
                                </div>
                                <h3 class="text-white text-xl font-bold">Payment Successful!</h3>
                                <p class="text-green-100 text-sm mt-1">Transaction completed</p>
                            </div>

                            <!-- Receipt Info -->
                            <div class="p-6 space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Receipt No.</span>
                                        <span class="font-mono font-semibold text-gray-900 dark:text-white" x-text="receipt.receipt_no"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Amount Paid</span>
                                        <span class="font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(receipt.total_paid)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Amount Received</span>
                                        <span class="font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(receipt.amount_received)"></span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Change</span>
                                        <span class="font-bold text-green-600 dark:text-green-400" x-text="formatCurrency(receipt.change)"></span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-y-2">
                                    <a :href="'/payment/receipt/' + receipt.payment.payment_id"
                                        target="_blank"
                                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2">
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
    function paymentProcessor(appData) {
        return {
            application: appData,
            amountReceived: null,
            change: null,
            paymentMethod: 'CASH',
            isProcessing: false,
            paymentSuccess: false,
            receipt: {},

            init() {
                // Pre-fill with exact amount for convenience
                this.amountReceived = this.application.total_amount;
                this.calculateChange();
            },

            get canProcess() {
                return this.amountReceived && this.amountReceived >= this.application.total_amount;
            },

            calculateChange() {
                if (this.amountReceived && this.amountReceived > 0) {
                    this.change = this.amountReceived - this.application.total_amount;
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
                    const response = await fetch(`/connection/service-application/${this.application.id}/process-payment`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
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
