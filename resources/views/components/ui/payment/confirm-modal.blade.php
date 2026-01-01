<div id="paymentConfirmModal" class="hidden fixed inset-0 bg-black/40 z-50 items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Confirm Payment</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span>Customer</span><span id="confirmCustomerName" class="font-medium"></span></div>
            <div class="flex justify-between"><span>Purpose</span><span id="confirmPurpose" class="font-medium"></span></div>
            <div class="flex justify-between"><span>Method</span><span id="confirmMethod" class="font-medium"></span></div>
            <div class="flex justify-between"><span>Total</span><span id="confirmTotal" class="font-bold text-green-600"></span></div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button id="confirmCancelBtn" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300">Cancel</button>
            <button id="confirmProceedBtn" class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white">Proceed</button>
        </div>
    </div>
</div>

