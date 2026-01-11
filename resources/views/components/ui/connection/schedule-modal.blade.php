<!-- Schedule Connection Modal -->
<div id="scheduleConnectionModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Schedule Connection</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Set the installation date</p>
                </div>
            </div>
            <button onclick="closeScheduleModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-4">
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded">
                <p class="text-sm text-green-800 dark:text-green-200 font-medium">
                    <i class="fas fa-check-circle mr-2"></i>
                    Payment has been confirmed. Schedule the connection date.
                </p>
            </div>

            <!-- Application Info -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Application #:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="scheduleAppNumber">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="scheduleCustomerName">-</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-600 dark:text-gray-400">Address:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="scheduleAddress">-</span>
                    </div>
                </div>
            </div>

            <!-- Date Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Connection Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="scheduleDate"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    min="">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select a date from today onwards</p>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Notes (Optional)
                </label>
                <textarea id="scheduleNotes" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Any special instructions for the field team..."></textarea>
            </div>

            <input type="hidden" id="scheduleApplicationId" value="">
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <button onclick="closeScheduleModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="submitSchedule()" id="scheduleSubmitBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-calendar-check mr-2"></i>Schedule Connection
            </button>
        </div>
    </div>
</div>

<script>
function openScheduleModal(applicationId, appNumber, customerName, address) {
    document.getElementById('scheduleApplicationId').value = applicationId;
    document.getElementById('scheduleAppNumber').textContent = appNumber;
    document.getElementById('scheduleCustomerName').textContent = customerName;
    document.getElementById('scheduleAddress').textContent = address;

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('scheduleDate').min = today;
    document.getElementById('scheduleDate').value = '';
    document.getElementById('scheduleNotes').value = '';

    document.getElementById('scheduleConnectionModal').classList.remove('hidden');
}

function closeScheduleModal() {
    document.getElementById('scheduleConnectionModal').classList.add('hidden');
}

async function submitSchedule() {
    const applicationId = document.getElementById('scheduleApplicationId').value;
    const scheduledDate = document.getElementById('scheduleDate').value;
    const btn = document.getElementById('scheduleSubmitBtn');

    if (!scheduledDate) {
        alert('Please select a connection date');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

    try {
        const response = await fetch(`/connection/service-application/${applicationId}/schedule`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                scheduled_date: scheduledDate
            })
        });

        const data = await response.json();

        if (data.success) {
            closeScheduleModal();
            if (window.showToast) {
                window.showToast('Connection scheduled successfully!', 'success');
            }
            location.reload();
        } else {
            throw new Error(data.message || 'Scheduling failed');
        }
    } catch (error) {
        alert('Error: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-calendar-check mr-2"></i>Schedule Connection';
    }
}
</script>
