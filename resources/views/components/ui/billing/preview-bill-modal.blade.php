<div id="previewBillModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Preview Water Bill</h3>
            <button onclick="closePreviewBillModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Bill Preview Content -->
        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg mb-6">
            <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b border-gray-300 dark:border-gray-600">
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">Consumer</p>
                    <p id="previewConsumer" class="text-lg font-semibold text-gray-900 dark:text-white">Juan Dela Cruz</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">Bill No</p>
                    <p id="previewBillNo" class="text-lg font-semibold text-gray-900 dark:text-white">WB-2024-00001</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-6">
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 uppercase">Billing Period</p>
                    <p id="previewPeriod" class="font-medium text-gray-900 dark:text-white">January 2024</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 uppercase">Due Date</p>
                    <p id="previewDueDate" class="font-medium text-gray-900 dark:text-white">--</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 uppercase">Account No</p>
                    <p id="previewAccountNo" class="font-medium text-gray-900 dark:text-white">--</p>
                </div>
            </div>

            <div class="border-t border-b border-gray-300 dark:border-gray-600 py-4">
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Previous Reading</p>
                        <p id="previewPrevReading" class="text-lg font-semibold text-gray-900 dark:text-white">0.000 m³</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Current Reading</p>
                        <p id="previewCurrentReading" class="text-lg font-semibold text-gray-900 dark:text-white">0.000 m³</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Consumption</p>
                        <p id="previewConsumption" class="text-lg font-semibold text-gray-900 dark:text-white">0.000 m³</p>
                    </div>
                </div>
            </div>

            <div class="my-6 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Water Charge:</span>
                    <span id="previewWaterCharge" class="font-medium text-gray-900 dark:text-white">₱0.00</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Sewerage Charge:</span>
                    <span id="previewSewerageCharge" class="font-medium text-gray-900 dark:text-white">₱0.00</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Penalties/Adjustments:</span>
                    <span id="previewPenalties" class="font-medium text-gray-900 dark:text-white">₱0.00</span>
                </div>
            </div>

            <div class="border-t-2 border-gray-400 dark:border-gray-500 pt-4 flex justify-between items-center">
                <span class="text-lg font-bold text-gray-900 dark:text-white">Total Amount Due:</span>
                <span id="previewTotalAmount" class="text-3xl font-bold text-blue-600 dark:text-blue-400">₱0.00</span>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button onclick="closePreviewBillModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Close</button>
            <button onclick="downloadPreviewBill()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                <i class="fas fa-download mr-2"></i>Download PDF
            </button>
        </div>
    </div>
</div>

<script>
function openPreviewBillModal(billData) {
    document.getElementById('previewBillModal').classList.remove('hidden');
    
    if (billData) {
        document.getElementById('previewConsumer').textContent = billData.consumer || 'Juan Dela Cruz';
        document.getElementById('previewBillNo').textContent = billData.bill_no || 'WB-2024-00001';
        document.getElementById('previewPeriod').textContent = billData.period || 'January 2024';
        document.getElementById('previewDueDate').textContent = billData.due_date || '--';
        document.getElementById('previewAccountNo').textContent = billData.account_no || '--';
        document.getElementById('previewPrevReading').textContent = (billData.prev_reading || 0) + ' m³';
        document.getElementById('previewCurrentReading').textContent = (billData.current_reading || 0) + ' m³';
        document.getElementById('previewConsumption').textContent = (billData.consumption || 0) + ' m³';
        document.getElementById('previewWaterCharge').textContent = '₱' + (billData.water_charge || 0).toFixed(2);
        document.getElementById('previewSewerageCharge').textContent = '₱' + (billData.sewerage_charge || 0).toFixed(2);
        document.getElementById('previewPenalties').textContent = '₱' + (billData.penalties || 0).toFixed(2);
        document.getElementById('previewTotalAmount').textContent = '₱' + (billData.total_amount || 0).toFixed(2);
    }
}

function closePreviewBillModal() {
    document.getElementById('previewBillModal').classList.add('hidden');
}

function downloadPreviewBill() {
    const billNo = document.getElementById('previewBillNo').textContent;
    if (window.showToast) {
        showToast('Bill preview downloaded successfully!', 'success');
    } else {
        alert('Downloading: ' + billNo + '.pdf');
    }
}

window.openPreviewBillModal = openPreviewBillModal;
window.closePreviewBillModal = closePreviewBillModal;
window.downloadPreviewBill = downloadPreviewBill;
</script>
