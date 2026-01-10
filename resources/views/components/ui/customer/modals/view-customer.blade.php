<div id="viewCustomerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Customer Profile</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Complete customer information</p>
                </div>
            </div>
            <button onclick="closeViewCustomerModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Personal Information -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Personal Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Full Name:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewCustomerName">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Customer Code:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewCustomerCode">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewPhone">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Email:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewEmail">-</span>
                    </div>
                </div>
            </div>

            <!-- Identification -->
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Identification</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">ID Type:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewIdType">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">ID Number:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewIdNumber">-</span>
                    </div>
                </div>
            </div>

            <!-- Service Location -->
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Service Location</h4>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Address:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewAddress">-</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Area:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewArea">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Landmark:</span>
                            <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewLandmark">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meter Assignment -->
            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Meter Assignment</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Meter Reader:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewMeterReader">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Reading Schedule:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewReadingSchedule">-</span>
                    </div>
                </div>
            </div>

            <!-- Account Details -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Account Details</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Registration Type:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewRegType">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Account Number:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewAccountNo">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewStatus">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Created Date:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white" id="viewCreatedDate">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end">
            <button onclick="closeViewCustomerModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function closeViewCustomerModal() {
    document.getElementById('viewCustomerModal').classList.add('hidden');
}

window.addEventListener('view-customer', (e) => {
    const data = e.detail;
    document.getElementById('viewCustomerName').textContent = data.CustomerName || '-';
    document.getElementById('viewCustomerCode').textContent = data.id || '-';
    document.getElementById('viewPhone').textContent = data.phone || '-';
    document.getElementById('viewEmail').textContent = data.Email || '-';
    document.getElementById('viewIdType').textContent = data.id_type || '-';
    document.getElementById('viewIdNumber').textContent = data.id_number || '-';
    document.getElementById('viewAddress').textContent = data.AreaCode || '-';
    document.getElementById('viewArea').textContent = data.area || '-';
    document.getElementById('viewLandmark').textContent = data.landmark || '-';
    document.getElementById('viewMeterReader').textContent = data.meterReader || '-';
    document.getElementById('viewReadingSchedule').textContent = data.readingSchedule ? new Date(data.readingSchedule).toLocaleDateString() : '-';
    document.getElementById('viewRegType').textContent = data.registration_type || '-';
    document.getElementById('viewAccountNo').textContent = data.account_no || 'Not Assigned';
    document.getElementById('viewCreatedDate').textContent = data.DateApplied ? new Date(data.DateApplied).toLocaleDateString() : '-';
    document.getElementById('viewStatus').textContent = data.Status || '-';
    
    document.getElementById('viewCustomerModal').classList.remove('hidden');
});
</script>
