<x-app-layout>
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">

        <div class="flex-1 flex flex-col overflow-auto">

            <!-- Main Content -->
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-4xl mx-auto">
                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Customer Application</h1>
                        <p class="text-gray-600 dark:text-gray-400">Fill out the form below to register a new customer</p>
                    </div>

                    <!-- Application Form -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <form id="customerApplicationForm" class="space-y-6">
                            @csrf

                            <!-- Personal Information Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Last Name -->
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Last Name *
                                        </label>
                                        <input
                                            type="text"
                                            id="last_name"
                                            name="last_name"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200"
                                            placeholder="Enter last name"
                                        >
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Required field</p>
                                    </div>

                                    <!-- First Name -->
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            First Name *
                                        </label>
                                        <input
                                            type="text"
                                            id="first_name"
                                            name="first_name"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200"
                                            placeholder="Enter first name"
                                        >
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Required field</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Address Information</h3>

                                <!-- Landmark -->
                                <div class="mb-4">
                                    <label for="landmark" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Landmark
                                    </label>
                                    <input
                                        type="text"
                                        id="landmark"
                                        name="landmark"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200"
                                        placeholder="Enter nearby landmark or reference point"
                                    >
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional - helps with location identification</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Purok Dropdown -->
                                    <div>
                                        <label for="purok" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Purok *
                                        </label>
                                        <select
                                            id="purok"
                                            name="purok"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200"
                                        >
                                            <option value="">Select Purok</option>
                                            <option value="purok-1">Purok 1</option>
                                            <option value="purok-2">Purok 2</option>
                                            <option value="purok-3">Purok 3</option>
                                            <option value="purok-4">Purok 4</option>
                                            <option value="purok-5">Purok 5</option>
                                            <option value="purok-6">Purok 6</option>
                                            <option value="purok-7">Purok 7</option>
                                            <option value="purok-8">Purok 8</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select the purok</p>
                                    </div>

                                    <!-- Barangay Dropdown -->
                                    <div>
                                        <label for="barangay" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Barangay *
                                        </label>
                                        <select
                                            id="barangay"
                                            name="barangay"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200"
                                        >
                                            <option value="">Select Barangay</option>
                                            <option value="bagong-silang">Bagong Silang</option>
                                            <option value="bambang">Bambang</option>
                                            <option value="batasan">Batasan</option>
                                            <option value="central">Central</option>
                                            <option value="dampol">Dampol</option>
                                            <option value="dona-aurora">Doña Aurora</option>
                                            <option value="maligaya">Maligaya</option>
                                            <option value="manggahan">Manggahan</option>
                                            <option value="masagana">Masagana</option>
                                            <option value="poblacion">Poblacion</option>
                                            <option value="san-antonio">San Antonio</option>
                                            <option value="san-jose">San Jose</option>
                                            <option value="san-roque">San Roque</option>
                                            <option value="santa-cruz">Santa Cruz</option>
                                            <option value="santo-cristo">Santo Cristo</option>
                                            <option value="santo-nino">Santo Niño</option>
                                            <option value="sinipian">Sinipian</option>
                                            <option value="tabon">Tabon</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select the barangay</p>
                                    </div>
                                </div>
                            </div>

                            <!-- System Information Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Information</h3>

                                <!-- Creation Date -->
                                <div class="max-w-xs">
                                    <label for="creation_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Creation Date
                                    </label>
                                    <input
                                        type="text"
                                        id="creation_date"
                                        name="creation_date"
                                        readonly
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 dark:text-gray-300 cursor-not-allowed"
                                        value="{{ now()->format('F d, Y') }}"
                                    >
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Automatically generated</p>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button
                                    type="button"
                                    class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200 font-medium"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium shadow-sm"
                                >
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
                                    <li>Ensure all information is accurate before submitting</li>
                                    <li>Application will be processed within 3-5 business days</li>
                                    <li>You will receive a confirmation email once processed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('customerApplicationForm');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Basic form validation
                const lastName = document.getElementById('last_name').value.trim();
                const firstName = document.getElementById('first_name').value.trim();
                const purok = document.getElementById('purok').value;
                const barangay = document.getElementById('barangay').value;

                if (!lastName || !firstName || !purok || !barangay) {
                    alert('Please fill in all required fields.');
                    return;
                }

                // Simulate form submission
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.textContent;

                submitButton.textContent = 'Submitting...';
                submitButton.disabled = true;

                // Simulate API call
                setTimeout(() => {
                    alert('Application submitted successfully!');
                    form.reset();
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;

                    // Reset creation date to current date
                    document.getElementById('creation_date').value = new Date().toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }, 1500);
            });
        });
    </script>
</x-app-layout>
