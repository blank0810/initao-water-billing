<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Process Payment</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Application Fee Payment</p>
                </div>
            </div>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-4">
            <!-- Application Info -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Application #:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="paymentAppNumber">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="paymentCustomerName">-</span>
                    </div>
                </div>
            </div>

            <!-- Charges Breakdown -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Charges Breakdown</h4>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50">
                                <th class="text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase px-4 py-2">Description</th>
                                <th class="text-right text-xs font-medium text-gray-600 dark:text-gray-400 uppercase px-4 py-2">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="paymentChargesList" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <!-- Charges will be inserted here -->
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-white">Total Amount Due</td>
                                <td class="px-4 py-3 text-lg font-bold text-gray-900 dark:text-white text-right" id="paymentTotalAmount">₱0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Payment Input -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Amount Received <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">₱</span>
                    <input type="number" id="paymentAmountReceived" step="0.01" min="0"
                        class="w-full pl-8 pr-4 py-3 text-lg font-medium border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="0.00"
                        oninput="calculateChange()">
                </div>
            </div>

            <!-- Change Calculation -->
            <div id="changeSection" class="hidden bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-green-800 dark:text-green-200">Change</span>
                    <span class="text-xl font-bold text-green-600 dark:text-green-400" id="paymentChange">₱0.00</span>
                </div>
            </div>

            <!-- Insufficient Amount Warning -->
            <div id="insufficientWarning" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <p class="text-sm text-red-800 dark:text-red-200">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Full payment required. Amount received is less than the total due.
                </p>
            </div>

            <input type="hidden" id="paymentApplicationId" value="">
            <input type="hidden" id="paymentTotalValue" value="0">
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <button onclick="closePaymentModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="submitPayment()" id="paymentSubmitBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <i class="fas fa-check-circle mr-2"></i>Process Payment
            </button>
        </div>
    </div>
</div>

<!-- Payment Success Modal -->
<div id="paymentSuccessModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full text-center p-8">
        <div class="w-20 h-20 mx-auto bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-6">
            <i class="fas fa-check-circle text-green-500 text-4xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Payment Successful!</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-4">Receipt has been generated.</p>
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Receipt Number</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white font-mono" id="successReceiptNo">-</p>
        </div>
        <button onclick="closePaymentSuccessModal()" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
            <i class="fas fa-check mr-2"></i>Done
        </button>
    </div>
</div>

<script>
let paymentTotalDue = 0;

function openPaymentModal(applicationId, appNumber, customerName, charges, totalAmount) {
    document.getElementById('paymentApplicationId').value = applicationId;
    document.getElementById('paymentAppNumber').textContent = appNumber;
    document.getElementById('paymentCustomerName').textContent = customerName;

    // Populate charges list
    const chargesList = document.getElementById('paymentChargesList');
    chargesList.innerHTML = '';

    if (charges && charges.length > 0) {
        charges.forEach(charge => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">${charge.description}</td>
                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white text-right font-medium">${formatCurrency(charge.remaining_amount)}</td>
            `;
            chargesList.appendChild(row);
        });
    }

    // Set total
    paymentTotalDue = totalAmount || 0;
    document.getElementById('paymentTotalAmount').textContent = formatCurrency(paymentTotalDue);
    document.getElementById('paymentTotalValue').value = paymentTotalDue;

    // Pre-fill amount with exact total
    document.getElementById('paymentAmountReceived').value = paymentTotalDue.toFixed(2);
    calculateChange();

    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentAmountReceived').value = '';
    document.getElementById('changeSection').classList.add('hidden');
    document.getElementById('insufficientWarning').classList.add('hidden');
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount || 0);
}

function calculateChange() {
    const amountReceived = parseFloat(document.getElementById('paymentAmountReceived').value) || 0;
    const totalDue = parseFloat(document.getElementById('paymentTotalValue').value) || 0;
    const change = amountReceived - totalDue;

    const changeSection = document.getElementById('changeSection');
    const insufficientWarning = document.getElementById('insufficientWarning');
    const submitBtn = document.getElementById('paymentSubmitBtn');

    if (amountReceived >= totalDue && amountReceived > 0) {
        document.getElementById('paymentChange').textContent = formatCurrency(change);
        changeSection.classList.remove('hidden');
        insufficientWarning.classList.add('hidden');
        submitBtn.disabled = false;
    } else if (amountReceived > 0) {
        changeSection.classList.add('hidden');
        insufficientWarning.classList.remove('hidden');
        submitBtn.disabled = true;
    } else {
        changeSection.classList.add('hidden');
        insufficientWarning.classList.add('hidden');
        submitBtn.disabled = true;
    }
}

async function submitPayment() {
    const applicationId = document.getElementById('paymentApplicationId').value;
    const amountReceived = parseFloat(document.getElementById('paymentAmountReceived').value) || 0;
    const btn = document.getElementById('paymentSubmitBtn');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

    try {
        const response = await fetch(`/connection/service-application/${applicationId}/process-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                amount_received: amountReceived
            })
        });

        const data = await response.json();

        if (data.success) {
            closePaymentModal();
            // Show success modal with receipt number
            document.getElementById('successReceiptNo').textContent = data.data.receipt_no;
            document.getElementById('paymentSuccessModal').classList.remove('hidden');
        } else {
            throw new Error(data.message || 'Payment processing failed');
        }
    } catch (error) {
        alert('Error: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Process Payment';
    }
}

function closePaymentSuccessModal() {
    document.getElementById('paymentSuccessModal').classList.add('hidden');
    // Reload page to show updated status
    location.reload();
}
</script>
