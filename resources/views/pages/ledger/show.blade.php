<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header with Back Button -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <a href="/ledger" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 mb-2 inline-block">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Ledger
                    </a>
                    <x-ui.page-header 
                        title="Ledger Entry Details" 
                        subtitle="View complete transaction details and audit information"
                    />
                </div>
                <div class="flex gap-2">
                    <x-ui.button variant="secondary" size="md" onclick="downloadEntry()" icon="fas fa-download">
                        Download
                    </x-ui.button>
                    <x-ui.button variant="primary" size="md" onclick="window.print()" icon="fas fa-print">
                        Print
                    </x-ui.button>
                </div>
            </div>

            <!-- Entry Header Card -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Entry ID</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">LDG-2024-0001</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Transaction Type</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">Bill Charge</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-orange-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Amount</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">₱739.20</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Status</p>
                    <p class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold">Posted</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Transaction Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-file-invoice mr-2"></i>Transaction Details
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Entry ID</span>
                                <span class="font-semibold text-gray-900 dark:text-white">LDG-2024-0001</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Transaction Type</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Bill Charge</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Related Bill</span>
                                <span class="font-semibold text-blue-600 dark:text-blue-400 cursor-pointer hover:underline">BILL-2024-0001</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Amount</span>
                                <span class="font-semibold text-orange-600 dark:text-orange-400">₱739.20</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Debit/Credit</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Debit (Charge)</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Posted Date</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Jan 15, 2024 10:30 AM</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 dark:text-gray-400">Status</span>
                                <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold">Posted</span>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-user mr-2"></i>Account Information
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Customer Name</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Juan Dela Cruz</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Account Number</span>
                                <span class="font-mono font-semibold text-gray-900 dark:text-white">ACC-2024-001</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Meter Number</span>
                                <span class="font-mono font-semibold text-gray-900 dark:text-white">MTR-00012345</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 dark:text-gray-400">Billing Period</span>
                                <span class="font-semibold text-gray-900 dark:text-white">January 2024</span>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Trail -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-lock mr-2"></i>Audit Trail (Immutable)
                        </h3>
                        
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Ledger entries are immutable:</strong> Historical records cannot be modified or deleted. All changes are logged for COA compliance.
                            </p>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Created By</span>
                                <span class="font-semibold text-gray-900 dark:text-white">System (Bill Generation)</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Created Date</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Jan 15, 2024 10:30 AM</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Last Modified</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Never (Immutable)</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 dark:text-gray-400">Reference Batch</span>
                                <span class="font-mono font-semibold text-gray-900 dark:text-white">BATCH-2024-0156</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-bolt mr-2"></i>Quick Actions
                        </h3>
                        
                        <div class="space-y-2">
                            <button onclick="downloadEntry()" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                                <i class="fas fa-download mr-2"></i>Download Entry
                            </button>
                            <button onclick="window.print()" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition">
                                <i class="fas fa-print mr-2"></i>Print Entry
                            </button>
                            <button onclick="viewRelatedBill()" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                                <i class="fas fa-link mr-2"></i>View Bill
                            </button>
                            <button onclick="viewAccountLedger()" class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition">
                                <i class="fas fa-history mr-2"></i>Account History
                            </button>
                        </div>
                    </div>

                    <!-- Entry Info -->
                    <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-lg p-4">
                        <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">
                            <i class="fas fa-check-circle mr-2"></i>Entry Posted
                        </h4>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            This ledger entry has been successfully posted and is now immutable. It cannot be modified or deleted.
                        </p>
                    </div>

                    <!-- COA Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">COA Reference</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">GL Account</span>
                                <span class="font-semibold text-gray-900 dark:text-white">1100</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Account Type</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Revenue</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">GL Description</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Water Service Revenue</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite([
        'resources/js/utils/action-functions.js'
    ])

    <script>
    function downloadEntry() {
        alert('Download entry as PDF');
    }

    function viewRelatedBill() {
        window.location.href = '/billing/show/1';
    }

    function viewAccountLedger() {
        window.location.href = '/ledger/history?account=ACC-2024-001';
    }

    window.downloadEntry = downloadEntry;
    window.viewRelatedBill = viewRelatedBill;
    window.viewAccountLedger = viewAccountLedger;
    </script>
</x-app-layout>
