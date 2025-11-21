@push('styles')
<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }

    /* Smooth transitions for step indicators */
    #step1-indicator, #step2-indicator, #step3-indicator, #step4-indicator {
        transition: all 0.3s ease;
    }

    /* Input focus effects */
    input:focus, select:focus {
        transform: scale(1.01);
        transition: transform 0.2s ease;
    }
</style>
@endpush

<x-app-layout>
<<<<<<< HEAD
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <x-ui.page-header
                title="New Customer Application"
                subtitle="Register a new customer for water service connection"
                icon="fas fa-user-plus">
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('customer.list') }}">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <form id="customerApplicationForm" class="space-y-6">
                @csrf

                <!-- Personal Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Customer's basic details</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="cust_first_name" required placeholder="Juan"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name</label>
                            <input type="text" name="cust_middle_name" placeholder="Santos"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="cust_last_name" required placeholder="Dela Cruz"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone" required placeholder="09XX XXX XXXX" pattern="[0-9]{11}"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">For SMS notifications</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Registration Type <span class="text-red-500">*</span>
                            </label>
                            <select name="registration_type" required 
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Type</option>
                                <option value="RESIDENTIAL">Residential</option>
                                <option value="COMMERCIAL">Commercial</option>
                                <option value="INDUSTRIAL">Industrial</option>
                            </select>
=======
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

                    <!-- Progress Stepper -->
                    <div class="mb-8">
                        <ol class="flex items-center w-full text-sm font-medium text-center text-gray-500 dark:text-gray-400 sm:text-base">
                            <li id="step1-indicator" class="flex md:w-full items-center text-blue-600 dark:text-blue-500 sm:after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                                <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                                    </svg>
                                    Personal <span class="hidden sm:inline-flex sm:ms-2">Info</span>
                                </span>
                            </li>
                            <li id="step2-indicator" class="flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                                <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                    <span class="me-2">2</span>
                                    Service <span class="hidden sm:inline-flex sm:ms-2">Address</span>
                                </span>
                            </li>
                            <li id="step3-indicator" class="flex md:w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700">
                                <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                    <span class="me-2">3</span>
                                    Connection <span class="hidden sm:inline-flex sm:ms-2">Details</span>
                                </span>
                            </li>
                            <li id="step4-indicator" class="flex items-center">
                                <span class="me-2">4</span>
                                Review
                            </li>
                        </ol>
                    </div>

                    <!-- Toast Notifications (will be inserted by JavaScript) -->
                    <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-4"></div>

                    <!-- Application Form -->
                    <form id="customerApplicationForm" class="space-y-6">
                        @csrf

                        <!-- STEP 1: Personal Information -->
                        <div id="step1" class="step-content transition-all duration-300">
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

                        <!-- Step 1 Navigation -->
                        <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" onclick="nextStep()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Next Step
                                <svg class="w-4 h-4 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </div>
                        </div>
                        <!-- End STEP 1 -->

                        <!-- STEP 2: Service Address -->
                        <div id="step2" class="step-content hidden transition-all duration-300">
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

                        <!-- Step 2 Navigation -->
                        <div class="flex items-center justify-between space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" onclick="prevStep()" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Previous
                            </button>
                            <button type="button" onclick="nextStep()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Next Step
                                <svg class="w-4 h-4 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </div>
                        </div>
                        <!-- End STEP 2 -->

                        <!-- STEP 3: Service Connection Details -->
                        <div id="step3" class="step-content hidden transition-all duration-300">
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

                        </div>

                        <!-- Step 3 Navigation -->
                        <div class="flex items-center justify-between space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" onclick="prevStep()" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Previous
                            </button>
                            <button type="button" onclick="nextStep()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Review Application
                                <svg class="w-4 h-4 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </button>
                        </div>
                        </div>
                        <!-- End STEP 3 -->

                        <!-- STEP 4: Review & Confirm -->
                        <div id="step4" class="step-content hidden transition-all duration-300">
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
                                <div class="flex items-center mb-6">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900">
                                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-xl font-semibold text-gray-900 dark:text-white">Review Your Application</h3>
                                </div>

                                <div id="reviewContent" class="space-y-6">
                                    <!-- Review content will be populated by JavaScript -->
                                </div>

                                <!-- Application Info -->
                                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <div class="flex items-center mb-3">
                                        <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <h4 class="font-semibold text-blue-900 dark:text-blue-300">Application Summary</h4>
                                    </div>
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

                                <!-- Terms & Conditions -->
                                <div class="mt-6 flex items-start">
                                    <input id="terms-checkbox" type="checkbox" class="w-4 h-4 mt-1 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="terms-checkbox" class="ms-2 text-sm text-gray-900 dark:text-gray-300">
                                        I confirm that all information provided is accurate and I agree to the terms and conditions of the water service application.
                                    </label>
                                </div>
                            </div>

                            <!-- Step 4 Navigation -->
                            <div class="flex items-center justify-between space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" onclick="prevStep()" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Previous
                                </button>
                                <button type="submit" id="submitButton" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Submit Application
                                </button>
                            </div>
>>>>>>> d495afb1c6251dddf501f93e05fce3c8006270e2
                        </div>
                        <!-- End STEP 4 -->
                    </form>

                </div>

                <!-- Identification Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <i class="fas fa-id-card text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Identification</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Valid government-issued ID</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                ID Type <span class="text-red-500">*</span>
                            </label>
                            <select name="id_type" required 
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select ID Type</option>
                                <option value="National ID">National ID</option>
                                <option value="Driver's License">Driver's License</option>
                                <option value="Passport">Passport</option>
                                <option value="SSS">SSS ID</option>
                                <option value="PhilHealth">PhilHealth ID</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                ID Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="id_number" required placeholder="Enter ID number"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Address Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Service Address</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Where the water service will be installed</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Barangay <span class="text-red-500">*</span>
                            </label>
                            <select name="barangay" required 
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Barangay</option>
                                <option value="Poblacion">Poblacion</option>
                                <option value="San Jose">San Jose</option>
                                <option value="San Roque">San Roque</option>
                                <option value="Central">Central</option>
                                <option value="Maligaya">Maligaya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Purok <span class="text-red-500">*</span>
                            </label>
                            <select name="purok" required 
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Purok</option>
                                <option value="Purok 1">Purok 1</option>
                                <option value="Purok 2">Purok 2</option>
                                <option value="Purok 3">Purok 3</option>
                                <option value="Purok 4">Purok 4</option>
                                <option value="Purok 5">Purok 5</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Landmark <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="landmark" required placeholder="Near church, beside store, etc."
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Area <span class="text-red-500">*</span>
                            </label>
                            <select name="area" id="areaSelect" required 
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Area</option>
                                <option value="Zone A">Zone A</option>
                                <option value="Zone B">Zone B</option>
                                <option value="Zone C">Zone C</option>
                                <option value="Zone D">Zone D</option>
                                <option value="Zone E">Zone E</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Meter Reader <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="meterReader" id="meterReaderInput" required readonly
                                   placeholder="Auto-filled based on area"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Reading Schedule <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="readingSchedule" id="readingScheduleInput" required readonly
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between gap-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        All fields marked with <span class="text-red-500">*</span> are required
                    </div>
                    <div class="flex gap-4">
                        <button type="button" onclick="window.history.back()" 
                                class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" id="submitBtn"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-sm">
                            <i class="fas fa-check mr-2"></i>Submit Application
                        </button>
                    </div>
                </div>
            </form>

            <!-- Success Modal -->
            <div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full p-6 transform transition-all">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Application Submitted!</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4 text-left">
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Customer Code:</span>
                                    <span class="font-mono font-semibold text-gray-900 dark:text-white" id="modalCustomerCode">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Invoice Number:</span>
                                    <span class="font-mono font-semibold text-gray-900 dark:text-white" id="modalInvoiceNumber">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total Amount:</span>
                                    <span class="font-semibold text-blue-600 dark:text-blue-400" id="modalTotalAmount">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Due Date:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white" id="modalDueDate">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-3 mb-4 text-left">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <i class="fas fa-info-circle mr-2"></i>
                                Status: <strong>NEW APPLICATION</strong>
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="printApplicationForm()" 
                                    class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                                <i class="fas fa-print mr-2"></i>Print Form
                            </button>
                            <button onclick="window.location.href='/customer/list'" 
                                    class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                <i class="fas fa-arrow-right mr-2"></i>Go to Customer List
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    @vite('resources/js/data/customer/add-customer.js')
=======
    @push('scripts')
    <script>
        // ============================================
        // STATE MANAGEMENT
        // ============================================
        let currentStep = 1;
        const totalSteps = 4;
        let formData = {};

        document.addEventListener('DOMContentLoaded', function() {
            // Load all dropdown data on page load
            loadProvinces();
            loadTowns();
            loadBarangays();
            loadPuroks();
            loadAccountTypes();
            loadWaterRates();

            // Load saved form data from localStorage
            loadFormFromLocalStorage();

            // Auto-save form data on input change
            document.getElementById('customerApplicationForm').addEventListener('input', saveFormToLocalStorage);

            // Form submission
            document.getElementById('customerApplicationForm').addEventListener('submit', handleFormSubmit);

            // Initialize first step
            showStep(1);
        });

        // ============================================
        // STEP NAVIGATION
        // ============================================
        function nextStep() {
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                    if (currentStep === 4) {
                        populateReviewStep();
                    }
                }
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }

        function showStep(step) {
            // Hide all steps
            for (let i = 1; i <= totalSteps; i++) {
                const stepEl = document.getElementById(`step${i}`);
                if (stepEl) {
                    stepEl.classList.add('hidden');
                }
            }

            // Show current step with animation
            const currentStepEl = document.getElementById(`step${step}`);
            if (currentStepEl) {
                currentStepEl.classList.remove('hidden');
                currentStepEl.classList.add('animate-fadeIn');
            }

            // Update progress indicators
            updateProgressIndicators(step);

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateProgressIndicators(step) {
            for (let i = 1; i <= totalSteps; i++) {
                const indicator = document.getElementById(`step${i}-indicator`);
                if (!indicator) continue;

                if (i < step) {
                    // Completed step - green with checkmark
                    indicator.classList.remove('text-gray-500', 'dark:text-gray-400', 'text-blue-600', 'dark:text-blue-500');
                    indicator.classList.add('text-green-600', 'dark:text-green-500');
                } else if (i === step) {
                    // Current step - blue
                    indicator.classList.remove('text-gray-500', 'dark:text-gray-400', 'text-green-600', 'dark:text-green-500');
                    indicator.classList.add('text-blue-600', 'dark:text-blue-500');
                } else {
                    // Future step - gray
                    indicator.classList.remove('text-blue-600', 'dark:text-blue-500', 'text-green-600', 'dark:text-green-500');
                    indicator.classList.add('text-gray-500', 'dark:text-gray-400');
                }
            }
        }

        // ============================================
        // STEP VALIDATION
        // ============================================
        function validateStep(step) {
            let requiredFields = [];

            if (step === 1) {
                requiredFields = [
                    { id: 'cust_first_name', label: 'First Name' },
                    { id: 'cust_last_name', label: 'Last Name' },
                    { id: 'c_type', label: 'Customer Type' }
                ];
            } else if (step === 2) {
                requiredFields = [
                    { id: 'province', label: 'Province' },
                    { id: 'town', label: 'Town/Municipality' },
                    { id: 'barangay', label: 'Barangay' },
                    { id: 'purok', label: 'Purok' }
                ];
            } else if (step === 3) {
                requiredFields = [
                    { id: 'account_type', label: 'Account Type' },
                    { id: 'water_rate', label: 'Water Rate' }
                ];
            } else if (step === 4) {
                // Check terms checkbox
                const termsCheckbox = document.getElementById('terms-checkbox');
                if (!termsCheckbox.checked) {
                    showToast('error', 'Please accept the terms and conditions to continue.');
                    return false;
                }
                return true;
            }

            const errors = [];
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!element || !element.value) {
                    errors.push(field.label);
                }
            });

            if (errors.length > 0) {
                showToast('error', `Please fill in required fields: ${errors.join(', ')}`);
                return false;
            }

            return true;
        }

        // ============================================
        // REVIEW STEP
        // ============================================
        function populateReviewStep() {
            const reviewContent = document.getElementById('reviewContent');

            // Get form values
            const firstName = document.getElementById('cust_first_name').value;
            const middleName = document.getElementById('cust_middle_name').value || 'N/A';
            const lastName = document.getElementById('cust_last_name').value;
            const customerType = document.getElementById('c_type').value;

            const provinceEl = document.getElementById('province');
            const townEl = document.getElementById('town');
            const barangayEl = document.getElementById('barangay');
            const purokEl = document.getElementById('purok');
            const landmark = document.getElementById('land_mark').value || 'N/A';

            const accountTypeEl = document.getElementById('account_type');
            const waterRateEl = document.getElementById('water_rate');

            reviewContent.innerHTML = `
                <!-- Personal Information -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Personal Information
                    </h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-semibold">${firstName} ${middleName} ${lastName}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer Type</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-semibold">${customerType}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Service Address -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        Service Address
                    </h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Complete Address</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-semibold">
                                Purok ${purokEl.options[purokEl.selectedIndex]?.text || 'N/A'},
                                Barangay ${barangayEl.options[barangayEl.selectedIndex]?.text || 'N/A'},
                                ${townEl.options[townEl.selectedIndex]?.text || 'N/A'},
                                ${provinceEl.options[provinceEl.selectedIndex]?.text || 'N/A'}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Landmark</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">${landmark}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Connection Details -->
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Service Connection Details
                    </h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Type</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-semibold">${accountTypeEl.options[accountTypeEl.selectedIndex]?.text || 'N/A'}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Water Rate Schedule</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-semibold">${waterRateEl.options[waterRateEl.selectedIndex]?.text || 'N/A'}</dd>
                        </div>
                    </dl>
                </div>
            `;
        }

        // ============================================
        // TOAST NOTIFICATIONS
        // ============================================
        function showToast(type, message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const colors = {
                success: 'text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200',
                error: 'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200',
                warning: 'text-orange-500 bg-orange-100 dark:bg-orange-700 dark:text-orange-200',
                info: 'text-blue-500 bg-blue-100 dark:bg-blue-800 dark:text-blue-200'
            };

            const icons = {
                success: '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>',
                error: '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>',
                warning: '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>',
                info: '<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>'
            };

            toast.className = `flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800 transition-all duration-300 transform translate-x-0`;
            toast.innerHTML = `
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${colors[type]} rounded-lg">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        ${icons[type]}
                    </svg>
                </div>
                <div class="ms-3 text-sm font-normal">${message}</div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" onclick="this.parentElement.remove()">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            `;

            container.appendChild(toast);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // ============================================
        // LOCAL STORAGE AUTO-SAVE
        // ============================================
        function saveFormToLocalStorage() {
            const form = document.getElementById('customerApplicationForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            localStorage.setItem('customerApplicationDraft', JSON.stringify(data));
        }

        function loadFormFromLocalStorage() {
            const savedData = localStorage.getItem('customerApplicationDraft');
            if (savedData) {
                const data = JSON.parse(savedData);
                Object.keys(data).forEach(key => {
                    const field = document.querySelector(`[name="${key}"]`);
                    if (field && data[key]) {
                        field.value = data[key];
                    }
                });
                showToast('info', 'Draft form data restored from previous session.');
            }
        }

        function clearFormFromLocalStorage() {
            localStorage.removeItem('customerApplicationDraft');
        }

        // ============================================
        // DATA LOADING FUNCTIONS
        // ============================================

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

            // Final validation
            if (!validateStep(4)) {
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

            try {
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData.entries());

                // Remove terms checkbox from data (not needed in backend)
                delete data['terms-checkbox'];

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
                    // Show success toast
                    showToast('success', `Customer created successfully! Application #: ${result.application.application_number}`);

                    // Clear localStorage draft
                    clearFormFromLocalStorage();

                    // Reset form and go back to step 1
                    e.target.reset();
                    currentStep = 1;
                    showStep(1);

                    // Reload dropdowns
                    setTimeout(() => {
                        loadProvinces();
                        loadTowns();
                        loadBarangays();
                        loadPuroks();
                        loadAccountTypes();
                        loadWaterRates();
                    }, 1000);

                    // Optional: Redirect to customer list after 3 seconds
                    setTimeout(() => {
                        window.location.href = '{{ route("customer.list") }}';
                    }, 3000);
                } else {
                    throw new Error(result.message || 'Failed to submit application');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('error', error.message || 'Failed to submit application. Please try again.');
            } finally {
                // Re-enable button
                submitButton.innerHTML = originalHTML;
                submitButton.disabled = false;
            }
        }
    </script>
    @endpush
>>>>>>> d495afb1c6251dddf501f93e05fce3c8006270e2
</x-app-layout>
