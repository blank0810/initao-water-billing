<div id="previewBillModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Bill Preview</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Review bill details before generation</p>
            </div>
            <button onclick="closePreviewBillModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Preview Content -->
        <div class="space-y-6">
            <!-- Bill Header Info -->
            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Bill Number</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">B-2024-001</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Billing Period</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">January 2024</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Bill Date</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">2024-01-08</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Due Date</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">2024-02-08</p>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Customer Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Name</p>
                        <p class="font-medium text-gray-900 dark:text-white">Juan Dela Cruz</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Account No</p>
                        <p class="font-medium text-gray-900 dark:text-white">ACC-2024-001</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Meter Serial</p>
                        <p class="font-medium text-gray-900 dark:text-white">M-2024-00001</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Category</p>
                        <p class="font-medium text-gray-900 dark:text-white">Residential Standard</p>
                    </div>
                </div>
            </div>

            <!-- Consumption Details -->
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Consumption Details</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Previous Reading</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">2,750 m³</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Current Reading</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">2,775 m³</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Total Consumption</p>
                        <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">25 m³</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Reading Date</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">2024-01-30</p>
                    </div>
                </div>
            </div>

            <!-- Calculation Breakdown -->
            <div class="bg-white dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Charge Breakdown</h4>
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="text-left py-2 text-gray-600 dark:text-gray-400">Tier</th>
                            <th class="text-left py-2 text-gray-600 dark:text-gray-400">Consumption Range</th>
                            <th class="text-right py-2 text-gray-600 dark:text-gray-400">Rate</th>
                            <th class="text-right py-2 text-gray-600 dark:text-gray-400">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">Tier 1</td>
                            <td class="py-2 text-gray-600 dark:text-gray-400">0-10 m³</td>
                            <td class="text-right text-gray-900 dark:text-white">₱10.00/m³</td>
                            <td class="text-right font-semibold text-gray-900 dark:text-white">₱100.00</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">Tier 2</td>
                            <td class="py-2 text-gray-600 dark:text-gray-400">11-20 m³</td>
                            <td class="text-right text-gray-900 dark:text-white">₱12.00/m³</td>
                            <td class="text-right font-semibold text-gray-900 dark:text-white">₱60.00</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-white">Tier 3</td>
                            <td class="py-2 text-gray-600 dark:text-gray-400">21-25 m³</td>
                            <td class="text-right text-gray-900 dark:text-white">₱15.00/m³</td>
                            <td class="text-right font-semibold text-gray-900 dark:text-white">₱75.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 p-4 rounded-lg">
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">Consumption Charges:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">₱235.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">Base Charge:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">₱50.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-300">Penalties/Adjustments:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">₱9.20</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-300 dark:border-gray-500">
                        <span class="font-bold text-gray-900 dark:text-white">TOTAL BILL AMOUNT:</span>
                        <span class="font-bold text-lg text-blue-600 dark:text-blue-400">₱294.20</span>
                    </div>
                </div>
            </div>

            <!-- Discount Info (if applicable) -->
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                <p class="text-sm text-green-800 dark:text-green-300">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Note:</strong> All charges are calculated based on the approved rate structure for this billing period. Senior citizen/PWD discounts (if applicable) will be applied during posting.
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button onclick="printPreview()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <div class="flex gap-3">
                <button onclick="closePreviewBillModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button onclick="submitGenerateBillFromPreview()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-file-invoice mr-2"></i>Generate This Bill
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openBillPreviewModal() {
    document.getElementById('previewBillModal').classList.remove('hidden');
}

function closePreviewBillModal() {
    document.getElementById('previewBillModal').classList.add('hidden');
}

function printPreview() {
    const previewContent = document.getElementById('previewBillModal').querySelector('.bg-white');
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Bill Preview</title>');
    printWindow.document.write('<style>body { font-family: Arial; margin: 20px; }');
    printWindow.document.write('.section { margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 20px; }');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; }');
    printWindow.document.write('th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }');
    printWindow.document.write('th { background-color: #f2f2f2; font-weight: bold; }');
    printWindow.document.write('.total { font-weight: bold; font-size: 16px; }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<h2>Water Billing Preview</h2>');
    printWindow.document.write(previewContent.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

function submitGenerateBillFromPreview() {
    if (window.showToast) {
        showToast('Bill generated successfully!', 'success');
    } else {
        alert('Bill generated successfully!');
    }
    closePreviewBillModal();
}
</script>
