<!-- Connection Print Modal -->
<div id="connectionPrintModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <i class="fas fa-print text-orange-600 dark:text-orange-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Print Documents</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Select document to print</p>
                </div>
            </div>
            <button onclick="closeConnectionPrintModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Connection Information -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-3">
                <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Connection Details</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400 block">Customer Name</span>
                        <span class="font-medium text-gray-900 dark:text-white" id="connPrintCustomerName">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400 block">Account #</span>
                        <span class="font-medium text-gray-900 dark:text-white font-mono" id="connPrintAccountNo">-</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-gray-600 dark:text-gray-400 block">Address</span>
                        <span class="font-medium text-gray-900 dark:text-white" id="connPrintAddress">-</span>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-gray-600"></div>

            <!-- Document Selection -->
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Documents</p>
                <div class="grid grid-cols-1 gap-3">
                    <!-- Application Form (conditional) -->
                    <a id="connPrintApplicationLink"
                        href="#"
                        target="_blank"
                        class="hidden px-4 py-3 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 font-medium rounded-lg transition-colors flex items-center justify-center gap-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        <i class="fas fa-file-alt"></i>
                        Application Form
                    </a>
                    <!-- Contract (conditional) -->
                    <a id="connPrintContractLink"
                        href="#"
                        target="_blank"
                        class="hidden px-4 py-3 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 font-medium rounded-lg transition-colors flex items-center justify-center gap-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        <i class="fas fa-file-contract"></i>
                        Water Service Contract
                    </a>
                    <!-- Account Statement (always available) -->
                    <a id="connPrintStatementLink"
                        href="#"
                        target="_blank"
                        class="px-4 py-3 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 font-medium rounded-lg transition-colors flex items-center justify-center gap-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        <i class="fas fa-file-invoice"></i>
                        Account Statement
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end">
            <button onclick="closeConnectionPrintModal()"
                class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
// Store current connection data for print modal
let currentPrintConnection = null;

// Format customer name properly
function formatConnectionCustomerName(connection) {
    const customer = connection.customer;
    if (!customer) return '-';

    const firstName = customer.cust_first_name || '';
    const middleName = customer.cust_middle_name || '';
    const lastName = customer.cust_last_name || '';

    return [firstName, middleName, lastName].filter(n => n).join(' ').trim() || '-';
}

// Format address for display
function formatConnectionAddress(connection) {
    const address = connection.address;
    if (!address) return '-';

    const purok = address.purok?.p_desc || '';
    const barangay = address.barangay?.b_desc || '';

    const parts = [purok, barangay].filter(p => p && p.trim());
    return parts.join(', ').trim() || '-';
}

// Open connection print modal
function openConnectionPrintModal(connection) {
    currentPrintConnection = connection;
    const connId = connection.connection_id;
    const appId = connection.service_application?.application_id;

    // Update modal content
    document.getElementById('connPrintCustomerName').textContent = formatConnectionCustomerName(connection);
    document.getElementById('connPrintAccountNo').textContent = connection.account_no || 'CONN-' + connId;
    document.getElementById('connPrintAddress').textContent = formatConnectionAddress(connection);

    // Update statement link (always available)
    document.getElementById('connPrintStatementLink').href = '/customer/service-connection/' + connId + '/statement';

    // Update application and contract links (conditional)
    const appLink = document.getElementById('connPrintApplicationLink');
    const contractLink = document.getElementById('connPrintContractLink');

    if (appId) {
        appLink.href = '/connection/service-application/' + appId + '/print';
        appLink.classList.remove('hidden');
        appLink.classList.add('flex');

        contractLink.href = '/connection/service-application/' + appId + '/contract';
        contractLink.classList.remove('hidden');
        contractLink.classList.add('flex');
    } else {
        appLink.classList.add('hidden');
        appLink.classList.remove('flex');
        contractLink.classList.add('hidden');
        contractLink.classList.remove('flex');
    }

    // Show modal
    document.getElementById('connectionPrintModal').classList.remove('hidden');
}

// Close connection print modal
function closeConnectionPrintModal() {
    document.getElementById('connectionPrintModal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('connectionPrintModal');
    if (e.target === modal) {
        closeConnectionPrintModal();
    }
});
</script>
