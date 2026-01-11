<!-- Reject Application Modal -->
<div id="rejectApplicationModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reject Application</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Decline this service application</p>
                </div>
            </div>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-4">
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded">
                <p class="text-sm text-red-800 dark:text-red-200 font-medium">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    This action cannot be undone. The customer will need to submit a new application.
                </p>
            </div>

            <!-- Application Info -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Application #:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="rejectAppNumber">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="rejectCustomerName">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="rejectCurrentStatus">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Submitted:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="rejectSubmittedDate">-</span>
                    </div>
                </div>
            </div>

            <!-- Rejection Reason -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Rejection Reason <span class="text-red-500">*</span>
                </label>
                <textarea id="rejectReason" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    placeholder="Please provide a detailed reason for rejection..."
                    required></textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimum 10 characters required</p>
            </div>

            <!-- Common Reasons -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quick Select:</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setRejectReason('Incomplete documentation. Please submit all required documents.')"
                        class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Incomplete Docs
                    </button>
                    <button type="button" onclick="setRejectReason('Invalid property documents. Proof of ownership or lease agreement is required.')"
                        class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Invalid Property Docs
                    </button>
                    <button type="button" onclick="setRejectReason('Service address is outside the coverage area.')"
                        class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Outside Coverage
                    </button>
                    <button type="button" onclick="setRejectReason('Duplicate application. Customer already has an existing application or connection.')"
                        class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Duplicate
                    </button>
                </div>
            </div>

            <input type="hidden" id="rejectApplicationId" value="">
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <button onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="submitRejection()" id="rejectSubmitBtn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-times-circle mr-2"></i>Reject Application
            </button>
        </div>
    </div>
</div>

<script>
function openRejectModal(applicationId, appNumber, customerName, currentStatus, submittedDate) {
    document.getElementById('rejectApplicationId').value = applicationId;
    document.getElementById('rejectAppNumber').textContent = appNumber;
    document.getElementById('rejectCustomerName').textContent = customerName;
    document.getElementById('rejectCurrentStatus').textContent = currentStatus;
    document.getElementById('rejectSubmittedDate').textContent = submittedDate;
    document.getElementById('rejectReason').value = '';

    document.getElementById('rejectApplicationModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectApplicationModal').classList.add('hidden');
}

function setRejectReason(reason) {
    document.getElementById('rejectReason').value = reason;
}

async function submitRejection() {
    const applicationId = document.getElementById('rejectApplicationId').value;
    const reason = document.getElementById('rejectReason').value.trim();
    const btn = document.getElementById('rejectSubmitBtn');

    if (reason.length < 10) {
        alert('Please provide a detailed rejection reason (minimum 10 characters)');
        return;
    }

    if (!confirm('Are you sure you want to reject this application? This action cannot be undone.')) {
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

    try {
        const response = await fetch(`/connection/service-application/${applicationId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                reason: reason
            })
        });

        const data = await response.json();

        if (data.success) {
            closeRejectModal();
            if (window.showToast) {
                window.showToast('Application rejected', 'warning');
            }
            location.reload();
        } else {
            throw new Error(data.message || 'Rejection failed');
        }
    } catch (error) {
        alert('Error: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-times-circle mr-2"></i>Reject Application';
    }
}
</script>
