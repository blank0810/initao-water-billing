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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Print Application Form</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Select document to print</p>
                </div>
            </div>
            <button onclick="closePrintModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Heading -->
            <div class="text-center">
                <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                    What kind of form to print?
                </h4>
            </div>

            <!-- Customer Information -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-3">
                <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Customer Information</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400 block">Customer Name</span>
                        <span class="font-medium text-gray-900 dark:text-white" id="printCustomerName">-</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400 block">Application #</span>
                        <span class="font-medium text-gray-900 dark:text-white" id="printApplicationNumber">-</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-gray-600 dark:text-gray-400 block">Address</span>
                        <span class="font-medium text-gray-900 dark:text-white" id="printAddress">-</span>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-gray-600"></div>

            <!-- Form Selection Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <button 
                    onclick="printApplicationForm()"
                    class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-file-pdf"></i>
                    Application Form
                </button>
                <button 
                    onclick="printMeedoContract()"
                    class="px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-file-contract"></i>
                    Meedo Contract
                </button>
            </div>
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
    
    const street = address.street || address.a_street || address.street_name || '';
    const purok = address.purok?.PurokName || address.purok?.p_desc || address.purok_name || '';
    const barangay = address.barangay?.b_desc || address.barangay_name || '';
    
    const parts = [street, purok, barangay].filter(p => p && p.trim());
    return parts.join(', ').trim() || '-';
}

// Prepare customer data for print utilities
function prepareCustomerForPrint(application) {
    const customer = application.customer || {};
    
    return {
        CustomerName: formatCustomerNameForPrint(application),
        customer_name: formatCustomerNameForPrint(application),
        AreaCode: formatPrintAddress(application),
        id: customer.id || customer.customer_id || application.application_id,
        customer_code: customer.customer_code || customer.code || application.application_number || '',
        Email: customer.email || customer.Email || '',
        Phone: customer.phone || customer.Phone || '',
        Address: formatPrintAddress(application),
        DateApplied: application.submitted_at || application.created_at || new Date().toISOString()
    };
}

// Open print modal
function openPrintModal(application) {
    currentPrintApplication = application;
    
    // Update modal content with properly formatted data
    document.getElementById('printCustomerName').textContent = formatCustomerNameForPrint(application);
    document.getElementById('printApplicationNumber').textContent = application.application_number || 'APP-' + application.application_id;
    document.getElementById('printAddress').textContent = formatPrintAddress(application);
    
    // Show modal
    document.getElementById('printFormModal').classList.remove('hidden');
}

// Close print modal
function closePrintModal() {
    document.getElementById('printFormModal').classList.add('hidden');
}

// Print Application Form - Opens in new tab with print-ready layout
function printApplicationForm() {
    if (!currentPrintApplication) {
        alert('No application selected');
        return;
    }
    
    // Check if the application form utility is loaded
    if (typeof window.printServiceApplicationForm !== 'function') {
        alert('Application form utility is not loaded. Please refresh the page.');
        console.error('Application form function not found');
        return;
    }
    
    try {
        const customerData = prepareCustomerForPrint(currentPrintApplication);
        
        // Call the application form utility directly
        window.printServiceApplicationForm(customerData);
        
        // Close modal after successfully opening the print page
        closePrintModal();
        
    } catch (error) {
        console.error('Error opening application form:', error);
        alert('Error opening application form. Please try again.');
    }
}

// Print Meedo Contract - Opens in new tab with print-ready layout
function printMeedoContract() {
    if (!currentPrintApplication) {
        alert('No application selected');
        return;
    }
    
    // Check if the meedo contract utility is loaded
    if (typeof window.printWaterServiceContract !== 'function') {
        alert('Meedo contract utility is not loaded. Please refresh the page.');
        console.error('Meedo contract function not found');
        return;
    }
    
    try {
        const customerData = prepareCustomerForPrint(currentPrintApplication);
        
        // Call the meedo contract utility directly
        window.printWaterServiceContract(customerData);
        
        // Close modal after successfully opening the print page
        closePrintModal();
        
    } catch (error) {
        console.error('Error opening meedo contract:', error);
        alert('Error opening meedo contract. Please try again.');
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('printFormModal');
    if (e.target === modal) {
        closePrintModal();
    }
});
</script>

@vite(['resources/js/utils/application-form.js', 'resources/js/utils/meedo-contract.js'])
