<x-app-layout>
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <div class="flex-1 flex flex-col overflow-auto">
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-4xl mx-auto">
                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Customer Application</h1>
                        <p class="text-gray-600 dark:text-gray-400">Register new customer with service application</p>
                    </div>

                    <!-- Success Message -->
                    <div id="successMessage" class="hidden mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <p class="font-medium text-green-800 dark:text-green-300">Application submitted successfully!</p>
                                <p class="text-sm text-green-700 dark:text-green-400 mt-1" id="successDetails"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="hidden mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <div>
                                <p class="font-medium text-red-800 dark:text-red-300">Error submitting application</p>
                                <p class="text-sm text-red-700 dark:text-red-400 mt-1" id="errorDetails"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Application Form -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <form id="customerApplicationForm" class="space-y-6">
                            @csrf

                            <!-- Personal Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                                        <input type="text" name="cust_first_name" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white uppercase"
                                            placeholder="JUAN">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name</label>
                                        <input type="text" name="cust_middle_name"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white uppercase"
                                            placeholder="SANTOS">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                                        <input type="text" name="cust_last_name" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white uppercase"
                                            placeholder="DELA CRUZ">
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Type *</label>
                                    <select name="c_type" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                        <option value="">Select Customer Type</option>
                                        <option value="RESIDENTIAL">Residential</option>
                                        <option value="COMMERCIAL">Commercial</option>
                                        <option value="INDUSTRIAL">Industrial</option>
                                        <option value="GOVERNMENT">Government</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Service Address -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Service Address</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Province *</label>
                                        <select id="province" name="prov_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            <option value="">Select Province</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select province first</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Town/Municipality *
                                            <span id="town-lock-icon" class="ml-1 text-gray-400" title="Select province first">🔒</span>
                                        </label>
                                        <select id="town" name="t_id" required disabled class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                            <option value="">Select Province First</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="town-helper">Please select a province first</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Barangay *</label>
                                        <select id="barangay" name="b_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            <option value="">Select Barangay</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Choose your barangay</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Purok *
                                            <span id="purok-lock-icon" class="ml-1 text-gray-400" title="Select barangay first">🔒</span>
                                        </label>
                                        <select id="purok" name="p_id" required disabled class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                            <option value="">Select Barangay First</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="purok-helper">Please select a barangay first</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Landmark</label>
                                    <input type="text" name="land_mark"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white uppercase"
                                        placeholder="NEAR CHURCH, BESIDE STORE, ETC.">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional - helps with location identification</p>
                                </div>
                            </div>

                            <!-- Service Connection Details -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Service Connection Details</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Type *</label>
                                        <select id="account_type" name="account_type_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            <option value="">Select Account Type</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Water Rate Schedule *</label>
                                        <select id="water_rate" name="rate_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                            <option value="">Select Water Rate</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Application Summary -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Application Summary</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Application Date:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ now()->format('F d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                        <span class="px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 rounded text-xs font-medium">PENDING</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('customer.list') }}"
                                    class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200 font-medium">
                                    Cancel
                                </a>
                                <button type="submit" id="submitButton"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium shadow-sm">
                                    Submit Application
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Form Instructions -->
                    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-blue-800 dark:text-blue-300">
                                <p class="font-medium mb-1">Application Guidelines</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Fields marked with * are required</li>
                                    <li>Names will be automatically converted to UPPERCASE</li>
                                    <li>Fill out address fields in order: Province → Town → Barangay → Purok</li>
                                    <li>Disabled fields (🔒) will unlock when you complete their prerequisite</li>
                                    <li>Application creates both customer record and service application</li>
                                    <li>Application will be PENDING until approved by admin</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial dropdown data
            loadProvinces();
            loadBarangays();
            loadAccountTypes();
            loadWaterRates();

            // Cascading dropdown event listeners
            document.getElementById('province').addEventListener('change', function() {
                const provinceId = this.value;
                const townSelect = document.getElementById('town');
                const townLockIcon = document.getElementById('town-lock-icon');
                const townHelper = document.getElementById('town-helper');

                if (provinceId) {
                    loadTowns(provinceId);
                    townSelect.disabled = false;
                    townLockIcon.style.display = 'none';
                    townHelper.textContent = 'Select your town/municipality';
                    townHelper.classList.remove('text-orange-500');
                    townHelper.classList.add('text-gray-500', 'dark:text-gray-400');
                } else {
                    clearDropdown('town');
                    townSelect.disabled = true;
                    townLockIcon.style.display = 'inline';
                    townHelper.textContent = 'Please select a province first';
                    townHelper.classList.remove('text-gray-500', 'dark:text-gray-400');
                    townHelper.classList.add('text-orange-500');
                }
            });

            document.getElementById('barangay').addEventListener('change', function() {
                const barangayId = this.value;
                const purokSelect = document.getElementById('purok');
                const purokLockIcon = document.getElementById('purok-lock-icon');
                const purokHelper = document.getElementById('purok-helper');

                if (barangayId) {
                    loadPuroks(barangayId);
                    purokSelect.disabled = false;
                    purokLockIcon.style.display = 'none';
                    purokHelper.textContent = 'Select your purok';
                    purokHelper.classList.remove('text-orange-500');
                    purokHelper.classList.add('text-gray-500', 'dark:text-gray-400');
                } else {
                    clearDropdown('purok');
                    purokSelect.disabled = true;
                    purokLockIcon.style.display = 'inline';
                    purokHelper.textContent = 'Please select a barangay first';
                    purokHelper.classList.remove('text-gray-500', 'dark:text-gray-400');
                    purokHelper.classList.add('text-orange-500');
                }
            });

            // Form submission
            document.getElementById('customerApplicationForm').addEventListener('submit', handleFormSubmit);
        });

        // Load provinces
        async function loadProvinces() {
            try {
                const response = await fetch('/api/address/provinces');
                const provinces = await response.json();
                populateDropdown('province', provinces, 'prov_id', 'prov_desc');
            } catch (error) {
                console.error('Error loading provinces:', error);
            }
        }

        // Load towns by province
        async function loadTowns(provinceId) {
            try {
                const response = await fetch(`/api/address/towns?province_id=${provinceId}`);
                const towns = await response.json();
                populateDropdown('town', towns, 't_id', 't_desc');
            } catch (error) {
                console.error('Error loading towns:', error);
            }
        }

        // Load all barangays
        async function loadBarangays() {
            try {
                const response = await fetch('/api/address/barangays');
                const barangays = await response.json();
                populateDropdown('barangay', barangays, 'b_id', 'b_desc');
            } catch (error) {
                console.error('Error loading barangays:', error);
            }
        }

        // Load puroks by barangay
        async function loadPuroks(barangayId) {
            try {
                const response = await fetch(`/api/address/puroks?barangay_id=${barangayId}`);
                const puroks = await response.json();
                populateDropdown('purok', puroks, 'p_id', 'p_desc');
            } catch (error) {
                console.error('Error loading puroks:', error);
            }
        }

        // Load account types
        async function loadAccountTypes() {
            try {
                const response = await fetch('/api/address/account-types');
                const accountTypes = await response.json();
                populateDropdown('account_type', accountTypes, 'at_id', 'at_desc');
            } catch (error) {
                console.error('Error loading account types:', error);
            }
        }

        // Load water rates
        async function loadWaterRates() {
            try {
                const response = await fetch('/api/address/water-rates');
                const waterRates = await response.json();
                populateDropdown('water_rate', waterRates, 'wr_id', 'rate_desc');
            } catch (error) {
                console.error('Error loading water rates:', error);
            }
        }

        // Populate dropdown
        function populateDropdown(elementId, items, valueKey, textKey) {
            const select = document.getElementById(elementId);
            const currentValue = select.value;

            // Define proper placeholders for each dropdown
            const placeholders = {
                'province': 'Select Province',
                'town': 'Select Town',
                'barangay': 'Select Barangay',
                'purok': 'Select Purok',
                'account_type': 'Select Account Type',
                'water_rate': 'Select Water Rate'
            };

            // Clear existing options and set proper placeholder
            const placeholder = placeholders[elementId] || 'Please select...';
            select.innerHTML = `<option value="">${placeholder}</option>`;

            // Add new options
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueKey];
                option.textContent = item[textKey];
                select.appendChild(option);
            });

            // Restore selection if valid
            if (currentValue && select.querySelector(`option[value="${currentValue}"]`)) {
                select.value = currentValue;
            }
        }

        // Clear dropdown
        function clearDropdown(elementId) {
            const select = document.getElementById(elementId);
            const placeholders = {
                'town': 'Select Province First',
                'purok': 'Select Barangay First'
            };

            const placeholder = placeholders[elementId] || 'Please select...';
            select.innerHTML = `<option value="">${placeholder}</option>`;
        }

        // Reset dependent fields to initial disabled state
        function resetDependentFields() {
            // Reset Town field
            const townSelect = document.getElementById('town');
            const townLockIcon = document.getElementById('town-lock-icon');
            const townHelper = document.getElementById('town-helper');
            townSelect.disabled = true;
            townSelect.innerHTML = '<option value="">Select Province First</option>';
            townLockIcon.style.display = 'inline';
            townHelper.textContent = 'Please select a province first';
            townHelper.classList.remove('text-gray-500', 'dark:text-gray-400');
            townHelper.classList.add('text-orange-500');

            // Reset Purok field
            const purokSelect = document.getElementById('purok');
            const purokLockIcon = document.getElementById('purok-lock-icon');
            const purokHelper = document.getElementById('purok-helper');
            purokSelect.disabled = true;
            purokSelect.innerHTML = '<option value="">Select Barangay First</option>';
            purokLockIcon.style.display = 'inline';
            purokHelper.textContent = 'Please select a barangay first';
            purokHelper.classList.remove('text-gray-500', 'dark:text-gray-400');
            purokHelper.classList.add('text-orange-500');
        }

        // Validate form before submission
        function validateForm() {
            const errors = [];

            // Check required fields
            const requiredFields = {
                'cust_first_name': 'First Name',
                'cust_last_name': 'Last Name',
                'c_type': 'Customer Type',
                'prov_id': 'Province',
                't_id': 'Town/Municipality',
                'b_id': 'Barangay',
                'p_id': 'Purok',
                'account_type_id': 'Account Type',
                'rate_id': 'Water Rate Schedule'
            };

            for (const [fieldName, fieldLabel] of Object.entries(requiredFields)) {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (!field || !field.value) {
                    errors.push(`${fieldLabel} is required`);
                }
            }

            if (errors.length > 0) {
                document.getElementById('errorDetails').innerHTML = errors.join('<br>');
                document.getElementById('errorMessage').classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return false;
            }

            return true;
        }

        // Handle form submission
        async function handleFormSubmit(e) {
            e.preventDefault();

            // Validate form first
            if (!validateForm()) {
                return;
            }

            const submitButton = document.getElementById('submitButton');
            const originalText = submitButton.textContent;

            // Disable button and show loading
            submitButton.textContent = 'Submitting...';
            submitButton.disabled = true;

            // Hide previous messages
            document.getElementById('successMessage').classList.add('hidden');
            document.getElementById('errorMessage').classList.add('hidden');

            try {
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData.entries());

                const response = await fetch('{{ route("customer.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    // Show success message
                    document.getElementById('successDetails').textContent =
                        `Customer: ${result.customer.cust_first_name} ${result.customer.cust_last_name} | Application #: ${result.application.application_number}`;
                    document.getElementById('successMessage').classList.remove('hidden');

                    // Reset form
                    e.target.reset();

                    // Reset dependent field states
                    resetDependentFields();

                    // Scroll to top
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    throw new Error(result.message || 'Failed to submit application');
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('errorDetails').textContent = error.message;
                document.getElementById('errorMessage').classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } finally {
                // Re-enable button
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            }
        }
    </script>
    @endpush
</x-app-layout>
