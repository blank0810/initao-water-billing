<!-- Verified Modal -->
<div id="verifiedModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Verified by Official</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                This confirms that <span id="verifiedCustomerName" class="font-semibold"></span>'s requirements have been thoroughly reviewed and verified by an authorized official.
            </p>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    <div class="flex justify-between mb-2">
                        <span>Verified by:</span>
                        <span class="font-medium">Admin Officer</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Verification Date:</span>
                        <span class="font-medium" id="verificationDate"></span>
                    </div>
                </div>
            </div>
            <button onclick="document.getElementById('verifiedModal').classList.add('hidden')" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                <i class="fas fa-check mr-2"></i>Acknowledged
            </button>
        </div>
    </div>
</div>

<script>
    // Expose showVerifiedModal globally
    window.showVerifiedModal = function(customerName, date) {
        const modal = document.getElementById('verifiedModal');
        const nameEl = document.getElementById('verifiedCustomerName');
        const dateEl = document.getElementById('verificationDate');
        
        if (modal && nameEl && dateEl) {
            nameEl.textContent = customerName || 'Customer';
            dateEl.textContent = date || new Date().toLocaleDateString();
            modal.classList.remove('hidden');
        }
    };
</script>