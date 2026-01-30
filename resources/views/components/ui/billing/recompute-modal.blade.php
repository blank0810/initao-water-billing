<div id="recomputeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-calculator text-orange-600 mr-2"></i>Recompute Bill
            </h3>
            <button onclick="closeRecomputeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Info Note -->
        <div class="p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg mb-4">
            <div class="flex items-start gap-2">
                <i class="fas fa-info-circle text-orange-600 dark:text-orange-400 mt-0.5"></i>
                <div class="text-sm text-orange-700 dark:text-orange-300">
                    <strong>Recompute</strong> updates meter readings and recalculates bill amounts.
                    This modifies the actual bill record and is only available for <strong>open periods</strong>.
                </div>
            </div>
        </div>

        <!-- Recompute Type Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-700 mb-4">
            <button type="button" id="tabSingleBill" onclick="switchRecomputeTab('single')" class="px-4 py-2 text-sm font-medium border-b-2 border-orange-600 text-orange-600 dark:text-orange-400">
                <i class="fas fa-file-invoice mr-2"></i>Single Bill
            </button>
            <button type="button" id="tabBatchPeriod" onclick="switchRecomputeTab('batch')" class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-layer-group mr-2"></i>Entire Period
            </button>
        </div>

        <!-- Single Bill Panel -->
        <div id="panelSingleBill" class="space-y-4">
            <!-- Bill ID Lookup -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bill ID *</label>
                <div class="flex gap-2">
                    <input type="number" id="recomputeBillId" required placeholder="Enter bill ID" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <button onclick="lookupBillForRecompute()" id="lookupRecomputeBtn" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        <i class="fas fa-search" id="lookupRecomputeIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Bill Info Card (shown after lookup) -->
            <div id="recomputeBillInfo" class="hidden space-y-4">
                <!-- Consumer & Period Info -->
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Consumer:</span>
                            <div id="recomputeConsumer" class="font-medium text-gray-900 dark:text-white">-</div>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Account No:</span>
                            <div id="recomputeAccountNo" class="font-medium text-gray-900 dark:text-white">-</div>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Period:</span>
                            <div id="recomputePeriod" class="font-medium text-gray-900 dark:text-white">-</div>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Area:</span>
                            <div id="recomputeArea" class="font-medium text-gray-900 dark:text-white">-</div>
                        </div>
                    </div>
                    <div id="recomputePeriodStatus" class="mt-3"></div>
                </div>

                <!-- Current Bill Values (readonly) -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-file-invoice mr-2"></i>Current Bill Values
                    </h4>
                    <div class="grid grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Prev Reading:</span>
                            <div id="currentPrevReadingDisplay" class="font-medium text-gray-900 dark:text-white">0</div>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Curr Reading:</span>
                            <div id="currentCurrReadingDisplay" class="font-medium text-gray-900 dark:text-white">0</div>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Consumption:</span>
                            <div id="currentConsumptionDisplay" class="font-medium text-gray-900 dark:text-white">0 cu.m</div>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Amount:</span>
                            <div id="currentAmountDisplay" class="font-medium text-gray-900 dark:text-white">₱0.00</div>
                        </div>
                    </div>
                </div>

                <!-- Editable Reading Fields -->
                <div class="p-4 bg-orange-50 dark:bg-orange-900/10 border border-orange-200 dark:border-orange-700 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-edit mr-2"></i>New Reading Values
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Previous Reading</label>
                            <input type="number" id="newPrevReadingRecompute" placeholder="Leave blank to keep current" min="0" step="0.001" oninput="calculateRecomputePreview()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Reading *</label>
                            <input type="number" id="newCurrReadingRecompute" placeholder="Enter new reading" min="0" step="0.001" oninput="calculateRecomputePreview()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Calculated Preview -->
                    <div id="recomputePreview" class="hidden mt-4 p-3 bg-white dark:bg-gray-800 rounded-lg border border-orange-300 dark:border-orange-600">
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">New Consumption:</span>
                                <div id="newConsumptionPreview" class="font-bold text-orange-600 dark:text-orange-400">0 cu.m</div>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Consumption Change:</span>
                                <div id="consumptionChangePreview" class="font-semibold">-</div>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                <div id="validationStatus" class="font-semibold text-green-600 dark:text-green-400">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                    <textarea id="recomputeRemarks" rows="2" placeholder="Optional remarks for audit trail..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                </div>
            </div>
        </div>

        <!-- Batch Period Panel -->
        <div id="panelBatchPeriod" class="hidden space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Period *</label>
                <select id="recomputePeriodSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Loading periods...</option>
                </select>
            </div>

            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 mt-0.5"></i>
                    <div class="text-sm text-yellow-700 dark:text-yellow-300">
                        <strong>Warning:</strong> This will recompute ALL bills in the selected period based on their current meter readings.
                        Only bills where readings have changed will be updated.
                    </div>
                </div>
            </div>
        </div>

        <!-- Error/Success Message -->
        <div id="recomputeMessage" class="hidden mt-4 p-3 rounded-lg text-sm"></div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeRecomputeModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="submitRecompute()" id="submitRecomputeBtn" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-calculator mr-2" id="submitRecomputeIcon"></i>Recompute Bill
            </button>
        </div>
    </div>
</div>

<script>
let recomputeModalData = {
    activeTab: 'single',
    currentBill: null,
    periods: [],
    isLoading: false
};

async function openRecomputeModal() {
    document.getElementById('recomputeModal').classList.remove('hidden');
    resetRecomputeModal();
    await loadOpenPeriods();
}

function closeRecomputeModal() {
    document.getElementById('recomputeModal').classList.add('hidden');
    resetRecomputeModal();
}

function resetRecomputeModal() {
    document.getElementById('recomputeBillId').value = '';
    document.getElementById('recomputeRemarks').value = '';
    document.getElementById('newPrevReadingRecompute').value = '';
    document.getElementById('newCurrReadingRecompute').value = '';
    document.getElementById('recomputeBillInfo').classList.add('hidden');
    document.getElementById('recomputePreview').classList.add('hidden');
    hideRecomputeMessage();
    recomputeModalData.currentBill = null;
    recomputeModalData.activeTab = 'single';
    switchRecomputeTab('single');
}

function switchRecomputeTab(tab) {
    recomputeModalData.activeTab = tab;

    const tabSingle = document.getElementById('tabSingleBill');
    const tabBatch = document.getElementById('tabBatchPeriod');
    const panelSingle = document.getElementById('panelSingleBill');
    const panelBatch = document.getElementById('panelBatchPeriod');

    if (tab === 'single') {
        tabSingle.className = 'px-4 py-2 text-sm font-medium border-b-2 border-orange-600 text-orange-600 dark:text-orange-400';
        tabBatch.className = 'px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300';
        panelSingle.classList.remove('hidden');
        panelBatch.classList.add('hidden');
    } else {
        tabBatch.className = 'px-4 py-2 text-sm font-medium border-b-2 border-orange-600 text-orange-600 dark:text-orange-400';
        tabSingle.className = 'px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300';
        panelBatch.classList.remove('hidden');
        panelSingle.classList.add('hidden');
    }
}

async function loadOpenPeriods() {
    const select = document.getElementById('recomputePeriodSelect');
    select.innerHTML = '<option value="">Loading...</option>';

    try {
        const response = await fetch('/water-bills/billing-periods');
        if (!response.ok) {
            throw new Error('Failed to fetch periods');
        }
        const result = await response.json();

        if (result.success && result.data) {
            const openPeriods = result.data.filter(p => !p.is_closed);
            recomputeModalData.periods = openPeriods;

            if (openPeriods.length > 0) {
                select.innerHTML = '<option value="">Select a period...</option>' +
                    openPeriods.map(p => `<option value="${p.per_id}">${p.per_name}</option>`).join('');
            } else {
                select.innerHTML = '<option value="">No open periods available</option>';
            }
        } else {
            select.innerHTML = '<option value="">No open periods available</option>';
        }
    } catch (error) {
        console.error('Failed to load periods:', error);
        select.innerHTML = '<option value="">Failed to load periods</option>';
    }
}

async function lookupBillForRecompute() {
    const billId = document.getElementById('recomputeBillId').value;

    if (!billId) {
        showRecomputeMessage('Please enter a bill ID', 'error');
        return;
    }

    const btn = document.getElementById('lookupRecomputeBtn');
    const icon = document.getElementById('lookupRecomputeIcon');
    btn.disabled = true;
    icon.className = 'fas fa-spinner fa-spin';
    hideRecomputeMessage();

    try {
        const response = await fetch(`/bill-adjustments/lookup/${billId}`);
        const result = await response.json();

        if (result.success && result.data) {
            recomputeModalData.currentBill = result.data;

            // Populate consumer info
            document.getElementById('recomputeConsumer').textContent = result.data.consumer_name || 'N/A';
            document.getElementById('recomputeAccountNo').textContent = result.data.account_no || 'N/A';
            document.getElementById('recomputePeriod').textContent = result.data.period_name || 'N/A';
            document.getElementById('recomputeArea').textContent = result.data.area_desc || 'N/A';

            // Populate current bill values
            const prevReading = parseFloat(result.data.prev_reading || 0);
            const currReading = parseFloat(result.data.curr_reading || 0);
            const consumption = parseFloat(result.data.consumption || 0);
            const amount = parseFloat(result.data.water_amount || 0);

            document.getElementById('currentPrevReadingDisplay').textContent = prevReading.toFixed(3);
            document.getElementById('currentCurrReadingDisplay').textContent = currReading.toFixed(3);
            document.getElementById('currentConsumptionDisplay').textContent = consumption.toFixed(3) + ' cu.m';
            document.getElementById('currentAmountDisplay').textContent = '₱' + amount.toFixed(2);

            // Set placeholders for new readings
            document.getElementById('newPrevReadingRecompute').placeholder = prevReading.toFixed(3) + ' (current)';
            document.getElementById('newCurrReadingRecompute').placeholder = currReading.toFixed(3) + ' (current)';

            // Check period status
            const periodStatus = document.getElementById('recomputePeriodStatus');
            const period = recomputeModalData.periods.find(p => p.per_id == result.data.period_id);
            if (period && !period.is_closed) {
                periodStatus.innerHTML = '<span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200"><i class="fas fa-check-circle mr-1"></i>Period is open - recompute allowed</span>';
            } else if (period && period.is_closed) {
                periodStatus.innerHTML = '<span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200"><i class="fas fa-lock mr-1"></i>Period is closed - recompute not allowed</span>';
            } else {
                periodStatus.innerHTML = '<span class="text-sm text-blue-600 dark:text-blue-400"><i class="fas fa-info-circle mr-1"></i>Period status will be verified on recompute</span>';
            }

            document.getElementById('recomputeBillInfo').classList.remove('hidden');
        } else {
            document.getElementById('recomputeBillInfo').classList.add('hidden');
            recomputeModalData.currentBill = null;
            showRecomputeMessage(result.message || 'Bill not found', 'error');
        }
    } catch (error) {
        console.error('Failed to lookup bill:', error);
        document.getElementById('recomputeBillInfo').classList.add('hidden');
        recomputeModalData.currentBill = null;
        showRecomputeMessage('Failed to lookup bill. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        icon.className = 'fas fa-search';
    }
}

function calculateRecomputePreview() {
    if (!recomputeModalData.currentBill) {
        document.getElementById('recomputePreview').classList.add('hidden');
        return;
    }

    const oldPrevReading = parseFloat(recomputeModalData.currentBill.prev_reading) || 0;
    const oldCurrReading = parseFloat(recomputeModalData.currentBill.curr_reading) || 0;
    const oldConsumption = parseFloat(recomputeModalData.currentBill.consumption) || 0;

    const newPrevInput = document.getElementById('newPrevReadingRecompute').value;
    const newCurrInput = document.getElementById('newCurrReadingRecompute').value;

    // If nothing entered, hide preview
    if (!newPrevInput && !newCurrInput) {
        document.getElementById('recomputePreview').classList.add('hidden');
        return;
    }

    const newPrevReading = newPrevInput ? parseFloat(newPrevInput) : oldPrevReading;
    const newCurrReading = newCurrInput ? parseFloat(newCurrInput) : oldCurrReading;

    // Validation
    const validationStatus = document.getElementById('validationStatus');
    if (newCurrReading < newPrevReading) {
        validationStatus.textContent = 'Invalid: Current < Previous';
        validationStatus.className = 'font-semibold text-red-600 dark:text-red-400';
        document.getElementById('recomputePreview').classList.remove('hidden');
        return;
    }

    const newConsumption = newCurrReading - newPrevReading;
    const consumptionDiff = newConsumption - oldConsumption;

    // Update preview
    document.getElementById('newConsumptionPreview').textContent = newConsumption.toFixed(3) + ' cu.m';

    const changeEl = document.getElementById('consumptionChangePreview');
    if (consumptionDiff > 0.001) {
        changeEl.textContent = '+' + consumptionDiff.toFixed(3) + ' cu.m';
        changeEl.className = 'font-semibold text-red-600 dark:text-red-400';
    } else if (consumptionDiff < -0.001) {
        changeEl.textContent = consumptionDiff.toFixed(3) + ' cu.m';
        changeEl.className = 'font-semibold text-green-600 dark:text-green-400';
    } else {
        changeEl.textContent = 'No change';
        changeEl.className = 'font-semibold text-gray-600 dark:text-gray-400';
    }

    validationStatus.textContent = 'Valid';
    validationStatus.className = 'font-semibold text-green-600 dark:text-green-400';

    document.getElementById('recomputePreview').classList.remove('hidden');
}

async function submitRecompute() {
    const btn = document.getElementById('submitRecomputeBtn');
    const icon = document.getElementById('submitRecomputeIcon');
    btn.disabled = true;
    icon.className = 'fas fa-spinner fa-spin mr-2';
    hideRecomputeMessage();

    try {
        let url, payload;

        if (recomputeModalData.activeTab === 'single') {
            if (!recomputeModalData.currentBill) {
                showRecomputeMessage('Please lookup a bill first', 'error');
                btn.disabled = false;
                icon.className = 'fas fa-calculator mr-2';
                return;
            }

            const newPrevInput = document.getElementById('newPrevReadingRecompute').value;
            const newCurrInput = document.getElementById('newCurrReadingRecompute').value;

            // Validate at least one reading is provided or it's a simple recompute
            const oldPrevReading = parseFloat(recomputeModalData.currentBill.prev_reading) || 0;
            const oldCurrReading = parseFloat(recomputeModalData.currentBill.curr_reading) || 0;

            const newPrevReading = newPrevInput ? parseFloat(newPrevInput) : oldPrevReading;
            const newCurrReading = newCurrInput ? parseFloat(newCurrInput) : oldCurrReading;

            // Validate readings
            if (newCurrReading < newPrevReading) {
                showRecomputeMessage('Current reading cannot be less than previous reading', 'error');
                btn.disabled = false;
                icon.className = 'fas fa-calculator mr-2';
                return;
            }

            url = '/bill-adjustments/recompute';
            payload = {
                bill_id: parseInt(document.getElementById('recomputeBillId').value),
                remarks: document.getElementById('recomputeRemarks').value || null
            };

            // Include new readings if changed
            if (newPrevInput && Math.abs(newPrevReading - oldPrevReading) > 0.001) {
                payload.new_prev_reading = newPrevReading;
            }
            if (newCurrInput && Math.abs(newCurrReading - oldCurrReading) > 0.001) {
                payload.new_curr_reading = newCurrReading;
            }

        } else {
            const periodId = document.getElementById('recomputePeriodSelect').value;
            if (!periodId) {
                showRecomputeMessage('Please select a period', 'error');
                btn.disabled = false;
                icon.className = 'fas fa-calculator mr-2';
                return;
            }

            url = '/bill-adjustments/recompute-period';
            payload = {
                period_id: parseInt(periodId)
            };
        }

        // Validate CSRF token presence
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            showRecomputeMessage('Session error. Please refresh the page.', 'error');
            btn.disabled = false;
            icon.className = 'fas fa-calculator mr-2';
            return;
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });

        // Handle 419 CSRF token mismatch
        if (response.status === 419) {
            showRecomputeMessage('Session expired. Please refresh the page.', 'error');
            btn.disabled = false;
            icon.className = 'fas fa-calculator mr-2';
            return;
        }

        // Check response status before parsing JSON
        if (!response.ok) {
            const errorText = await response.text();
            let errorMessage = 'Recomputation failed';
            try {
                const errorJson = JSON.parse(errorText);
                errorMessage = errorJson.message || errorMessage;
            } catch {
                // Response was not JSON (e.g., HTML error page)
            }
            showRecomputeMessage(errorMessage, 'error');
            btn.disabled = false;
            icon.className = 'fas fa-calculator mr-2';
            return;
        }

        const result = await response.json();

        if (result.success) {
            showRecomputeMessage(result.message, 'success');

            // Dispatch event for other components to refresh
            document.dispatchEvent(new CustomEvent('bill-recomputed', { detail: result.data }));

            // Close modal after brief delay
            setTimeout(() => {
                closeRecomputeModal();
                if (typeof showToast === 'function') {
                    showToast(result.message, 'success');
                }
            }, 1500);
        } else {
            showRecomputeMessage(result.message || 'Recomputation failed', 'error');
        }
    } catch (error) {
        console.error('Failed to recompute:', error);
        showRecomputeMessage('Failed to recompute. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        icon.className = 'fas fa-calculator mr-2';
    }
}

function showRecomputeMessage(message, type) {
    const msgDiv = document.getElementById('recomputeMessage');
    msgDiv.textContent = message;
    msgDiv.className = type === 'error'
        ? 'mt-4 p-3 rounded-lg text-sm bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800'
        : 'mt-4 p-3 rounded-lg text-sm bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800';
    msgDiv.classList.remove('hidden');
}

function hideRecomputeMessage() {
    const msgDiv = document.getElementById('recomputeMessage');
    msgDiv.classList.add('hidden');
    msgDiv.textContent = '';
}

// Allow Enter key to trigger lookup
document.addEventListener('DOMContentLoaded', function() {
    const billIdInput = document.getElementById('recomputeBillId');
    if (billIdInput) {
        billIdInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                lookupBillForRecompute();
            }
        });
    }
});

// Expose functions globally
window.openRecomputeModal = openRecomputeModal;
window.closeRecomputeModal = closeRecomputeModal;
window.switchRecomputeTab = switchRecomputeTab;
window.lookupBillForRecompute = lookupBillForRecompute;
window.calculateRecomputePreview = calculateRecomputePreview;
window.submitRecompute = submitRecompute;
</script>
