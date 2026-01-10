<!-- Ledger Entry Modal Component -->
@props([
    'id' => 'ledger-entry-modal',
    'title' => 'Ledger Entry Details',
    'entry' => null
])

<div id="{{ $id }}-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity"></div>

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full transform transition-all">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-book mr-2"></i>{{ $title }}
            </h2>
            <button onclick="closeLedgerModal('{{ $id }}')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="px-6 py-4 max-h-96 overflow-y-auto">
            <div class="space-y-4">
                <!-- Entry Info -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Entry ID</p>
                        <p class="font-mono font-semibold text-gray-900 dark:text-white">LDG-2024-0001</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Type</p>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded text-xs font-semibold">Bill Charge</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Amount</p>
                        <p class="text-lg font-bold text-orange-600 dark:text-orange-400">₱739.20</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
                        <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded text-xs font-semibold">Posted</span>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- Account Info -->
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white mb-2">Account Information</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Customer</span>
                            <span class="text-gray-900 dark:text-white">Juan Dela Cruz</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Account Number</span>
                            <span class="font-mono text-gray-900 dark:text-white">ACC-2024-001</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Billing Period</span>
                            <span class="text-gray-900 dark:text-white">January 2024</span>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- Transaction Details -->
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white mb-2">Transaction Details</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Debit</span>
                            <span class="text-orange-600 dark:text-orange-400 font-semibold">₱739.20</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Credit</span>
                            <span class="text-gray-600 dark:text-gray-400">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Date Posted</span>
                            <span class="text-gray-900 dark:text-white">Jan 15, 2024 10:30 AM</span>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- Audit Info -->
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                        <i class="fas fa-lock mr-2 text-green-600"></i>Immutable Audit Trail
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        This entry is immutable and cannot be modified or deleted. All changes are logged for complete audit compliance.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end gap-2">
            <button onclick="closeLedgerModal('{{ $id }}')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition font-medium">
                Close
            </button>
            <a href="/ledger/show/1" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
                View Full Details
            </a>
        </div>
    </div>
</div>

<script>
window.openLedgerModal = function(modalId) {
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById(modalId + '-overlay');
    if (modal) modal.classList.remove('hidden');
    if (overlay) overlay.classList.remove('hidden');
};

window.closeLedgerModal = function(modalId) {
    const modal = document.getElementById(modalId);
    const overlay = document.getElementById(modalId + '-overlay');
    if (modal) modal.classList.add('hidden');
    if (overlay) overlay.classList.add('hidden');
};

// Close on overlay click
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('{{ $id }}-overlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            closeLedgerModal('{{ $id }}');
        });
    }
});

// Close on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLedgerModal('{{ $id }}');
    }
});
</script>
