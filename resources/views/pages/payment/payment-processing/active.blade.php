<!-- Active Customer Payment Processing -->
<div class="space-y-6">
    <!-- Customer Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-user-check text-green-600 mr-2"></i>
            Active Customer Information
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Name</label>
                <input type="text" x-model="customerName" readonly class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Number</label>
                <input type="text" x-model="accountNo" readonly class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meter Number</label>
                <input type="text" x-model="meterNo" readonly class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service Address</label>
                <input type="text" x-model="address" readonly class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            </div>
        </div>
    </div>

    <!-- Current Bill -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-file-invoice text-blue-600 mr-2"></i>
            Current Water Bill
        </h2>
        <table class="w-full">
            <tbody>
                <tr class="border-b border-gray-100 dark:border-gray-700">
                    <td class="py-3 text-sm text-gray-900 dark:text-gray-100">Water Consumption</td>
                    <td class="py-3 text-sm text-right font-medium text-gray-900 dark:text-gray-100" x-text="formatCurrency(420.50)"></td>
                </tr>
                <tr class="border-b border-gray-100 dark:border-gray-700">
                    <td class="py-3 text-sm text-gray-900 dark:text-gray-100">Late Payment Penalty</td>
                    <td class="py-3 text-sm text-right font-medium text-gray-900 dark:text-gray-100" x-text="formatCurrency(30.00)"></td>
                </tr>
                <tr class="bg-gray-50 dark:bg-gray-700">
                    <td class="py-3 text-sm font-bold text-gray-900 dark:text-white">TOTAL AMOUNT DUE</td>
                    <td class="py-3 text-right text-lg font-bold text-blue-600 dark:text-blue-400" x-text="formatCurrency(450.50)"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Payment Details -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-credit-card text-purple-600 mr-2"></i>
            Payment Details
        </h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Type <span class="text-red-500">*</span></label>
                <select x-model="paymentPurpose" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Type</option>
                    <option value="current_bill">Current Bill Payment</option>
                    <option value="full_settlement">Full Settlement</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method <span class="text-red-500">*</span></label>
                    <select x-model="paymentMethod" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="check">Check</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="gcash">GCash</option>
                        <option value="maya">Maya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" x-model="paymentDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reference Number</label>
                <input type="text" x-model="referenceNumber" placeholder="Optional" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                <textarea x-model="remarks" rows="2" placeholder="Additional notes" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
        </div>
    </div>

    <!-- Payment Summary -->
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-700 rounded-lg shadow-sm border border-green-200 dark:border-gray-600 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-calculator text-green-600 mr-2"></i>
            Payment Summary
        </h2>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-700 dark:text-gray-300">Current Bill:</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(450.50)"></span>
            </div>
            <div class="flex justify-between items-center pt-3 border-t border-green-200 dark:border-gray-600">
                <span class="text-lg font-bold text-gray-900 dark:text-white">Amount to Pay:</span>
                <span class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="formatCurrency(450.50)"></span>
            </div>
        </div>
    </div>
</div>
