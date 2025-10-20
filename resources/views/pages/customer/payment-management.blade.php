<x-app-layout>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900">

        <div class="flex-1 flex flex-col overflow-auto">
            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-6xl mx-auto flex flex-col space-y-6">

                    <!-- Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <button onclick="history.back()"
                                class="px-3 py-1 border rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                                Back
                            </button>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payment Processing</h1>
                        </div>
                        <span
                            class="inline-block px-3 py-1 bg-green-100 dark:bg-green-700 text-green-800 dark:text-white rounded-full font-medium">
                            Ready for Payment
                        </span>
                    </div>

                    <!-- Side-by-side Cards -->
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Customer Info -->
                        <div class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow p-6">
                            <h2 class="text-lg font-semibold mb-4">Customer Information</h2>
                            <div id="customerInfo" class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
                                Select a customer to see details
                            </div>
                        </div>

                        <!-- Pending Requirements -->
                        <div class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow p-6">
                            <h2 class="text-lg font-semibold mb-4">Pending Requirements</h2>
                            <ul id="requirementsList" class="list-disc list-inside text-gray-600 dark:text-gray-400 text-sm">
                                <li>Select a customer to see pending requirements</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow p-6 space-y-4">
                        <h2 class="text-lg font-semibold mb-2">Payment Details</h2>
                        <input type="hidden" id="customerId">

                        <!-- Customer Type -->
                        <div class="flex gap-4 items-center">
                            <label class="flex items-center gap-1">
                                <input type="radio" name="customerType" value="walkin" checked> Walk-in
                            </label>
                            <label class="flex items-center gap-1">
                                <input type="radio" name="customerType" value="online"> Online
                            </label>
                        </div>

                        <!-- Payment Amount -->
                        <input type="number" id="paymentAmount" placeholder="Amount (₱)"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">

                        <!-- Payment Method -->
                        <select id="paymentMethod"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="credit">Credit Card</option>
                            <option value="debit">Debit Card</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="paypal">PayPal</option>
                            <option value="gcash">GCash</option>
                            <option value="maya">Maya</option>
                        </select>

                        <!-- Reference & Date -->
                        <input type="text" id="paymentReference" placeholder="Reference Number"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <input type="date" id="paymentDate"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">

                        <!-- Description -->
                        <input type="text" id="paymentDescription" placeholder="Description (optional)"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">

                        <!-- Payment Summary -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded text-gray-700 dark:text-gray-200 text-sm">
                            <p><strong>Customer:</strong> <span id="summaryCustomer">N/A</span></p>
                            <p><strong>Total Amount:</strong> ₱<span id="summaryAmount">0.00</span></p>
                            <p><strong>Pending Requirements:</strong> <span id="summaryRequirements">N/A</span></p>
                        </div>

                        <button id="processPayment"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                            Process Payment
                        </button>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const customerId = params.get('customerId');
            const customerName = params.get('customerName');
            const pendingRequirements = params.get('requirements') ? JSON.parse(params.get('requirements')) : [];

            if (customerId && customerName) {
                document.getElementById('customerId').value = customerId;
                document.getElementById('customerInfo').innerHTML = `
                    <div class="space-y-1">
                        <p><strong>${customerName}</strong> (ID: ${customerId})</p>
                        <p>Email: ${params.get('customerEmail') || 'N/A'}</p>
                        <p>Phone: ${params.get('customerPhone') || 'N/A'}</p>
                        <p>Address: ${params.get('customerAddress') || 'N/A'}</p>
                    </div>
                `;

                const reqList = document.getElementById('requirementsList');
                reqList.innerHTML = pendingRequirements.length
                    ? pendingRequirements.map(r => `<li>${r}</li>`).join('')
                    : '<li>No pending requirements</li>';

                document.getElementById('summaryCustomer').textContent = customerName;
                document.getElementById('summaryRequirements').textContent = pendingRequirements.join(', ') || 'None';
            }

            const paymentAmountInput = document.getElementById('paymentAmount');
            paymentAmountInput.addEventListener('input', () => {
                document.getElementById('summaryAmount').textContent = parseFloat(paymentAmountInput.value || 0).toFixed(2);
            });

            document.getElementById('processPayment').addEventListener('click', () => {
                const amount = paymentAmountInput.value;
                const method = document.getElementById('paymentMethod').value;
                if (!amount || !method) {
                    alert('Please fill in all required fields!');
                    return;
                }
                alert(`Payment of ₱${amount} processed successfully for ${customerName}`);
            });
        });
    </script>
</x-app-layout>
