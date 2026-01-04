<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rate Structure</h3>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Type</label>
            <select id="rate-account-type" class="w-full md:w-64 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                <option value="RESIDENTIAL">Residential</option>
                <option value="COMMERCIAL">Commercial</option>
                <option value="INDUSTRIAL">Industrial</option>
            </select>
        </div>

        <div class="mb-6">
            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Consumption Tiers</h4>
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tier</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Range (m³)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Rate</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Label</th>
                    </tr>
                </thead>
                <tbody id="rate-tier-body" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
            </table>
        </div>

        <div>
            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Fixed Charges</h4>
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody id="rate-fixed-body" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Billing Calculator</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consumption (m³)</label>
                <input type="number" id="rate-calc-consumption" value="25" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Discount</label>
                <select id="rate-calc-discount" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    <option value="">None</option>
                    <option value="SENIOR_DISCOUNT">Senior (5%)</option>
                    <option value="PWD_DISCOUNT">PWD (5%)</option>
                </select>
            </div>
        </div>
        <button onclick="window.BillingModule.calculateRate()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Calculate</button>

        <div id="rate-calc-result" class="mt-6 hidden">
            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Breakdown</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span>Consumption:</span><span id="rate-calc-consumption-charge" class="font-semibold">₱0.00</span></div>
                <div class="flex justify-between"><span>Fixed:</span><span id="rate-calc-fixed" class="font-semibold">₱0.00</span></div>
                <div class="flex justify-between"><span>Subtotal:</span><span id="rate-calc-subtotal" class="font-semibold">₱0.00</span></div>
                <div class="flex justify-between text-green-600"><span>Discount:</span><span id="rate-calc-discount-amt" class="font-semibold">-₱0.00</span></div>
                <div class="flex justify-between"><span>VAT:</span><span id="rate-calc-vat" class="font-semibold">₱0.00</span></div>
                <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2"><span>Total:</span><span id="rate-calc-total">₱0.00</span></div>
            </div>
            <div id="rate-calc-tiers" class="mt-4"></div>
        </div>
    </div>
</div>
