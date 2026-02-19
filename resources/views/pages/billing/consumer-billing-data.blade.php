{{-- Customer Profile Card --}}
<x-ui.card id="customerProfileCard" class="mb-6">
    <div class="flex justify-between items-start mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Customer Profile</h3>
        <span id="overdueBadge" class="px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full text-sm font-medium hidden">
            <i class="fas fa-exclamation-triangle mr-1"></i>Overdue by <span id="overdueDays">0</span> day(s)
        </span>
    </div>
    <div class="flex items-start space-x-6 mb-6">
        <div class="h-16 w-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
            <span id="customerInitials">--</span>
        </div>
        <div class="flex-1">
            <h4 id="customerName" class="text-xl font-bold text-gray-900 dark:text-white mb-3">Select a consumer</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                <div class="flex items-center">
                    <i class="fas fa-id-card text-gray-400 w-5 mr-3"></i>
                    <span class="text-gray-500 dark:text-gray-400 w-20">ID:</span>
                    <span id="customerId" class="text-gray-900 dark:text-white font-mono font-medium">--</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-tag text-gray-400 w-5 mr-3"></i>
                    <span class="text-gray-500 dark:text-gray-400 w-20">Class:</span>
                    <span id="customerClass" class="text-gray-900 dark:text-white font-medium">--</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-tachometer-alt text-gray-400 w-5 mr-3"></i>
                    <span class="text-gray-500 dark:text-gray-400 w-20">Meter:</span>
                    <span id="meterNo" class="text-gray-900 dark:text-white font-mono font-medium">--</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-circle text-gray-400 w-5 mr-3"></i>
                    <span class="text-gray-500 dark:text-gray-400 w-20">Status:</span>
                    <span id="customerStatus" class="text-gray-900 dark:text-white font-medium">--</span>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <div class="space-y-3">
            <div class="flex items-center text-gray-600 dark:text-gray-400 font-medium">
                <i class="fas fa-address-book mr-2"></i>Contact Information
            </div>
            <div class="space-y-2 ml-6">
                <div class="flex items-center text-sm">
                    <i class="fas fa-envelope text-gray-400 w-4 mr-2"></i>
                    <span id="customerEmail" class="text-gray-900 dark:text-white">--</span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-phone text-gray-400 w-4 mr-2"></i>
                    <span id="customerPhone" class="text-gray-900 dark:text-white">--</span>
                </div>
            </div>
        </div>
        <div class="space-y-3">
            <div class="flex items-center text-gray-600 dark:text-gray-400 font-medium">
                <i class="fas fa-map-marker-alt mr-2"></i>Location
            </div>
            <div class="ml-6 text-sm">
                <span id="customerLocation" class="text-gray-900 dark:text-white">--</span>
            </div>
        </div>
    </div>
</x-ui.card>

{{-- Current Bill Overview Card --}}
<x-ui.card id="billOverviewCard" title="Current Bill Overview" class="mb-6">
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 text-white shadow-lg mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="text-sm opacity-90 mb-1">Current Amount Due</div>
                <div id="currentAmount" class="text-3xl font-bold">₱0.00</div>
            </div>
            <div class="text-right opacity-90">
                <i class="fas fa-file-invoice-dollar text-2xl"></i>
            </div>
        </div>
        <button id="processPaymentBtn" onclick="openProcessPaymentModal()" class="w-full bg-white text-blue-600 border-white hover:bg-blue-50 px-4 py-2 rounded-lg font-medium transition">
            <i class="fas fa-credit-card mr-2"></i>Process Payment
        </button>
        <div id="paidStatusMessage" class="hidden w-full bg-white/20 text-white px-4 py-2 rounded-lg font-medium text-center">
            <i class="fas fa-check-circle mr-2"></i>Bill Paid
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm">
                <i class="fas fa-calendar-alt mr-2"></i>Billing Period
            </div>
            <div id="billingPeriod" class="text-lg font-semibold text-gray-900 dark:text-white ml-6">--</div>
        </div>
        <div class="space-y-3">
            <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm">
                <i class="fas fa-receipt mr-2"></i>Bill Details
            </div>
            <div class="ml-6 space-y-2">
                <div class="flex items-center gap-2">
                    <span id="billNo" class="text-lg font-semibold text-gray-900 dark:text-white">--</span>
                    <span id="billingStatus" class="px-2 py-1 rounded-full text-xs font-medium">--</span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                    <div><i class="fas fa-calendar-plus mr-1"></i>Issued: <span id="issuedDate">--</span></div>
                    <div><i class="fas fa-calendar-times mr-1"></i>Due: <span id="dueDate">--</span></div>
                </div>
            </div>
        </div>
    </div>
</x-ui.card>

{{-- Recent Activities Card --}}
<x-ui.card id="recentActivitiesCard" title="Recent Activities" class="mb-6">
    <div class="space-y-3" id="activitiesList"></div>
</x-ui.card>

{{-- Water Usage Details Card --}}
<x-ui.card id="waterUsageCard" title="Water Usage Details" class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div class="flex items-center text-gray-600 dark:text-gray-400 font-medium">
                <i class="fas fa-droplet mr-2"></i>Current Reading
            </div>
            <div class="space-y-3 ml-6">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-gray-400 text-sm">Consumption</span>
                    <span id="consumption" class="text-lg font-semibold text-blue-600 dark:text-blue-400">--</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-gray-400 text-sm">Current Usage</span>
                    <span id="currentUsage" class="text-lg font-semibold text-green-600 dark:text-green-400">--</span>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="flex items-center text-gray-600 dark:text-gray-400 font-medium">
                <i class="fas fa-tachometer-alt mr-2"></i>Meter Information
            </div>
            <div class="space-y-3 ml-6">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-gray-400 text-sm">Reading</span>
                    <span id="meterReading" class="text-gray-900 dark:text-white font-semibold">--</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-gray-400 text-sm">Date Read</span>
                    <span id="dateRead" class="text-gray-900 dark:text-white font-semibold">--</span>
                </div>
            </div>
        </div>
    </div>
</x-ui.card>

{{-- Billing Summary Card --}}
<x-ui.card id="billSummaryCard" title="Billing Summary" class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                <div class="flex items-center">
                    <i class="fas fa-file-invoice text-blue-500 mr-3"></i>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Bills</span>
                </div>
                <span id="totalMonthBills" class="text-xl font-bold text-blue-600 dark:text-blue-400">0</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-xl">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Unpaid Bills</span>
                </div>
                <span id="unpaidMonthBills" class="text-xl font-bold text-red-600 dark:text-red-400">0</span>
            </div>
        </div>
        <div class="space-y-4">
            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl">
                <div class="flex items-center mb-2">
                    <i class="fas fa-dollar-sign text-yellow-500 mr-2"></i>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Unpaid Amount</span>
                </div>
                <div id="totalUnpaidAmount" class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">₱0.00</div>
            </div>
            <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-gray-500 mr-2"></i>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Overall Status</span>
                </div>
                <span id="overallStatusBadge" class="px-3 py-1 rounded-full text-xs font-medium">--</span>
            </div>
        </div>
    </div>
</x-ui.card>

{{-- Billing History Card --}}
<x-ui.card id="billingHistoryCard" title="Billing History" class="mb-6">
    <div class="divide-y divide-gray-200 dark:divide-gray-700" id="billingHistoryList"></div>
    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
            <i class="fas fa-history mr-2"></i>View Complete History
        </button>
    </div>
</x-ui.card>

{{-- Monthly Bill Trend Card --}}
<x-ui.card id="billTrendGraphCard" title="Monthly Bill Trend" class="mb-6">
    <div class="h-64 mb-4">
        <canvas id="monthlyTrendChart"></canvas>
    </div>
    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
        <button class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
            <i class="fas fa-chart-line mr-2"></i>View Detailed Analytics
        </button>
    </div>
</x-ui.card>

{{-- Process Payment Modal --}}
<div id="processPaymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Process Payment</h3>
            <button onclick="closeProcessPaymentModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Amount Due</div>
                    <div id="modalAmountDue" class="text-2xl font-bold text-blue-600 dark:text-blue-400">₱0.00</div>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Unpaid Bills</div>
                    <div id="modalUnpaidCount" class="text-2xl font-bold text-green-600 dark:text-green-400">0</div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consumer</label>
                    <input type="text" id="modalConsumerName" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Number</label>
                    <input type="text" id="modalAccountNo" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Receipt Number</label>
                    <input type="text" id="modalReceiptNo" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Date</label>
                    <input type="date" id="modalPaymentDate" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount Received *</label>
                    <input type="number" id="modalPaymentAmount" required placeholder="0.00" min="0" step="0.01" onchange="updateAllocation()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method *</label>
                    <select id="modalPaymentMethod" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Select method...</option>
                        <option value="CASH">Cash</option>
                        <option value="CHECK">Check</option>
                        <option value="BANK_TRANSFER">Bank Transfer</option>
                        <option value="GCASH">GCash</option>
                        <option value="PAYMAYA">PayMaya</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Processed By *</label>
                <select id="modalProcessedBy" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Select staff...</option>
                    <option value="1">Admin User</option>
                    <option value="2">Cashier 1</option>
                    <option value="3">Cashier 2</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Apply Payment To</label>
                <div id="modalBillsList" class="space-y-2 max-h-48 overflow-y-auto p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"></div>
            </div>
            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Allocated:</span>
                    <span id="modalTotalAllocated" class="text-lg font-bold text-yellow-600 dark:text-yellow-400">₱0.00</span>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Remaining:</span>
                    <span id="modalRemaining" class="text-lg font-bold text-gray-900 dark:text-white">₱0.00</span>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeProcessPaymentModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="submitProcessPayment()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="fas fa-check mr-2"></i>Process Payment
            </button>
        </div>
    </div>
</div>

{{-- Bill Photo Lightbox Modal --}}
<div id="billPhotoModal" class="hidden fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center" onclick="closeBillPhotoModal(event)">
    <div class="relative max-w-2xl w-full mx-4" onclick="event.stopPropagation()">
        <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-camera mr-2 text-gray-400"></i>
                    <span id="billPhotoTitle">Meter Reading Photo</span>
                </h3>
                <button onclick="closeBillPhotoModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <img id="billPhotoImage" src="" alt="Meter Reading" class="w-full h-auto max-h-[60vh] object-contain rounded-lg">
            </div>
        </div>
    </div>
</div>

<script>
function updateCustomerProfile(details) {
    if (!details) return;
    document.getElementById('customerInitials').textContent = details.name.slice(0, 2).toUpperCase();
    document.getElementById('customerName').textContent = details.name;
    document.getElementById('customerId').textContent = details.id;
    document.getElementById('customerClass').textContent = details.class;
    document.getElementById('meterNo').textContent = details.meterNo;
    document.getElementById('customerStatus').textContent = details.status;
    document.getElementById('customerEmail').textContent = details.email;
    document.getElementById('customerPhone').textContent = details.phone;
    document.getElementById('customerLocation').textContent = details.location;
    const overdueBadge = document.getElementById('overdueBadge');
    if (details.overdueDays > 0) {
        overdueBadge.classList.remove('hidden');
        document.getElementById('overdueDays').textContent = details.overdueDays;
    } else {
        overdueBadge.classList.add('hidden');
    }
}

function updateBillOverview(details) {
    if (!details) return;
    document.getElementById('currentAmount').textContent = `₱${details.currentAmountDue.toFixed(2)}`;
    document.getElementById('billingPeriod').textContent = details.billingPeriod;
    document.getElementById('billNo').textContent = details.billNo;
    document.getElementById('issuedDate').textContent = details.issuedDate;
    document.getElementById('dueDate').textContent = details.dueDate;
    const statusElement = document.getElementById('billingStatus');
    statusElement.textContent = details.billingStatus;
    statusElement.className = 'px-2 py-1 rounded-full text-xs font-semibold shadow ' +
        (details.billingStatus === 'PAID' ? 'bg-green-200 text-green-800' :
         details.billingStatus === 'OVERDUE' ? 'bg-red-200 text-red-800' :
         'bg-yellow-200 text-yellow-800');

    // Toggle Process Payment button visibility based on bill status
    const processPaymentBtn = document.getElementById('processPaymentBtn');
    const paidStatusMessage = document.getElementById('paidStatusMessage');
    if (details.billingStatus === 'PAID') {
        processPaymentBtn.classList.add('hidden');
        paidStatusMessage.classList.remove('hidden');
    } else {
        processPaymentBtn.classList.remove('hidden');
        paidStatusMessage.classList.add('hidden');
    }
}

function updateRecentActivities(details) {
    if (!details) return;
    const list = document.getElementById('activitiesList');
    list.innerHTML = details.activities.map(a => `
        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
            <div class="flex items-center">
                <i class="fas fa-circle text-blue-400 text-xs mr-3"></i>
                <span class="text-gray-900 dark:text-white text-sm font-medium">${a.action}</span>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">${a.date}</span>
        </div>
    `).join('');
}

function updateWaterUsage(details) {
    if (!details) return;
    document.getElementById('consumption').textContent = details.consumption;
    document.getElementById('currentUsage').textContent = details.currentUsage;
    document.getElementById('meterReading').textContent = details.meterReading;
    document.getElementById('dateRead').textContent = details.dateRead;
}

function updateBillSummary(details) {
    if (!details) return;
    document.getElementById('totalMonthBills').textContent = details.totalMonthBills;
    document.getElementById('unpaidMonthBills').textContent = details.unpaidMonthBills;
    document.getElementById('totalUnpaidAmount').textContent = `₱${details.totalUnpaidAmount.toFixed(2)}`;
    const statusBadge = document.getElementById('overallStatusBadge');
    statusBadge.textContent = details.overallBillingStatus;
    statusBadge.className = 'px-3 py-1 rounded-full text-xs font-medium ' +
        (details.overallBillingStatus === 'PAID' ? 'bg-green-100 text-green-800' :
         details.overallBillingStatus === 'OVERDUE' ? 'bg-red-100 text-red-800' :
         'bg-yellow-100 text-yellow-800');
}

function updateBillingHistory(details) {
    if (!details) return;
    // Use API data if available
    if (details.billingHistory && details.billingHistory.length > 0) {
        updateBillingHistoryFromApi(details.billingHistory);
        return;
    }
    // Fallback to dummy data
    const list = document.getElementById('billingHistoryList');
    if (!window.billingData?.billingHistory) {
        list.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No billing history available</p>';
        return;
    }
    list.innerHTML = window.billingData.billingHistory.map(bill => `
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-gray-400 mr-3"></i>
                <div>
                    <div class="text-gray-900 dark:text-white font-medium">${bill.month}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-clock mr-1"></i>Due: ${bill.dueRead}
                    </div>
                </div>
            </div>
            <div class="text-right space-y-2">
                <div class="font-semibold text-gray-900 dark:text-white">${bill.amount}</div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                    bill.status === 'PAID' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                }">
                    <i class="fas fa-${bill.status === 'PAID' ? 'check' : 'times'} mr-1"></i>${bill.status}
                </span>
            </div>
        </div>
    `).join('');
}

function updateBillingHistoryFromApi(billingHistory) {
    const list = document.getElementById('billingHistoryList');
    if (!billingHistory || billingHistory.length === 0) {
        list.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No billing history available</p>';
        return;
    }

    // Helper to format numbers safely
    const formatNum = (val, dec = 2) => {
        const n = parseFloat(val);
        return isNaN(n) ? (dec === 3 ? '0.000' : '0.00') : n.toFixed(dec);
    };

    // Helper to escape HTML entities
    const esc = (text) => {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    };

    list.innerHTML = billingHistory.map(bill => {
        const period = esc(bill.period);
        const dueDate = esc(bill.due_date || 'N/A');
        const status = esc(bill.status);
        const photoUrl = esc(bill.photo_url || '');
        const statusIcon = status === 'PAID' ? 'check' : status === 'OVERDUE' ? 'exclamation-circle' : 'clock';
        const statusClass = status === 'PAID' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
            status === 'OVERDUE' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        const photoClass = bill.has_photo ? 'text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20' : 'text-gray-300 hover:bg-gray-50 dark:text-gray-600 dark:hover:bg-gray-800';
        const photoTitle = bill.has_photo ? 'View meter reading photo' : 'No photo available';

        return `
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-gray-400 mr-3"></i>
                <div>
                    <div class="text-gray-900 dark:text-white font-medium">${period}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-clock mr-1"></i>Due: ${dueDate}
                    </div>
                    <div class="text-xs text-gray-400">
                        Consumption: ${formatNum(bill.consumption, 3)} m³
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button data-photo-url="${photoUrl}" data-has-photo="${bill.has_photo ? 'true' : 'false'}" data-period="${period}" onclick="openBillPhoto(this.dataset.photoUrl, this.dataset.hasPhoto === 'true', this.dataset.period)" class="p-2 rounded-lg transition-colors ${photoClass}" title="${photoTitle}">
                    <i class="fas fa-camera"></i>
                </button>
                <div class="text-right space-y-2">
                    <div class="font-semibold text-gray-900 dark:text-white">₱${formatNum(bill.total_amount, 2)}</div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                        <i class="fas fa-${statusIcon} mr-1"></i>${status}
                    </span>
                </div>
            </div>
        </div>`;
    }).join('');
}

async function updateBillTrendGraph(details) {
    if (!details) return;
    // Use API data if available
    if (details.monthlyTrend && details.monthlyTrend.labels?.length > 0) {
        updateBillTrendGraphFromApi(details.monthlyTrend);
        return;
    }
    const canvas = document.getElementById('monthlyTrendChart');
    if (!canvas) return;

    // Safely destroy existing chart
    if (window.monthlyTrendChart && typeof window.monthlyTrendChart.destroy === 'function') {
        window.monthlyTrendChart.destroy();
    }

    const ctx = canvas.getContext('2d');
    const trend = window.billingData?.monthlyTrend || { labels: [], data: [] };

    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded');
        return;
    }

    window.monthlyTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: trend.labels,
            datasets: [{
                label: 'Bill Amount (₱)',
                data: trend.data,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => `₱${context.parsed.y.toFixed(2)}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: (value) => '₱' + value }
                }
            }
        }
    });
}

function updateBillTrendGraphFromApi(monthlyTrend) {
    const canvas = document.getElementById('monthlyTrendChart');
    if (!canvas) return;

    // Safely destroy existing chart
    if (window.monthlyTrendChart && typeof window.monthlyTrendChart.destroy === 'function') {
        window.monthlyTrendChart.destroy();
    }

    const ctx = canvas.getContext('2d');

    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded');
        return;
    }

    window.monthlyTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyTrend.labels || [],
            datasets: [{
                label: 'Bill Amount (₱)',
                data: monthlyTrend.data || [],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => `₱${context.parsed.y.toFixed(2)}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: (value) => '₱' + value }
                }
            }
        }
    });
}

let paymentAllocations = [];

function openProcessPaymentModal() {
    const modal = document.getElementById('processPaymentModal');
    const amount = document.getElementById('currentAmount').textContent;
    const consumer = document.getElementById('customerName').textContent;
    const accountNo = document.getElementById('customerId').textContent;
    const unpaidCount = document.getElementById('unpaidMonthBills').textContent;
    
    document.getElementById('modalAmountDue').textContent = amount;
    document.getElementById('modalUnpaidCount').textContent = unpaidCount;
    document.getElementById('modalConsumerName').value = consumer;
    document.getElementById('modalAccountNo').value = accountNo;
    document.getElementById('modalPaymentAmount').value = amount.replace('₱', '');
    document.getElementById('modalPaymentDate').valueAsDate = new Date();
    document.getElementById('modalReceiptNo').value = 'RCPT-' + Date.now();
    document.getElementById('modalPaymentMethod').value = '';
    document.getElementById('modalProcessedBy').value = '';
    
    loadUnpaidBills();
    modal.classList.remove('hidden');
}

function loadUnpaidBills() {
    const connectionId = window.currentConnectionId || 0;
    const billsList = document.getElementById('modalBillsList');
    paymentAllocations = [];

    // Try to use API data first
    if (window.connectionBillingData?.billing_history) {
        const unpaidBills = window.connectionBillingData.billing_history.filter(b =>
            b.status === 'UNPAID' || b.status === 'OVERDUE'
        );

        if (unpaidBills.length === 0) {
            billsList.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No unpaid bills</p>';
            return;
        }

        billsList.innerHTML = unpaidBills.map(bill => `
            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600">
                <div class="flex-1">
                    <div class="font-medium text-gray-900 dark:text-white">Bill #${bill.bill_id} - ${bill.period}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Due: ${bill.due_date || 'N/A'} | Amount: ₱${bill.total_amount?.toFixed(2) || '0.00'}</div>
                </div>
                <input type="number"
                       id="alloc-${bill.bill_id}"
                       data-bill-id="${bill.bill_id}"
                       data-bill-amount="${bill.total_amount}"
                       placeholder="0.00"
                       min="0"
                       max="${bill.total_amount}"
                       step="0.01"
                       onchange="updateAllocation()"
                       class="w-32 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
            </div>
        `).join('');
        return;
    }

    // Fallback to dummy data
    if (!window.billingData?.waterBillHistory) {
        billsList.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No billing data available</p>';
        return;
    }

    const bills = window.billingData.waterBillHistory.filter(b =>
        b.connection_id === connectionId &&
        (b.stat_id === window.billing?.STATUSES?.ACTIVE || b.stat_id === window.billing?.STATUSES?.OVERDUE)
    );

    if (bills.length === 0) {
        billsList.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">No unpaid bills</p>';
        return;
    }

    billsList.innerHTML = bills.map(bill => `
        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600">
            <div class="flex-1">
                <div class="font-medium text-gray-900 dark:text-white">Bill #${bill.bill_id}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Due: ${bill.due_date} | Amount: ₱${bill.total_amount.toFixed(2)}</div>
            </div>
            <input type="number"
                   id="alloc-${bill.bill_id}"
                   data-bill-id="${bill.bill_id}"
                   data-bill-amount="${bill.total_amount}"
                   placeholder="0.00"
                   min="0"
                   max="${bill.total_amount}"
                   step="0.01"
                   onchange="updateAllocation()"
                   class="w-32 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
        </div>
    `).join('');
}

function updateAllocation() {
    const inputs = document.querySelectorAll('[id^="alloc-"]');
    let total = 0;
    paymentAllocations = [];
    
    inputs.forEach(input => {
        const amount = parseFloat(input.value) || 0;
        if (amount > 0) {
            total += amount;
            paymentAllocations.push({
                bill_id: parseInt(input.dataset.billId),
                amount: amount,
                target_type: window.billing.TARGET_TYPES.BILL
            });
        }
    });
    
    const received = parseFloat(document.getElementById('modalPaymentAmount').value) || 0;
    document.getElementById('modalTotalAllocated').textContent = '₱' + total.toFixed(2);
    document.getElementById('modalRemaining').textContent = '₱' + (received - total).toFixed(2);
}

function closeProcessPaymentModal() {
    document.getElementById('processPaymentModal').classList.add('hidden');
    paymentAllocations = [];
}

function submitProcessPayment() {
    const receiptNo = document.getElementById('modalReceiptNo').value;
    const amount = parseFloat(document.getElementById('modalPaymentAmount').value);
    const date = document.getElementById('modalPaymentDate').value;
    const method = document.getElementById('modalPaymentMethod').value;
    const processedBy = document.getElementById('modalProcessedBy').value;
    const totalAllocated = paymentAllocations.reduce((sum, a) => sum + a.amount, 0);
    
    if (!receiptNo || !amount || !date || !method || !processedBy) {
        showToast('Please fill all required fields', 'error');
        return;
    }
    
    if (totalAllocated > amount) {
        showToast('Total allocated amount cannot exceed payment amount', 'error');
        return;
    }
    
    if (paymentAllocations.length === 0) {
        showToast('Please allocate payment to at least one bill', 'error');
        return;
    }
    
    const payment = {
        receipt_no: receiptNo,
        amount_received: amount,
        payment_date: date,
        payment_method: method,
        processed_by: processedBy,
        allocations: paymentAllocations,
        stat_id: window.billing.STATUSES.ACTIVE
    };
    
    console.log('Payment processed:', payment);
    showToast(`Payment processed successfully! Receipt: ${receiptNo}`, 'success');
    closeProcessPaymentModal();
    
    setTimeout(() => {
        if (typeof updateConsumerView === 'function') {
            const connectionId = window.currentConnectionId || 1001;
            const details = billing.getConsumerDetails(connectionId);
            updateConsumerView(details);
        }
    }, 500);
}

function openBillPhoto(photoUrl, hasPhoto, period) {
    if (!hasPhoto) {
        return;
    }
    const modal = document.getElementById('billPhotoModal');
    const image = document.getElementById('billPhotoImage');
    const title = document.getElementById('billPhotoTitle');
    image.src = photoUrl;
    title.textContent = `Meter Reading Photo - ${period}`;
    modal.classList.remove('hidden');
}

function closeBillPhotoModal(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('billPhotoModal').classList.add('hidden');
    document.getElementById('billPhotoImage').src = '';
}

window.updateCustomerProfile = updateCustomerProfile;
window.updateBillOverview = updateBillOverview;
window.updateRecentActivities = updateRecentActivities;
window.updateWaterUsage = updateWaterUsage;
window.updateBillSummary = updateBillSummary;
window.updateBillingHistory = updateBillingHistory;
window.updateBillingHistoryFromApi = updateBillingHistoryFromApi;
window.updateBillTrendGraph = updateBillTrendGraph;
window.updateBillTrendGraphFromApi = updateBillTrendGraphFromApi;
window.openProcessPaymentModal = openProcessPaymentModal;
window.closeProcessPaymentModal = closeProcessPaymentModal;
window.submitProcessPayment = submitProcessPayment;
window.openBillPhoto = openBillPhoto;
window.closeBillPhotoModal = closeBillPhotoModal;
</script>
