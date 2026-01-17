<!-- Print Modal -->
<div id="printFormModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
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
            <button onclick="closePrintModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Customer Information -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-3">
                <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Application Details</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400 block">Customer Name</span>
                        <span class="font-medium text-gray-900 dark:text-white" id="printCustomerName">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400 block">Application #</span>
                        <span class="font-medium text-gray-900 dark:text-white font-mono" id="printApplicationNumber">-</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-gray-600 dark:text-gray-400 block">Address</span>
                        <span class="font-medium text-gray-900 dark:text-white" id="printAddress">-</span>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-gray-600"></div>

            <!-- Document Selection -->
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Documents</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a id="printApplicationFormLink"
                        href="#"
                        target="_blank"
                        class="px-4 py-3 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 font-medium rounded-lg transition-colors flex items-center justify-center gap-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        <i class="fas fa-file-alt"></i>
                        Application Form
                    </a>
                    <a id="printContractLink"
                        href="#"
                        target="_blank"
                        class="px-4 py-3 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 font-medium rounded-lg transition-colors flex items-center justify-center gap-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        <i class="fas fa-file-contract"></i>
                        Water Service Contract
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end">
            <button onclick="closePrintModal()"
                class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
// Store current application data for print modal
let currentPrintApplication = null;

// Format customer name properly
function formatCustomerNameForPrint(application) {
    const customer = application.customer;
    if (!customer) return application.customer_name || '-';

    // Try different name field combinations
    if (customer.CustomerName) return customer.CustomerName;
    if (customer.customer_name) return customer.customer_name;

    const firstName = customer.cust_first_name || customer.first_name || '';
    const middleName = customer.cust_middle_name || customer.middle_name || '';
    const lastName = customer.cust_last_name || customer.last_name || '';

    return [firstName, middleName, lastName].filter(n => n).join(' ').trim() || '-';
}

// Format address for display
function formatPrintAddress(application) {
    const address = application.address;
    if (!address) return application.address_line || '-';

    const purok = address.purok?.p_desc || address.purok_name || '';
    const barangay = address.barangay?.b_desc || address.barangay_name || '';

    const parts = [purok, barangay].filter(p => p && p.trim());
    return parts.join(', ').trim() || '-';
}

// Open print modal
function openPrintModal(application) {
    currentPrintApplication = application;
    const appId = application.application_id;

    // Update modal content with properly formatted data
    document.getElementById('printCustomerName').textContent = formatCustomerNameForPrint(application);
    document.getElementById('printApplicationNumber').textContent = application.application_number || 'APP-' + appId;
    document.getElementById('printAddress').textContent = formatPrintAddress(application);

    // Update print links with correct URLs
    document.getElementById('printApplicationFormLink').href = '/connection/service-application/' + appId + '/print';
    document.getElementById('printContractLink').href = '/connection/service-application/' + appId + '/contract';

    // Show modal
    document.getElementById('printFormModal').classList.remove('hidden');
}

// Close print modal
function closePrintModal() {
    document.getElementById('printFormModal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('printFormModal');
    if (e.target === modal) {
        closePrintModal();
    }
});
</script>
