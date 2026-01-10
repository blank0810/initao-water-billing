<!-- Bill Details Tab -->
<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Consumption Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-tachometer-alt mr-2"></i>Meter Reading
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Previous Reading</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">100 m³</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg">
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Current Reading</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">125 m³</p>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/30 p-4 rounded-lg">
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Consumption</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">25 m³</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg">
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Days Covered</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">31 days</p>
                    </div>
                </div>
            </div>

            <!-- Rate Structure Applied -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-layer-group mr-2"></i>Rate Structure Applied
                </h3>
                
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    <strong>Rate Category:</strong> Residential Standard (Active) | <strong>Period:</strong> Jan 2024
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-2 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Consumption Range</th>
                                <th class="text-right py-2 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Rate/m³</th>
                                <th class="text-right py-2 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Usage</th>
                                <th class="text-right py-2 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Charge</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/20">
                                <td class="py-3 px-4 text-gray-900 dark:text-white">0 - 10 m³</td>
                                <td class="text-right py-3 px-4 text-gray-900 dark:text-white font-semibold">₱10.00</td>
                                <td class="text-right py-3 px-4 text-gray-900 dark:text-white font-semibold">10 m³</td>
                                <td class="text-right py-3 px-4 text-blue-600 dark:text-blue-400 font-semibold">₱100.00</td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-700 bg-green-50 dark:bg-green-900/20">
                                <td class="py-3 px-4 text-gray-900 dark:text-white">11 - 20 m³</td>
                                <td class="text-right py-3 px-4 text-gray-900 dark:text-white font-semibold">₱12.00</td>
                                <td class="text-right py-3 px-4 text-gray-900 dark:text-white font-semibold">10 m³</td>
                                <td class="text-right py-3 px-4 text-green-600 dark:text-green-400 font-semibold">₱120.00</td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-700 bg-orange-50 dark:bg-orange-900/20">
                                <td class="py-3 px-4 text-gray-900 dark:text-white">21+ m³</td>
                                <td class="text-right py-3 px-4 text-gray-900 dark:text-white font-semibold">₱15.00</td>
                                <td class="text-right py-3 px-4 text-gray-900 dark:text-white font-semibold">5 m³</td>
                                <td class="text-right py-3 px-4 text-orange-600 dark:text-orange-400 font-semibold">₱75.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bill Computation -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-receipt mr-2"></i>Bill Computation
                </h3>
                
                <div class="space-y-2">
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Water Charges (as computed above)</span>
                        <span class="font-semibold text-gray-900 dark:text-white">₱295.00</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Basic Service Charge</span>
                        <span class="font-semibold text-gray-900 dark:text-white">₱500.00</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Environmental Fee</span>
                        <span class="font-semibold text-gray-900 dark:text-white">₱0.00</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Penalties / Surcharge</span>
                        <span class="font-semibold text-gray-900 dark:text-white">₱0.00</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Adjustments / Credits</span>
                        <span class="font-semibold text-gray-900 dark:text-white">₱55.80</span>
                    </div>
                    <div class="flex justify-between py-3 bg-blue-50 dark:bg-blue-900/30 px-4 rounded-lg">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">TOTAL AMOUNT DUE</span>
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">₱739.20</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-bolt mr-2"></i>Quick Actions
                </h3>
                
                <div class="space-y-2">
                    <button onclick="openPaymentModal()" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-credit-card mr-2"></i>Pay Now
                    </button>
                    <button onclick="window.print()" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-print mr-2"></i>Print Bill
                    </button>
                    <button class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </button>
                    <button class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-share mr-2"></i>Email Bill
                    </button>
                </div>
            </div>

            <!-- Reminders -->
            <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-500 rounded-lg p-4">
                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>Important
                </h4>
                <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                    <li>• Due date: Feb 15, 2024</li>
                    <li>• Late payment penalty: 5% per month</li>
                    <li>• Payment channels available</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function openPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
}
window.openPaymentModal = openPaymentModal;
</script>
