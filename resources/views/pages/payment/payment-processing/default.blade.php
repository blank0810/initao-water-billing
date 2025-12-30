<!-- Default Payment Processing -->
<div class="space-y-6">
    <!-- Customer Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-user-circle text-blue-600 mr-2"></i>
            Customer Information
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Name</label>
                <input type="text" x-model="customerName" readonly class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Code</label>
                <input type="text" x-model="customerCode" readonly class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <input type="text" x-model="customerStatus" readonly class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            </div>
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                <input type="text" x-model="address" readonly class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            </div>
        </div>
    </div>

    <!-- Payment Purpose Selection -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
            Payment Purpose
        </h2>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Payment Purpose <span class="text-red-500">*</span></label>
            <select x-model="selectedPurpose" @change="loadPurposeCharges()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="">-- Select Purpose --</option>
                <template x-for="purpose in availablePurposes" :key="purpose.value">
                    <option :value="purpose.value" x-text="purpose.label"></option>
                </template>
                <option value="custom">Custom Payment</option>
            </select>
        </div>
    </div>

    <!-- Charges Breakdown -->
    <div x-show="selectedPurpose" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-file-invoice-dollar text-green-600 mr-2"></i>
            Charges Breakdown
        </h2>
        
        <!-- Custom Amount Input -->
        <div x-show="selectedPurpose === 'custom'" class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Custom Amount <span class="text-red-500">*</span></label>
            <input type="number" x-model="customAmount" @input="updateCustomCharges()" step="0.01" min="0" placeholder="Enter amount" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
        </div>

        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Description</th>
                    <th class="text-right py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Amount</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="charge in charges" :key="charge.name">
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="py-3 text-sm text-gray-900 dark:text-gray-100" x-text="charge.name"></td>
                        <td class="py-3 text-sm text-right font-medium text-gray-900 dark:text-gray-100" x-text="formatCurrency(charge.amount)"></td>
                    </tr>
                </template>
                <tr class="bg-gray-50 dark:bg-gray-700">
                    <td class="py-3 text-sm font-bold text-gray-900 dark:text-white">TOTAL AMOUNT DUE</td>
                    <td class="py-3 text-right text-lg font-bold text-blue-600 dark:text-blue-400" x-text="formatCurrency(totalCharges)"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Payment Details -->
    <div x-show="selectedPurpose" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-credit-card text-purple-600 mr-2"></i>
            Payment Details
        </h2>
        <div class="space-y-4">
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
                        <option value="credit_card">Credit Card</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" x-model="paymentDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reference Number</label>
                <input type="text" x-model="referenceNumber" placeholder="Optional - Receipt or transaction number" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                <textarea x-model="remarks" rows="2" placeholder="Additional notes (optional)" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
        </div>
    </div>

    <!-- Payment Summary -->
    <div x-show="selectedPurpose" class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg shadow-sm border border-blue-200 dark:border-gray-600 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-calculator text-indigo-600 mr-2"></i>
            Payment Summary
        </h2>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-700 dark:text-gray-300">Total Charges:</span>
                <span class="text-lg font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(totalCharges)"></span>
            </div>
            <div class="flex justify-between items-center pt-3 border-t border-blue-200 dark:border-gray-600">
                <span class="text-lg font-bold text-gray-900 dark:text-white">Amount to Pay:</span>
                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="formatCurrency(totalCharges)"></span>
            </div>
        </div>
    </div>
</div>
