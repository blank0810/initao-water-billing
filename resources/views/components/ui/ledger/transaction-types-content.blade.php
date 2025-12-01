<div class="space-y-6">
    <!-- Transaction Type Cards -->
    <x-ui.card title="Transaction Types">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-6 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-red-500 hover:bg-red-50 dark:hover:bg-red-900 transition">
                <div class="flex items-center mb-4">
                    <i class="fas fa-file-invoice text-red-500 text-3xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">BILL</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Water Consumption</div>
                    </div>
                </div>
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                    <div>• Monthly water bills</div>
                    <div>• Consumption charges</div>
                    <div>• Fixed service fees</div>
                </div>
            </div>

            <div class="p-6 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900 transition">
                <div class="flex items-center mb-4">
                    <i class="fas fa-money-bill-wave text-green-500 text-3xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">PAYMENT</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Payment Received</div>
                    </div>
                </div>
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                    <div>• Cash payments</div>
                    <div>• Online payments</div>
                    <div>• Check payments</div>
                </div>
            </div>

            <div class="p-6 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900 transition">
                <div class="flex items-center mb-4">
                    <i class="fas fa-adjust text-orange-500 text-3xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">ADJUSTMENT</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Bill Adjustments</div>
                    </div>
                </div>
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                    <div>• Late payment penalties</div>
                    <div>• Discounts applied</div>
                    <div>• Bill corrections</div>
                </div>
            </div>

            <div class="p-6 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900 transition">
                <div class="flex items-center mb-4">
                    <i class="fas fa-coins text-purple-500 text-3xl mr-3"></i>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">CHARGE</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">One-Time Fees</div>
                    </div>
                </div>
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                    <div>• Installation fees</div>
                    <div>• Reconnection fees</div>
                    <div>• Security deposits</div>
                </div>
            </div>
        </div>
    </x-ui.card>

    <!-- Ledger Entry Flow -->
    <x-ui.card title="Ledger Entry Flow">
        <div class="space-y-4">
            <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Understanding Debit & Credit
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="p-3 bg-white dark:bg-gray-800 rounded">
                        <div class="font-semibold text-red-600 mb-1">DEBIT (Amount Owed)</div>
                        <ul class="text-gray-700 dark:text-gray-300 space-y-1">
                            <li>• Bills generated</li>
                            <li>• Penalties added</li>
                            <li>• One-time charges</li>
                            <li>• Increases balance</li>
                        </ul>
                    </div>
                    <div class="p-3 bg-white dark:bg-gray-800 rounded">
                        <div class="font-semibold text-green-600 mb-1">CREDIT (Amount Paid)</div>
                        <ul class="text-gray-700 dark:text-gray-300 space-y-1">
                            <li>• Payments received</li>
                            <li>• Discounts applied</li>
                            <li>• Refunds issued</li>
                            <li>• Decreases balance</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                    <i class="fas fa-calculator mr-2"></i>
                    Balance Calculation
                </h4>
                <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Outstanding Balance = Total Debits - Total Credits
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Positive balance means customer owes money • Negative balance means overpayment
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="p-3 bg-green-50 dark:bg-green-900 rounded-lg text-center">
                    <div class="text-xs text-green-600 dark:text-green-300 font-medium mb-1">ACTIVE</div>
                    <div class="text-sm text-gray-700 dark:text-gray-300">Entry is open</div>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900 rounded-lg text-center">
                    <div class="text-xs text-blue-600 dark:text-blue-300 font-medium mb-1">PAID</div>
                    <div class="text-sm text-gray-700 dark:text-gray-300">Fully cleared</div>
                </div>
                <div class="p-3 bg-red-50 dark:bg-red-900 rounded-lg text-center">
                    <div class="text-xs text-red-600 dark:text-red-300 font-medium mb-1">CANCELLED</div>
                    <div class="text-sm text-gray-700 dark:text-gray-300">Voided entry</div>
                </div>
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900 rounded-lg text-center">
                    <div class="text-xs text-yellow-600 dark:text-yellow-300 font-medium mb-1">ADJUSTED</div>
                    <div class="text-sm text-gray-700 dark:text-gray-300">Modified entry</div>
                </div>
            </div>
        </div>
    </x-ui.card>
</div>
