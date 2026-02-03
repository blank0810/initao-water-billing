<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

            {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ route('payment.management') }}"
                    class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Payment Management
                </a>
            </div>

            {{-- Header --}}
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white mb-4 shadow-lg">
                    <i class="fas fa-tint text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Process Water Bill Payment</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Complete the payment transaction below</p>
            </div>

            {{-- Error Message --}}
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="space-y-6">
                {{-- Customer Information Card --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-user-circle mr-3"></i>
                            Customer Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer Name</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $bill->serviceConnection->customer->cust_first_name }}
                                    {{ $bill->serviceConnection->customer->cust_middle_name ? substr($bill->serviceConnection->customer->cust_middle_name, 0, 1) . '.' : '' }}
                                    {{ $bill->serviceConnection->customer->cust_last_name }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account Number</label>
                                <p class="mt-1 text-lg font-mono font-semibold text-gray-900 dark:text-white">
                                    {{ $bill->serviceConnection->account_no }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</label>
                                <p class="mt-1 text-gray-700 dark:text-gray-300">
                                    {{ $bill->serviceConnection->address->purok->purok_name ?? '' }}{{ $bill->serviceConnection->address->purok ? ',' : '' }}
                                    {{ $bill->serviceConnection->address->barangay->b_name ?? 'N/A' }}, Initao
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account Type</label>
                                <p class="mt-1 text-gray-700 dark:text-gray-300">
                                    {{ $bill->serviceConnection->accountType->at_desc ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bill Details Card --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-file-invoice-dollar mr-3"></i>
                            Bill Details
                        </h2>
                    </div>
                    <div class="p-6">
                        {{-- Period and Due Date --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Billing Period</label>
                                <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $bill->period->per_name ?? 'Unknown Period' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</label>
                                <div class="mt-1 flex items-center gap-3">
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $bill->due_date?->format('F d, Y') ?? 'N/A' }}
                                    </p>
                                    @if ($bill->due_date && $bill->due_date->isPast())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Overdue
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Meter Readings --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-center">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Previous Reading</label>
                                <p class="mt-1 text-lg font-mono font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($bill->previousReading->reading_value ?? 0, 3) }} m³
                                </p>
                            </div>
                            <div class="text-center">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Current Reading</label>
                                <p class="mt-1 text-lg font-mono font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($bill->currentReading->reading_value ?? 0, 3) }} m³
                                </p>
                            </div>
                            <div class="text-center">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Consumption</label>
                                <p class="mt-1 text-lg font-mono font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($bill->consumption, 3) }} m³
                                </p>
                            </div>
                        </div>

                        {{-- Amount Breakdown --}}
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                            <div class="space-y-3">
                                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                    <span>Water Charge</span>
                                    <span class="font-mono">₱ {{ number_format($bill->water_amount, 2) }}</span>
                                </div>
                                @if ($bill->adjustment_total != 0)
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                        <span>Adjustments</span>
                                        <span class="font-mono {{ $bill->adjustment_total < 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $bill->adjustment_total < 0 ? '-' : '' }}₱ {{ number_format(abs($bill->adjustment_total), 2) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white border-t border-gray-200 dark:border-gray-600 pt-3">
                                    <span>Total Amount Due</span>
                                    <span class="font-mono text-blue-600 dark:text-blue-400">₱ {{ number_format($totalAmount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Form Card --}}
                <form action="{{ route('payment.bill.store', $bill->bill_id) }}" method="POST"
                    x-data="{
                        totalDue: {{ $totalAmount }},
                        amountReceived: {{ $totalAmount }},
                        get change() {
                            return Math.max(0, this.amountReceived - this.totalDue);
                        },
                        get isValid() {
                            return this.amountReceived >= this.totalDue;
                        }
                    }">
                    @csrf

                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                            <h2 class="text-lg font-semibold text-white flex items-center">
                                <i class="fas fa-cash-register mr-3"></i>
                                Payment
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Amount Received --}}
                                <div>
                                    <label for="amount_received"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Amount Received <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 dark:text-gray-400 font-semibold">₱</span>
                                        <input type="number" name="amount_received" id="amount_received"
                                            x-model.number="amountReceived" step="0.01" min="{{ $totalAmount }}"
                                            required
                                            class="w-full pl-10 pr-4 py-3 text-xl font-mono font-bold border-2 rounded-xl
                                                   focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                                   dark:bg-gray-700 dark:border-gray-600 dark:text-white
                                                   transition-all duration-200"
                                            :class="isValid ? 'border-green-300 bg-green-50 dark:bg-green-900/20' :
                                                'border-red-300 bg-red-50 dark:bg-red-900/20'">
                                    </div>
                                    <p class="mt-2 text-sm" :class="isValid ? 'text-green-600' : 'text-red-600'">
                                        <i class="fas" :class="isValid ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                                        <span x-text="isValid ? 'Amount is sufficient' : 'Amount must be at least ₱' + totalDue.toFixed(2)"></span>
                                    </p>
                                </div>

                                {{-- Change --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Change
                                    </label>
                                    <div
                                        class="w-full px-4 py-3 text-xl font-mono font-bold bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 border-2 border-green-300 dark:border-green-700 rounded-xl text-green-700 dark:text-green-300">
                                        ₱ <span x-text="change.toFixed(2)">0.00</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('payment.management') }}"
                                    class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </a>
                                <button type="submit" :disabled="!isValid"
                                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg">
                                    <i class="fas fa-check-circle mr-2"></i>Process Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
