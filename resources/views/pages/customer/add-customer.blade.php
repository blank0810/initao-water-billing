<x-app-layout>
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <div class="flex-1 flex flex-col overflow-auto">
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-5xl mx-auto">
                    <!-- Header -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Customer Application</h1>
                                <p class="text-gray-600 dark:text-gray-400">Register new customer with service application</p>
                            </div>
                            <a href="{{ route('customer.list') }}"
                                class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to List
                            </a>
                        </div>
                    </div>

                    <!-- Success Alert (Flowbite) -->
                    <div id="successAlert" class="hidden flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Success</span>
                        <div>
                            <span class="font-medium">Success!</span> <span id="successDetails"></span>
                        </div>
                        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#successAlert" aria-label="Close">
                            <span class="sr-only">Close</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Error Alert (Flowbite) -->
                    <div id="errorAlert" class="hidden flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Error</span>
                        <div>
                            <span class="font-medium">Error!</span> <span id="errorDetails"></span>
                        </div>
                        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#errorAlert" aria-label="Close">
                            <span class="sr-only">Close</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Application Form -->
                    <form id="customerApplicationForm" class="space-y-6">
                        @csrf

                        <!-- Personal Information Section -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
                            <div class="flex items-center mb-6">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h3 class="ml-4 text-xl font-semibold text-gray-900 dark:text-white">Personal Information</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="cust_first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="cust_first_name" name="cust_first_name" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 uppercase"
                                        placeholder="JUAN">
                                </div>
                                <div>
                                    <label for="cust_middle_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Middle Name</label>
                                    <input type="text" id="cust_middle_name" name="cust_middle_name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 uppercase"
                                        placeholder="SANTOS">
                                </div>
                                <div>
                                    <label for="cust_last_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="cust_last_name" name="cust_last_name" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 uppercase"
                                        placeholder="DELA CRUZ">
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="c_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Customer Type <span class="text-red-500">*</span>
                                </label>
                                <select id="c_type" name="c_type" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Select Customer Type</option>
                                    <option value="RESIDENTIAL">Residential</option>
                                    <option value="COMMERCIAL">Commercial</option>
                                    <option value="INDUSTRIAL">Industrial</option>
                                    <option value="GOVERNMENT">Government</option>
                                </select>
                            </div>
                        </div>

                        <!-- Service Address Section -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
                            <div class="flex items-center mb-6">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 dark:bg-green-900">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <h3 class="ml-4 text-xl font-semibold text-gray-900 dark:text-white">Service Address</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="province" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Province <span class="text-red-500">*</span>
                                    </label>
                                    <select id="province" name="prov_id" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="">Select Province</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="town" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Town/Municipality <span class="text-red-500">*</span>
                                    </label>
                                    <select id="town" name="t_id" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="">Select Town</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="barangay" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Barangay <span class="text-red-500">*</span>
                                    </label>
                                    <select id="barangay" name="b_id" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="">Select Barangay</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="purok" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Purok <span class="text-red-500">*</span>
                                    </label>
                                    <select id="purok" name="p_id" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="">Select Purok</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="land_mark" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Landmark</label>
                                <input type="text" id="land_mark" name="land_mark"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 uppercase"
                                    placeholder="NEAR CHURCH, BESIDE STORE, ETC.">
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Optional - helps with location identification</p>
                            </div>
                        </div>

                        <!-- Service Connection Details Section -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
                            <div class="flex items-center mb-6">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <h3 class="ml-4 text-xl font-semibold text-gray-900 dark:text-white">Service Connection Details</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="account_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Account Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="account_type" name="account_type_id" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="">Select Account Type</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="water_rate" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Water Rate Schedule <span class="text-red-500">*</span>
                                    </label>
                                    <select id="water_rate" name="rate_id" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="">Select Water Rate</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Application Summary -->
                            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Application Summary
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400">Application Date:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ now()->format('F d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                        <span class="px-2.5 py-0.5 bg-orange-100 text-orange-800 text-xs font-medium rounded dark:bg-orange-900 dark:text-orange-300">PENDING</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6">
                            <div class="flex items-center p-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                </svg>
                                <span class="sr-only">Info</span>
                                <div>
                                    <span class="font-medium">Note:</span> Fields marked with <span class="text-red-500">*</span> are required
                                </div>
                            </div>

                            <div class="flex items-center space-x-3">
                                <a href="{{ route('customer.list') }}"
                                    class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                    Cancel
                                </a>
                                <button type="submit" id="submitButton"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Submit Application
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </main>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load all dropdown data on page load
            loadProvinces();
            loadTowns();
            loadBarangays();
            loadPuroks();
            loadAccountTypes();
            loadWaterRates();

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

        // Load all towns
        async function loadTowns() {
            try {
                const response = await fetch('/api/address/towns');
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

        // Load all puroks
        async function loadPuroks() {
            try {
                const response = await fetch('/api/address/puroks');
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
                document.getElementById('errorDetails').textContent = errors.join(', ');
                document.getElementById('errorAlert').classList.remove('hidden');
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
            const originalHTML = submitButton.innerHTML;

            // Disable button and show loading
            submitButton.innerHTML = `
                <svg class="inline w-4 h-4 mr-2 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Submitting...
            `;
            submitButton.disabled = true;

            // Hide previous messages
            document.getElementById('successAlert').classList.add('hidden');
            document.getElementById('errorAlert').classList.add('hidden');

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
                    document.getElementById('successAlert').classList.remove('hidden');

                    // Reset form
                    e.target.reset();

                    // Scroll to top
                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    // Reload dropdowns
                    loadProvinces();
                    loadTowns();
                    loadBarangays();
                    loadPuroks();
                    loadAccountTypes();
                    loadWaterRates();
                } else {
                    throw new Error(result.message || 'Failed to submit application');
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('errorDetails').textContent = error.message;
                document.getElementById('errorAlert').classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } finally {
                // Re-enable button
                submitButton.innerHTML = originalHTML;
                submitButton.disabled = false;
            }
        }
    </script>
    @endpush
</x-app-layout>
