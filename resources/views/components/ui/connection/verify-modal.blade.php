<!-- Verify Application Modal -->
<div id="verifyApplicationModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Verify Application</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Confirm document verification</p>
                </div>
            </div>
            <button onclick="closeVerifyModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded">
                <p class="text-sm text-blue-800 dark:text-blue-200 font-medium">
                    <i class="fas fa-info-circle mr-2"></i>
                    You are verifying that all required documents have been reviewed and are complete.
                </p>
            </div>

            <!-- Application Info -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Application #:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="verifyAppNumber">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="verifyCustomerName">-</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-600 dark:text-gray-400">Address:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="verifyAddress">-</span>
                    </div>
                </div>
            </div>

            <!-- Verification Checklist -->
            <div class="space-y-2">
                <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" id="checkDocuments" class="rounded border-gray-300 text-blue-600 mr-2">
                    All required documents are complete and valid
                </label>
                <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" id="checkAddress" class="rounded border-gray-300 text-blue-600 mr-2">
                    Service address has been verified
                </label>
                <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" id="checkIdentity" class="rounded border-gray-300 text-blue-600 mr-2">
                    Customer identity has been confirmed
                </label>
            </div>

            <input type="hidden" id="verifyApplicationId" value="">
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <button onclick="closeVerifyModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="submitVerification()" id="verifySubmitBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <i class="fas fa-clipboard-check mr-2"></i>Verify Application
            </button>
        </div>
    </div>
</div>

<script>
function openVerifyModal(applicationId, appNumber, customerName, address) {
    document.getElementById('verifyApplicationId').value = applicationId;
    document.getElementById('verifyAppNumber').textContent = appNumber;
    document.getElementById('verifyCustomerName').textContent = customerName;
    document.getElementById('verifyAddress').textContent = address;

    // Reset checkboxes
    document.getElementById('checkDocuments').checked = false;
    document.getElementById('checkAddress').checked = false;
    document.getElementById('checkIdentity').checked = false;
    document.getElementById('verifySubmitBtn').disabled = true;

    document.getElementById('verifyApplicationModal').classList.remove('hidden');
}

function closeVerifyModal() {
    document.getElementById('verifyApplicationModal').classList.add('hidden');
}

// Enable submit when all checkboxes are checked
['checkDocuments', 'checkAddress', 'checkIdentity'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', function() {
        const allChecked = document.getElementById('checkDocuments').checked &&
                          document.getElementById('checkAddress').checked &&
                          document.getElementById('checkIdentity').checked;
        document.getElementById('verifySubmitBtn').disabled = !allChecked;
    });
});

async function submitVerification() {
    const applicationId = document.getElementById('verifyApplicationId').value;
    const btn = document.getElementById('verifySubmitBtn');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

    try {
        const response = await fetch(`/connection/service-application/${applicationId}/verify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            closeVerifyModal();
            // Show success notification
            if (window.showToast) {
                window.showToast('Application verified successfully!', 'success');
            }
            // Reload page or update UI
            location.reload();
        } else {
            throw new Error(data.message || 'Verification failed');
        }
    } catch (error) {
        alert('Error: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-clipboard-check mr-2"></i>Verify Application';
    }
}
</script>
