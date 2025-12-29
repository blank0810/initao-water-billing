<form id="paymentForm" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 transition-all duration-300">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
            <i class="fas fa-money-bill-wave text-green-600 dark:text-green-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Payment Processing</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="transition-opacity duration-300" :class="{'opacity-50 pointer-events-none': paymentType === 'document'}">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Amount Received *</label>
            <div class="relative">
                <span class="absolute left-4 top-3.5 text-gray-500 font-bold">₱</span>
                <input type="number" id="amountReceived" step="0.01" min="0" required class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            </div>
        </div>
        <div class="transition-opacity duration-300" :class="{'opacity-50 pointer-events-none': paymentType === 'document'}">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Payment Method *</label>
            <select id="paymentMethod" required class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                <option value="">Select Method</option>
                <option value="CASH">💵 Cash</option>
                <option value="CARD">💳 Card</option>
                <option value="BANK_TRANSFER">🏦 Bank Transfer</option>
                <option value="GCASH">📱 GCash</option>
                <option value="PAYMAYA">📱 PayMaya</option>
                <option value="OTHER">Other</option>
            </select>
        </div>
        <div class="transition-opacity duration-300" :class="{'opacity-50 pointer-events-none': paymentType === 'document'}">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Purpose of Payment *</label>
            <select id="paymentPurpose" required class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                <option value="">Select Purpose</option>
                <option value="BILL_PAYMENT">Bill Payment</option>
                <option value="APPLICATION_FEE">Application Fee</option>
                <option value="CONNECTION_FEE">Connection Fee</option>
                <option value="SECURITY_DEPOSIT">Security Deposit</option>
                <option value="METER_INSTALLATION">Meter Installation</option>
                <option value="ADJUSTMENT_PENALTY">Adjustment/Penalty</option>
                <option value="OTHER">Other</option>
            </select>
        </div>
        <div class="md:col-span-2 transition-opacity duration-300" :class="{'opacity-50 pointer-events-none': paymentType === 'document'}">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reference Number</label>
            <input type="text" id="referenceNumber" placeholder="Optional - Transaction reference" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
    </div>
</form>

