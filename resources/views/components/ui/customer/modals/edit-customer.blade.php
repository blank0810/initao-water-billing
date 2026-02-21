<div id="editCustomerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Customer</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            <form id="editCustomerForm" class="space-y-4">
                <input type="hidden" id="edit_customer_id">

                <!-- Name Fields -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name <span class="text-red-500">*</span></label>
                        <input type="text" id="edit_cust_first_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name</label>
                        <input type="text" id="edit_cust_middle_name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" id="edit_cust_last_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Suffix</label>
                        <select id="edit_customer_suffix"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                onchange="toggleEditCustomSuffix()">
                            <option value="">None</option>
                            <option value="JR.">Jr.</option>
                            <option value="SR.">Sr.</option>
                            <option value="II">II</option>
                            <option value="III">III</option>
                            <option value="IV">IV</option>
                            <option value="V">V</option>
                            <option value="OTHER">Other</option>
                        </select>
                        <input type="text" id="edit_customer_suffix_custom"
                               class="mt-2 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white uppercase hidden"
                               placeholder="Enter suffix" maxlength="10">
                    </div>
                </div>

                <!-- Customer Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Type <span class="text-red-500">*</span></label>
                    <select id="edit_customer_type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="RESIDENTIAL">Residential</option>
                        <option value="COMMERCIAL">Commercial</option>
                        <option value="INDUSTRIAL">Industrial</option>
                    </select>
                </div>

                <!-- Landmark -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Landmark</label>
                    <input type="text" id="edit_customer_landmark" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Nearby landmark (optional)">
                </div>

                <!-- Error message area -->
                <div id="edit_customer_error" class="hidden text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg"></div>
            </form>
        </div>

        <div class="flex gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
            <button onclick="closeEditModal()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button onclick="saveCustomer()" id="editCustomerSaveBtn" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                Save Changes
            </button>
        </div>
    </div>
</div>

<script>
function openEditModal(data) {
    document.getElementById('edit_customer_id').value = data.cust_id || '';
    document.getElementById('edit_cust_first_name').value = data.cust_first_name || '';
    document.getElementById('edit_cust_middle_name').value = data.cust_middle_name || '';
    document.getElementById('edit_cust_last_name').value = data.cust_last_name || '';
    document.getElementById('edit_customer_type').value = (data.c_type || 'RESIDENTIAL').toUpperCase();
    document.getElementById('edit_customer_landmark').value = data.land_mark || '';

    // Set suffix dropdown
    const suffixSelect = document.getElementById('edit_customer_suffix');
    const customInput = document.getElementById('edit_customer_suffix_custom');
    const predefined = ['', 'JR.', 'SR.', 'II', 'III', 'IV', 'V'];
    const upperSuffix = (data.cust_suffix || '').toUpperCase();

    if (predefined.includes(upperSuffix)) {
        suffixSelect.value = upperSuffix;
        customInput.classList.add('hidden');
        customInput.value = '';
    } else if (data.cust_suffix) {
        suffixSelect.value = 'OTHER';
        customInput.value = data.cust_suffix;
        customInput.classList.remove('hidden');
    } else {
        suffixSelect.value = '';
        customInput.classList.add('hidden');
        customInput.value = '';
    }

    // Clear any previous error
    const errorDiv = document.getElementById('edit_customer_error');
    errorDiv.classList.add('hidden');
    errorDiv.textContent = '';

    document.getElementById('editCustomerModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editCustomerModal').classList.add('hidden');
}

function toggleEditCustomSuffix() {
    const select = document.getElementById('edit_customer_suffix');
    const customInput = document.getElementById('edit_customer_suffix_custom');
    if (select.value === 'OTHER') {
        customInput.classList.remove('hidden');
        customInput.focus();
    } else {
        customInput.classList.add('hidden');
        customInput.value = '';
    }
}

async function saveCustomer() {
    const custId = document.getElementById('edit_customer_id').value;
    const firstName = document.getElementById('edit_cust_first_name').value.trim();
    const lastName = document.getElementById('edit_cust_last_name').value.trim();
    const errorDiv = document.getElementById('edit_customer_error');
    const saveBtn = document.getElementById('editCustomerSaveBtn');

    // Client-side validation
    if (!firstName || !lastName) {
        errorDiv.textContent = 'First name and last name are required.';
        errorDiv.classList.remove('hidden');
        return;
    }

    if (!custId) {
        errorDiv.textContent = 'Customer ID is missing. Cannot update.';
        errorDiv.classList.remove('hidden');
        return;
    }

    // Resolve suffix
    let suffix = document.getElementById('edit_customer_suffix').value;
    if (suffix === 'OTHER') {
        suffix = document.getElementById('edit_customer_suffix_custom').value.trim();
    }

    const payload = {
        cust_first_name: firstName,
        cust_middle_name: document.getElementById('edit_cust_middle_name').value.trim() || null,
        cust_last_name: lastName,
        cust_suffix: suffix || null,
        c_type: document.getElementById('edit_customer_type').value,
        land_mark: document.getElementById('edit_customer_landmark').value.trim() || null,
    };

    // Disable button during request
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';
    errorDiv.classList.add('hidden');

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch(`/customer/${custId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        });

        const result = await response.json();

        if (!response.ok) {
            if (result.errors) {
                const messages = Object.values(result.errors).flat().join(', ');
                throw new Error(messages);
            }
            throw new Error(result.message || 'Failed to update customer');
        }

        closeEditModal();

        // Show success alert
        showEditAlert('Customer updated successfully!', 'success');

        // Notify listeners to reload data
        window.dispatchEvent(new CustomEvent('customer-updated', { detail: result.customer }));

    } catch (error) {
        errorDiv.textContent = error.message;
        errorDiv.classList.remove('hidden');
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save Changes';
    }
}

function showEditAlert(message, type = 'info') {
    const alertColors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };

    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 ${alertColors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-[60] transition-opacity`;
    alertDiv.textContent = message;
    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

// Listen for edit-customer event from JS list managers
window.addEventListener('edit-customer', (e) => {
    openEditModal(e.detail);
});
</script>
