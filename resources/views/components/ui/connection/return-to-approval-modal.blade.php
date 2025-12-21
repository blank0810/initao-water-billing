<div id="returnToApprovalModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <i class="fas fa-undo text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Return to Approval</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Send application back to approval queue</p>
                </div>
            </div>
            <button onclick="closeReturnToApprovalModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    You are about to return <span id="returnCustomerName" class="font-semibold"></span> to the Approval stage.
                    This action is used when a customer was approved by mistake.
                </p>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Customer Code:</span>
                    <span id="returnCustomerCode" class="font-medium text-gray-900 dark:text-white">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Account No:</span>
                    <span id="returnAccountNo" class="font-medium text-gray-900 dark:text-white">-</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason</label>
                <textarea id="returnReason" rows="3" placeholder="Describe why this is being returned..." class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500"></textarea>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button onclick="closeReturnToApprovalModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="confirmReturnToApproval()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-undo mr-2"></i>Return to Approval
            </button>
        </div>
    </div>
</div>
<script>
let returnConn = null;
function openReturnToApprovalModal(conn) {
    returnConn = conn;
    document.getElementById('returnCustomerName').textContent = conn.customer_name || '-';
    document.getElementById('returnCustomerCode').textContent = conn.customer_code || '-';
    document.getElementById('returnAccountNo').textContent = conn.account_no || '-';
    document.getElementById('returnReason').value = '';
    document.getElementById('returnToApprovalModal').classList.remove('hidden');
}
function closeReturnToApprovalModal() {
    document.getElementById('returnToApprovalModal').classList.add('hidden');
    returnConn = null;
}
function confirmReturnToApproval() {
    const reason = document.getElementById('returnReason').value.trim();
    if (!returnConn) return;
    // Simulate sending back to approval
    alert(`Application for ${returnConn.customer_name} returned to Approval.\nReason: ${reason || 'N/A'}`);
    closeReturnToApprovalModal();
    window.location.href = "{{ route('approve.customer') }}";
}
window.openReturnToApprovalModal = openReturnToApprovalModal;
window.closeReturnToApprovalModal = closeReturnToApprovalModal;
window.confirmReturnToApproval = confirmReturnToApproval;
</script>
