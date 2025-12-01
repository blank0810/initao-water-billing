<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

            <!-- Enhanced Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-credit-card text-white text-xl"></i>
                            </div>
                            Payment Management
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Process customer payments securely and efficiently</p>
                    </div>
                    <a href="{{ route('customer.list') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                </div>
            </div>

            <!-- Breadcrumb Process Guide -->
            <div class="mb-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-2 text-sm">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg">
                            <i class="fas fa-file-alt"></i>
                            <span class="font-medium">1. Application</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg">
                            <i class="fas fa-credit-card"></i>
                            <span class="font-medium">2. Payment</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg">
                            <i class="fas fa-check-circle"></i>
                            <span class="font-medium">3. Approval</span>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg">
                            <i class="fas fa-plug"></i>
                            <span class="font-medium">4. Connection</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Search Panel -->
            <div id="searchPanel" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-search text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Select Customer</h3>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="customerSearchInput" placeholder="Search by name, code, or address..." 
                        class="w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                </div>
                <div id="searchResults" class="mt-4 hidden">
                    <div class="border-2 border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase">Customer</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase">Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody id="searchResultsBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment Container -->
            <div id="paymentContainer" class="space-y-6" style="display:none;">

                <!-- Customer Info Card -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl shadow-lg p-6 border border-blue-200 dark:border-gray-600">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Customer Information</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Customer Code</label>
                            <input type="text" id="displayCustomerCode" readonly class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Customer Name</label>
                            <input type="text" id="displayCustomerName" readonly class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Address</label>
                            <input type="text" id="displayAddress" readonly class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase">Current Status</label>
                            <input type="text" id="displayStatus" readonly class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Invoice Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-invoice text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Invoice Details</h3>
                    </div>
                    <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Invoice Number:</span>
                            <span class="text-sm font-mono font-bold text-gray-900 dark:text-white" id="invoiceNumber"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Invoice Status:</span>
                            <span class="text-sm font-bold text-orange-600 dark:text-orange-400" id="invoiceStatus">PENDING</span>
                        </div>
                    </div>
                    <div class="border-t-2 border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <i class="fas fa-list-ul text-gray-400"></i>
                            Charges Breakdown
                        </h4>
                        <div id="chargesBreakdown" class="space-y-2"></div>
                        <div class="border-t-2 border-gray-200 dark:border-gray-700 mt-4 pt-4">
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Total Amount:</span>
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400" id="totalAmount"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form Card -->
                <form id="paymentForm" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-green-600 dark:text-green-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Payment Processing</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Amount Received *</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-gray-500 font-bold">₱</span>
                                <input type="number" id="amountReceived" step="0.01" min="0" required
                                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Payment Method *</label>
                            <select id="paymentMethod" required
                                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                <option value="">Select Method</option>
                                <option value="CASH">💵 Cash</option>
                                <option value="CARD">💳 Card</option>
                                <option value="BANK_TRANSFER">🏦 Bank Transfer</option>
                                <option value="GCASH">📱 GCash</option>
                                <option value="PAYMAYA">📱 PayMaya</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reference Number</label>
                            <input type="text" id="referenceNumber" placeholder="Optional - Transaction reference"
                                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>
                    </div>
                </form>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('customer.list') }}" class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 font-semibold transition-all">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="button" id="processPaymentBtn"
                        class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                        <i class="fas fa-check-circle mr-2"></i>Process Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/data/customer/customer.js', 'resources/js/data/customer/payment.js'])
</x-app-layout>
