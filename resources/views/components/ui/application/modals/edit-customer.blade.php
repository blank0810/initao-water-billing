<div id="editCustomerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class="fas fa-edit text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Customer</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Update customer information</p>
                </div>
            </div>
            <button onclick="closeEditCustomerModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="editCustomerForm" class="p-6 space-y-6">
            <input type="hidden" id="editCustomerId">

            <!-- Personal Information -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name *</label>
                <input type="text" id="editCustomerName" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <!-- Suffix -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Suffix</label>
                <select id="editCustomerSuffix"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        onchange="toggleEditCustomerSuffix()">
                    <option value="">None</option>
                    <option value="JR.">Jr.</option>
                    <option value="SR.">Sr.</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                    <option value="IV">IV</option>
                    <option value="V">V</option>
                    <option value="OTHER">Other</option>
                </select>
                <input type="text" id="editCustomerSuffixCustom"
                       class="mt-2 w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white uppercase hidden"
                       placeholder="Enter suffix" maxlength="10">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number *</label>
                    <input type="tel" id="editPhone" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" id="editEmail" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Type *</label>
                    <select id="editIdType" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Select ID Type</option>
                        <option value="National ID">National ID</option>
                        <option value="Driver's License">Driver's License</option>
                        <option value="Passport">Passport</option>
                        <option value="SSS">SSS ID</option>
                        <option value="PhilHealth">PhilHealth ID</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Number *</label>
                    <input type="text" id="editIdNumber" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Complete Address *</label>
                <input type="text" id="editAddress" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Area *</label>
                    <select id="editArea" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Select Area</option>
                        <option value="Zone A">Zone A</option>
                        <option value="Zone B">Zone B</option>
                        <option value="Zone C">Zone C</option>
                        <option value="Zone D">Zone D</option>
                        <option value="Zone E">Zone E</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meter Reader (Auto)</label>
                    <input type="text" id="editMeterReader" readonly class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reading Schedule (Auto)</label>
                    <input type="date" id="editReadingSchedule" readonly class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Registration Type *</label>
                    <select id="editRegType" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="RESIDENTIAL">Residential</option>
                        <option value="COMMERCIAL">Commercial</option>
                        <option value="INDUSTRIAL">Industrial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Landmark</label>
                    <input type="text" id="editLandmark" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
        </form>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <button onclick="closeEditCustomerModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="saveEditCustomer()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>
    </div>
</div>

<script>
const areaMapping = {
    'Zone A': { meterReader: 'John Smith', readingDay: 5 },
    'Zone B': { meterReader: 'Jane Doe', readingDay: 10 },
    'Zone C': { meterReader: 'Mike Johnson', readingDay: 15 },
    'Zone D': { meterReader: 'Sarah Williams', readingDay: 20 },
    'Zone E': { meterReader: 'Tom Brown', readingDay: 25 }
};

document.getElementById('editArea')?.addEventListener('change', function(e) {
    const area = e.target.value;
    if (area && areaMapping[area]) {
        document.getElementById('editMeterReader').value = areaMapping[area].meterReader;
        const today = new Date();
        const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, areaMapping[area].readingDay);
        document.getElementById('editReadingSchedule').value = nextMonth.toISOString().split('T')[0];
    }
});

function toggleEditCustomerSuffix() {
    const select = document.getElementById('editCustomerSuffix');
    const customInput = document.getElementById('editCustomerSuffixCustom');
    if (select.value === 'OTHER') {
        customInput.classList.remove('hidden');
        customInput.focus();
    } else {
        customInput.classList.add('hidden');
        customInput.value = '';
    }
}

function closeEditCustomerModal() {
    document.getElementById('editCustomerModal').classList.add('hidden');
}

function saveEditCustomer() {
    const suffixSelect = document.getElementById('editCustomerSuffix').value;
    const resolvedSuffix = suffixSelect === 'OTHER' ? (document.getElementById('editCustomerSuffixCustom').value || '') : suffixSelect;

    const data = {
        id: document.getElementById('editCustomerId').value,
        CustomerName: document.getElementById('editCustomerName').value,
        suffix: resolvedSuffix,
        phone: document.getElementById('editPhone').value,
        Email: document.getElementById('editEmail').value,
        id_type: document.getElementById('editIdType').value,
        id_number: document.getElementById('editIdNumber').value,
        AreaCode: document.getElementById('editAddress').value,
        area: document.getElementById('editArea').value,
        landmark: document.getElementById('editLandmark').value,
        meterReader: document.getElementById('editMeterReader').value,
        readingSchedule: document.getElementById('editReadingSchedule').value,
        registration_type: document.getElementById('editRegType').value
    };
    
    window.dispatchEvent(new CustomEvent('save-customer', { detail: data }));
    closeEditCustomerModal();
}

window.addEventListener('edit-customer', (e) => {
    const data = e.detail;
    document.getElementById('editCustomerId').value = data.id || '';
    document.getElementById('editCustomerName').value = data.CustomerName || '';

    // Set suffix dropdown
    const suffixSelect = document.getElementById('editCustomerSuffix');
    const customInput = document.getElementById('editCustomerSuffixCustom');
    const predefined = ['', 'JR.', 'SR.', 'II', 'III', 'IV', 'V'];
    const upperSuffix = (data.suffix || '').toUpperCase();

    if (predefined.includes(upperSuffix)) {
        suffixSelect.value = upperSuffix;
        customInput.classList.add('hidden');
        customInput.value = '';
    } else if (data.suffix) {
        suffixSelect.value = 'OTHER';
        customInput.value = data.suffix;
        customInput.classList.remove('hidden');
    } else {
        suffixSelect.value = '';
        customInput.classList.add('hidden');
        customInput.value = '';
    }

    document.getElementById('editPhone').value = data.phone || '';
    document.getElementById('editEmail').value = data.Email || '';
    document.getElementById('editIdType').value = data.id_type || '';
    document.getElementById('editIdNumber').value = data.id_number || '';
    document.getElementById('editAddress').value = data.AreaCode || '';
    document.getElementById('editArea').value = data.area || '';
    document.getElementById('editLandmark').value = data.landmark || '';
    document.getElementById('editMeterReader').value = data.meterReader || '';
    document.getElementById('editReadingSchedule').value = data.readingSchedule || '';
    document.getElementById('editRegType').value = data.registration_type || 'RESIDENTIAL';
    
    document.getElementById('editCustomerModal').classList.remove('hidden');
});
</script>
