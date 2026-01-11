<!-- Complete Connection Modal -->
<div id="completeConnectionModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <i class="fas fa-plug text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Complete Service Connection</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Finalize connection and assign meter</p>
                </div>
            </div>
            <button onclick="closeCompleteConnectionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded">
                <p class="text-sm text-green-800 dark:text-green-200 font-medium">
                    <i class="fas fa-info-circle mr-2"></i>
                    This will create the service connection and mark the application as complete.
                </p>
            </div>

            <!-- Application Info -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-file-alt mr-2"></i>Application Details
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Application #:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="completeAppNumber">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="completeCustomerName">-</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-600 dark:text-gray-400">Address:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="completeAddress">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Scheduled Date:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="completeScheduledDate">-</span>
                    </div>
                </div>
            </div>

            <!-- Connection Setup Form -->
            <div class="space-y-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-cog mr-2"></i>Connection Setup
                </h4>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Account Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Account Type <span class="text-red-500">*</span>
                        </label>
                        <select id="completeAccountType"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            required>
                            <option value="">Loading account types...</option>
                        </select>
                    </div>

                    <!-- Rate -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rate Classification <span class="text-red-500">*</span>
                        </label>
                        <select id="completeRate"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            required>
                            <option value="">Loading rates...</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Meter Assignment -->
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 space-y-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i>Meter Information
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Enter the meter details from the customer's purchased meter
                </p>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Meter Serial -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Meter Serial No. <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="completeMeterSerial" placeholder="e.g., MTR-2026-001"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-mono"
                            required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Serial number from meter label</p>
                    </div>

                    <!-- Meter Brand -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Meter Brand <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="completeMeterBrand" placeholder="e.g., Neptune, Sensus"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Brand/manufacturer of the meter</p>
                    </div>

                    <!-- Initial Reading -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Initial Reading <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="completeInitialReading" step="0.001" min="0" value="0.000"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Current meter reading at installation (usually 0.000 for new meters)</p>
                    </div>
                </div>
            </div>

            <input type="hidden" id="completeApplicationId" value="">
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <button onclick="closeCompleteConnectionModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="submitCompleteConnection()" id="completeSubmitBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-plug mr-2"></i>Complete Connection
            </button>
        </div>
    </div>
</div>

<script>
let accountTypes = [];
let waterRates = [];

async function openCompleteConnectionModal(applicationId, appNumber, customerName, address, scheduledDate) {
    document.getElementById('completeApplicationId').value = applicationId;
    document.getElementById('completeAppNumber').textContent = appNumber;
    document.getElementById('completeCustomerName').textContent = customerName;
    document.getElementById('completeAddress').textContent = address;
    document.getElementById('completeScheduledDate').textContent = scheduledDate;

    // Reset form
    document.getElementById('completeAccountType').value = '';
    document.getElementById('completeRate').value = '';
    document.getElementById('completeMeterSerial').value = '';
    document.getElementById('completeMeterBrand').value = '';
    document.getElementById('completeInitialReading').value = '0.000';

    document.getElementById('completeConnectionModal').classList.remove('hidden');

    // Load dropdown data
    await Promise.all([
        loadAccountTypes(),
        loadWaterRates()
    ]);
}

function closeCompleteConnectionModal() {
    document.getElementById('completeConnectionModal').classList.add('hidden');
}

async function loadAccountTypes() {
    const select = document.getElementById('completeAccountType');
    select.innerHTML = '<option value="">Loading...</option>';

    try {
        const response = await fetch('/customer/service-connection/account-types', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();

        if (data.success) {
            accountTypes = data.data;
            select.innerHTML = '<option value="">Select Account Type</option>';

            accountTypes.forEach(type => {
                const option = document.createElement('option');
                option.value = type.id;
                option.textContent = type.description;
                select.appendChild(option);
            });
        }
    } catch (error) {
        select.innerHTML = '<option value="">Error loading account types</option>';
        console.error('Error loading account types:', error);
    }
}

async function loadWaterRates() {
    const select = document.getElementById('completeRate');
    select.innerHTML = '<option value="">Loading...</option>';

    try {
        const response = await fetch('/customer/service-connection/water-rates', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();

        if (data.success) {
            waterRates = data.data;
            select.innerHTML = '<option value="">Select Rate</option>';

            waterRates.forEach(rate => {
                const option = document.createElement('option');
                option.value = rate.id;
                option.textContent = rate.description + (rate.rate ? ` (₱${parseFloat(rate.rate).toFixed(2)}/cu.m)` : '');
                select.appendChild(option);
            });
        }
    } catch (error) {
        select.innerHTML = '<option value="">Error loading rates</option>';
        console.error('Error loading rates:', error);
    }
}

async function submitCompleteConnection() {
    const applicationId = document.getElementById('completeApplicationId').value;
    const accountTypeId = document.getElementById('completeAccountType').value;
    const rateId = document.getElementById('completeRate').value;
    const meterSerial = document.getElementById('completeMeterSerial').value.trim();
    const meterBrand = document.getElementById('completeMeterBrand').value.trim();
    const initialReading = document.getElementById('completeInitialReading').value;
    const btn = document.getElementById('completeSubmitBtn');

    // Validation
    if (!accountTypeId) {
        alert('Please select an account type');
        return;
    }
    if (!rateId) {
        alert('Please select a rate classification');
        return;
    }
    if (!meterSerial) {
        alert('Please enter the meter serial number');
        return;
    }
    if (!meterBrand) {
        alert('Please enter the meter brand');
        return;
    }
    if (initialReading === '' || parseFloat(initialReading) < 0) {
        alert('Please enter a valid initial reading');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

    try {
        const response = await fetch('/customer/service-connection/complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                application_id: applicationId,
                account_type_id: parseInt(accountTypeId),
                rate_id: parseInt(rateId),
                meter_serial: meterSerial,
                meter_brand: meterBrand,
                install_read: parseFloat(initialReading)
            })
        });

        const data = await response.json();

        if (data.success) {
            closeCompleteConnectionModal();
            if (window.showToast) {
                window.showToast('Service connection completed successfully!', 'success');
            }
            // Redirect to connection details or reload
            if (data.data && data.data.connection_id) {
                window.location.href = `/customer/service-connection/${data.data.connection_id}`;
            } else {
                location.reload();
            }
        } else {
            throw new Error(data.message || 'Connection completion failed');
        }
    } catch (error) {
        alert('Error: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plug mr-2"></i>Complete Connection';
    }
}
</script>
