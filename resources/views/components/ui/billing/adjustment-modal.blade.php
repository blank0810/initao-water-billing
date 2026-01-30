<div id="addAdjustmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add Bill Adjustment</h3>
            <button onclick="closeAddAdjustmentModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-4">
            <!-- Bill ID Lookup -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bill ID *</label>
                    <div class="flex gap-2">
                        <input type="number" id="adjustmentBillId" required placeholder="Enter bill ID" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <button onclick="loadBillInfo()" id="lookupBillBtn" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                            <i class="fas fa-search" id="lookupIcon"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consumer</label>
                    <input type="text" id="adjustmentConsumer" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>

            <!-- Bill Info Card -->
            <div id="billInfoCard" class="hidden p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="grid grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Current Amount:</span>
                        <div id="billCurrentAmount" class="font-semibold text-gray-900 dark:text-white">₱0.00</div>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Consumption:</span>
                        <div id="billConsumption" class="font-semibold text-gray-900 dark:text-white">0 cu.m</div>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Due Date:</span>
                        <div id="billDueDate" class="font-semibold text-gray-900 dark:text-white">--</div>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                        <div id="billStatus" class="font-semibold text-gray-900 dark:text-white">--</div>
                    </div>
                </div>
            </div>

            <!-- Error message display -->
            <div id="adjustmentError" class="hidden p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-600 dark:text-red-400">
            </div>

            <!-- Adjustment Type Tabs -->
            <div id="adjustmentTabs" class="hidden">
                <div class="flex border-b border-gray-200 dark:border-gray-700 mb-4">
                    <button type="button" id="tabConsumption" onclick="switchAdjustmentTab('consumption')" class="px-4 py-2 text-sm font-medium border-b-2 border-blue-600 text-blue-600 dark:text-blue-400">
                        <i class="fas fa-tachometer-alt mr-2"></i>Consumption Adjustment
                    </button>
                    <button type="button" id="tabAmount" onclick="switchAdjustmentTab('amount')" class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <i class="fas fa-peso-sign mr-2"></i>Amount Adjustment
                    </button>
                </div>

                <!-- Consumption Adjustment Panel -->
                <div id="panelConsumption" class="space-y-4">
                    <!-- Ledger-Only Info Note -->
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                            <div class="text-sm text-blue-700 dark:text-blue-300">
                                <strong>Ledger Adjustment:</strong> This creates a ledger entry for the difference without modifying the original bill amount.
                                To modify the actual bill record, use <strong>Recompute Bill</strong> (only available for open periods).
                            </div>
                        </div>
                    </div>

                    <!-- Current Readings (readonly) -->
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Readings</h4>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Previous:</span>
                                <span id="currentPrevReading" class="font-medium text-gray-900 dark:text-white ml-1">0</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Present:</span>
                                <span id="currentCurrReading" class="font-medium text-gray-900 dark:text-white ml-1">0</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Consumption:</span>
                                <span id="currentConsumption" class="font-medium text-gray-900 dark:text-white ml-1">0 cu.m</span>
                            </div>
                        </div>
                    </div>

                    <!-- New Readings Input -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Previous Reading</label>
                            <input type="number" id="newPrevReading" placeholder="Leave blank to keep current" min="0" step="0.001" oninput="calculateNewConsumption()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Present Reading *</label>
                            <input type="number" id="newCurrReading" required placeholder="Enter new present reading" min="0" step="0.001" oninput="calculateNewConsumption()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Adjustment Type for Consumption -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adjustment Type *</label>
                        <select id="consumptionAdjustmentType" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Select type...</option>
                        </select>
                    </div>

                    <!-- New Consumption Preview -->
                    <div id="consumptionPreview" class="hidden p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">New Consumption:</span>
                                <span id="newConsumptionValue" class="ml-2 text-lg font-bold text-yellow-600 dark:text-yellow-400">0 cu.m</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Change:</span>
                                <span id="consumptionChange" class="ml-2 font-semibold"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Remarks for Consumption -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks *</label>
                        <textarea id="consumptionRemarks" rows="2" required placeholder="Enter reason for consumption adjustment..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                    </div>
                </div>

                <!-- Amount Adjustment Panel -->
                <div id="panelAmount" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adjustment Type *</label>
                        <select id="adjustmentType" required onchange="updateAdjustmentPreview()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Select type...</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount *</label>
                            <input type="number" id="adjustmentAmount" required placeholder="0.00" min="0.01" step="0.01" oninput="updateAdjustmentPreview()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Direction</label>
                            <input type="text" id="adjustmentDirection" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <div id="adjustmentPreview" class="hidden p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">New Total Amount:</span>
                            <span id="newTotalAmount" class="text-xl font-bold text-yellow-600 dark:text-yellow-400">₱0.00</span>
                        </div>
                    </div>

                    <!-- Remarks for Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks *</label>
                        <textarea id="adjustmentRemarks" rows="2" required placeholder="Enter reason for adjustment..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeAddAdjustmentModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="submitAdjustment()" id="submitAdjustmentBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-check mr-2" id="submitIcon"></i>Add Adjustment
            </button>
        </div>
    </div>
</div>

<script>
// Store loaded data for the modal
let adjustmentModalData = {
    adjustmentTypes: [],
    currentBill: null,
    activeTab: 'consumption',
    isLoading: false
};

async function openAddAdjustmentModal() {
    document.getElementById('addAdjustmentModal').classList.remove('hidden');
    document.getElementById('billInfoCard').classList.add('hidden');
    document.getElementById('adjustmentTabs').classList.add('hidden');
    document.getElementById('adjustmentPreview').classList.add('hidden');
    document.getElementById('consumptionPreview').classList.add('hidden');
    hideAdjustmentError();

    // Reset to consumption tab
    adjustmentModalData.activeTab = 'consumption';
    switchAdjustmentTab('consumption');

    // Load adjustment types from API
    await loadAdjustmentTypes();
}

async function loadAdjustmentTypes() {
    const select = document.getElementById('adjustmentType');
    const consumptionSelect = document.getElementById('consumptionAdjustmentType');

    select.innerHTML = '<option value="">Loading...</option>';
    consumptionSelect.innerHTML = '<option value="">Loading...</option>';
    select.disabled = true;
    consumptionSelect.disabled = true;

    try {
        const response = await fetch('/bill-adjustments/types');
        const result = await response.json();

        if (result.success && result.data) {
            adjustmentModalData.adjustmentTypes = result.data;
            const options = '<option value="">Select type...</option>' +
                result.data.map(t => `<option value="${t.bill_adjustment_type_id}" data-direction="${t.direction}">${t.name} (${t.direction === 'debit' ? 'Debit' : 'Credit'})</option>`).join('');

            select.innerHTML = options;
            consumptionSelect.innerHTML = options;
        } else {
            select.innerHTML = '<option value="">Failed to load types</option>';
            consumptionSelect.innerHTML = '<option value="">Failed to load types</option>';
            showAdjustmentError('Failed to load adjustment types');
        }
    } catch (error) {
        console.error('Failed to load adjustment types:', error);
        select.innerHTML = '<option value="">Failed to load types</option>';
        consumptionSelect.innerHTML = '<option value="">Failed to load types</option>';
        showAdjustmentError('Failed to load adjustment types');
    } finally {
        select.disabled = false;
        consumptionSelect.disabled = false;
    }
}

async function loadBillInfo() {
    const billId = document.getElementById('adjustmentBillId').value;

    if (!billId) {
        showAdjustmentError('Please enter a bill ID');
        return;
    }

    // Set loading state
    const lookupBtn = document.getElementById('lookupBillBtn');
    const lookupIcon = document.getElementById('lookupIcon');
    lookupBtn.disabled = true;
    lookupIcon.className = 'fas fa-spinner fa-spin';
    hideAdjustmentError();

    try {
        const response = await fetch(`/bill-adjustments/lookup/${billId}`);
        const result = await response.json();

        if (result.success && result.data) {
            adjustmentModalData.currentBill = result.data;

            // Update consumer name
            document.getElementById('adjustmentConsumer').value = result.data.consumer_name || 'N/A';

            // Update bill info card
            document.getElementById('billCurrentAmount').textContent = '₱' + parseFloat(result.data.total_amount).toFixed(2);
            document.getElementById('billConsumption').textContent = parseFloat(result.data.consumption).toFixed(3) + ' cu.m';
            document.getElementById('billDueDate').textContent = result.data.due_date || '--';
            document.getElementById('billStatus').textContent = result.data.status || '--';
            document.getElementById('billInfoCard').classList.remove('hidden');

            // Update current readings display
            document.getElementById('currentPrevReading').textContent = parseFloat(result.data.prev_reading).toFixed(3);
            document.getElementById('currentCurrReading').textContent = parseFloat(result.data.curr_reading).toFixed(3);
            document.getElementById('currentConsumption').textContent = parseFloat(result.data.consumption).toFixed(3) + ' cu.m';

            // Pre-fill new readings with current values
            document.getElementById('newPrevReading').placeholder = result.data.prev_reading + ' (current)';
            document.getElementById('newCurrReading').placeholder = result.data.curr_reading + ' (current)';

            // Show adjustment tabs
            document.getElementById('adjustmentTabs').classList.remove('hidden');

            // Update previews if data already entered
            calculateNewConsumption();
            updateAdjustmentPreview();
        } else {
            document.getElementById('billInfoCard').classList.add('hidden');
            document.getElementById('adjustmentTabs').classList.add('hidden');
            document.getElementById('adjustmentConsumer').value = '';
            adjustmentModalData.currentBill = null;
            showAdjustmentError(result.message || 'Bill not found');
        }
    } catch (error) {
        console.error('Failed to lookup bill:', error);
        document.getElementById('billInfoCard').classList.add('hidden');
        document.getElementById('adjustmentTabs').classList.add('hidden');
        document.getElementById('adjustmentConsumer').value = '';
        adjustmentModalData.currentBill = null;
        showAdjustmentError('Failed to lookup bill. Please try again.');
    } finally {
        lookupBtn.disabled = false;
        lookupIcon.className = 'fas fa-search';
    }
}

function switchAdjustmentTab(tab) {
    adjustmentModalData.activeTab = tab;

    // Update tab styles
    const tabConsumption = document.getElementById('tabConsumption');
    const tabAmount = document.getElementById('tabAmount');
    const panelConsumption = document.getElementById('panelConsumption');
    const panelAmount = document.getElementById('panelAmount');

    if (tab === 'consumption') {
        tabConsumption.className = 'px-4 py-2 text-sm font-medium border-b-2 border-blue-600 text-blue-600 dark:text-blue-400';
        tabAmount.className = 'px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300';
        panelConsumption.classList.remove('hidden');
        panelAmount.classList.add('hidden');
    } else {
        tabAmount.className = 'px-4 py-2 text-sm font-medium border-b-2 border-blue-600 text-blue-600 dark:text-blue-400';
        tabConsumption.className = 'px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300';
        panelAmount.classList.remove('hidden');
        panelConsumption.classList.add('hidden');
    }
}

function calculateNewConsumption() {
    if (!adjustmentModalData.currentBill) {
        document.getElementById('consumptionPreview').classList.add('hidden');
        return;
    }

    const oldPrevReading = parseFloat(adjustmentModalData.currentBill.prev_reading) || 0;
    const oldCurrReading = parseFloat(adjustmentModalData.currentBill.curr_reading) || 0;
    const oldConsumption = parseFloat(adjustmentModalData.currentBill.consumption) || 0;

    const newPrevReading = parseFloat(document.getElementById('newPrevReading').value) || oldPrevReading;
    const newCurrReading = parseFloat(document.getElementById('newCurrReading').value);

    if (isNaN(newCurrReading) || newCurrReading === '') {
        document.getElementById('consumptionPreview').classList.add('hidden');
        return;
    }

    // Validate readings
    if (newCurrReading < newPrevReading) {
        document.getElementById('consumptionPreview').classList.add('hidden');
        showAdjustmentError('Present reading cannot be less than previous reading');
        return;
    }
    hideAdjustmentError();

    const newConsumption = newCurrReading - newPrevReading;
    const consumptionDiff = newConsumption - oldConsumption;

    // Update preview
    document.getElementById('newConsumptionValue').textContent = newConsumption.toFixed(3) + ' cu.m';

    const changeEl = document.getElementById('consumptionChange');
    if (consumptionDiff > 0) {
        changeEl.textContent = '+' + consumptionDiff.toFixed(3) + ' cu.m';
        changeEl.className = 'ml-2 font-semibold text-red-600 dark:text-red-400';
    } else if (consumptionDiff < 0) {
        changeEl.textContent = consumptionDiff.toFixed(3) + ' cu.m';
        changeEl.className = 'ml-2 font-semibold text-green-600 dark:text-green-400';
    } else {
        changeEl.textContent = 'No change';
        changeEl.className = 'ml-2 font-semibold text-gray-600 dark:text-gray-400';
    }

    document.getElementById('consumptionPreview').classList.remove('hidden');
}

function updateAdjustmentPreview() {
    const typeSelect = document.getElementById('adjustmentType');
    const amount = parseFloat(document.getElementById('adjustmentAmount').value) || 0;

    if (!adjustmentModalData.currentBill || !typeSelect.value || !amount) {
        document.getElementById('adjustmentPreview').classList.add('hidden');
        document.getElementById('adjustmentDirection').value = '';
        return;
    }

    const direction = typeSelect.options[typeSelect.selectedIndex].dataset.direction;
    document.getElementById('adjustmentDirection').value = direction === 'debit' ? 'Debit (+)' : 'Credit (-)';

    const currentAmount = parseFloat(adjustmentModalData.currentBill.total_amount) || 0;
    const newTotal = direction === 'debit' ? currentAmount + amount : currentAmount - amount;

    document.getElementById('newTotalAmount').textContent = '₱' + newTotal.toFixed(2);
    document.getElementById('newTotalAmount').className = newTotal < 0
        ? 'text-xl font-bold text-red-600 dark:text-red-400'
        : 'text-xl font-bold text-yellow-600 dark:text-yellow-400';
    document.getElementById('adjustmentPreview').classList.remove('hidden');
}

function closeAddAdjustmentModal() {
    document.getElementById('addAdjustmentModal').classList.add('hidden');

    // Reset all fields
    document.getElementById('adjustmentBillId').value = '';
    document.getElementById('adjustmentConsumer').value = '';
    document.getElementById('adjustmentType').value = '';
    document.getElementById('adjustmentAmount').value = '';
    document.getElementById('adjustmentRemarks').value = '';
    document.getElementById('adjustmentDirection').value = '';
    document.getElementById('newPrevReading').value = '';
    document.getElementById('newCurrReading').value = '';
    document.getElementById('consumptionRemarks').value = '';
    document.getElementById('consumptionAdjustmentType').value = '';

    // Hide cards and previews
    document.getElementById('billInfoCard').classList.add('hidden');
    document.getElementById('adjustmentTabs').classList.add('hidden');
    document.getElementById('adjustmentPreview').classList.add('hidden');
    document.getElementById('consumptionPreview').classList.add('hidden');

    hideAdjustmentError();
    adjustmentModalData.currentBill = null;
}

async function submitAdjustment() {
    if (!adjustmentModalData.currentBill) {
        showAdjustmentError('Please lookup the bill first by clicking the search button');
        return;
    }

    const billId = document.getElementById('adjustmentBillId').value;

    // Determine which type of adjustment to submit
    if (adjustmentModalData.activeTab === 'consumption') {
        await submitConsumptionAdjustment(billId);
    } else {
        await submitAmountAdjustment(billId);
    }
}

async function submitConsumptionAdjustment(billId) {
    const newCurrReading = document.getElementById('newCurrReading').value;
    const newPrevReading = document.getElementById('newPrevReading').value;
    const adjustmentTypeId = document.getElementById('consumptionAdjustmentType').value;
    const remarks = document.getElementById('consumptionRemarks').value;

    // Validation
    if (!newCurrReading) {
        showAdjustmentError('Please enter the new present reading');
        return;
    }

    if (!adjustmentTypeId) {
        showAdjustmentError('Please select an adjustment type');
        return;
    }

    if (!remarks) {
        showAdjustmentError('Please enter remarks for the adjustment');
        return;
    }

    // Validate readings
    const prevReading = newPrevReading ? parseFloat(newPrevReading) : parseFloat(adjustmentModalData.currentBill.prev_reading);
    const currReading = parseFloat(newCurrReading);

    if (currReading < prevReading) {
        showAdjustmentError('Present reading cannot be less than previous reading');
        return;
    }

    // Set loading state
    const submitBtn = document.getElementById('submitAdjustmentBtn');
    const submitIcon = document.getElementById('submitIcon');
    submitBtn.disabled = true;
    submitIcon.className = 'fas fa-spinner fa-spin mr-2';
    hideAdjustmentError();

    try {
        const payload = {
            bill_id: parseInt(billId),
            new_curr_reading: currReading,
            bill_adjustment_type_id: parseInt(adjustmentTypeId),
            remarks: remarks
        };

        // Only include new_prev_reading if explicitly changed
        if (newPrevReading) {
            payload.new_prev_reading = parseFloat(newPrevReading);
        }

        const response = await fetch('/bill-adjustments/consumption', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            showToast('Consumption adjustment applied successfully!', 'success');
            closeAddAdjustmentModal();
            document.dispatchEvent(new CustomEvent('adjustment-created'));
        } else {
            showAdjustmentError(result.message || 'Failed to apply consumption adjustment');
        }
    } catch (error) {
        console.error('Failed to submit consumption adjustment:', error);
        showAdjustmentError('Failed to submit adjustment. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitIcon.className = 'fas fa-check mr-2';
    }
}

async function submitAmountAdjustment(billId) {
    const type = document.getElementById('adjustmentType').value;
    const amount = document.getElementById('adjustmentAmount').value;
    const remarks = document.getElementById('adjustmentRemarks').value;

    // Validation
    if (!type || !amount || !remarks) {
        showAdjustmentError('Please fill all required fields');
        return;
    }

    if (parseFloat(amount) <= 0) {
        showAdjustmentError('Amount must be greater than 0');
        return;
    }

    // Set loading state
    const submitBtn = document.getElementById('submitAdjustmentBtn');
    const submitIcon = document.getElementById('submitIcon');
    submitBtn.disabled = true;
    submitIcon.className = 'fas fa-spinner fa-spin mr-2';
    hideAdjustmentError();

    try {
        const response = await fetch('/bill-adjustments/amount', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                bill_id: parseInt(billId),
                bill_adjustment_type_id: parseInt(type),
                amount: parseFloat(amount),
                remarks: remarks
            })
        });

        const result = await response.json();

        if (result.success) {
            showToast('Amount adjustment added successfully!', 'success');
            closeAddAdjustmentModal();
            document.dispatchEvent(new CustomEvent('adjustment-created'));
        } else {
            showAdjustmentError(result.message || 'Failed to create adjustment');
        }
    } catch (error) {
        console.error('Failed to submit amount adjustment:', error);
        showAdjustmentError('Failed to submit adjustment. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitIcon.className = 'fas fa-check mr-2';
    }
}

function showAdjustmentError(message) {
    const errorDiv = document.getElementById('adjustmentError');
    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');
}

function hideAdjustmentError() {
    const errorDiv = document.getElementById('adjustmentError');
    errorDiv.classList.add('hidden');
    errorDiv.textContent = '';
}

// Allow Enter key to trigger bill lookup
document.addEventListener('DOMContentLoaded', function() {
    const billIdInput = document.getElementById('adjustmentBillId');
    if (billIdInput) {
        billIdInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                loadBillInfo();
            }
        });
    }
});

// Expose functions globally
window.loadBillInfo = loadBillInfo;
window.updateAdjustmentPreview = updateAdjustmentPreview;
window.calculateNewConsumption = calculateNewConsumption;
window.switchAdjustmentTab = switchAdjustmentTab;
window.openAddAdjustmentModal = openAddAdjustmentModal;
window.closeAddAdjustmentModal = closeAddAdjustmentModal;
window.submitAdjustment = submitAdjustment;
</script>
