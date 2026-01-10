<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Create Payment</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Process a new payment for customer</p>
                    </div>
                    <button onclick="history.back()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </button>
                </div>
            </div>

            <div x-data="createPayment()" class="space-y-6">
                
                <!-- Customer Information Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Name</label>
                            <input type="text" x-model="customerName" readonly 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer ID</label>
                            <input type="text" x-model="customerId" readonly 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Payment Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">₱</span>
                                <input type="number" x-model="amount" step="0.01" min="0" required
                                    class="w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method *</label>
                            <select x-model="paymentMethod" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="credit">Credit Card</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference Number</label>
                            <input type="text" x-model="referenceNumber" placeholder="Optional reference number"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Date *</label>
                            <input type="date" x-model="paymentDate" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                        <textarea x-model="notes" rows="3" placeholder="Additional notes or comments"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>

                <!-- Payment Summary Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Payment Amount:</span>
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(amount || 0)">₱ 0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Processing Fee:</span>
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(processingFee)">₱ 0.00</span>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between text-lg">
                            <span class="font-semibold text-gray-900 dark:text-white">Total Amount:</span>
                            <span class="font-bold text-green-600 dark:text-green-400" x-text="formatCurrency(totalAmount)">₱ 0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="history.back()" 
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="button" @click="processPayment()" :disabled="!isFormValid"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg disabled:cursor-not-allowed">
                        <span x-show="!processing">Process Payment</span>
                        <span x-show="processing" class="flex items-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createPayment() {
            return {
                customerId: '',
                customerName: '',
                amount: '',
                paymentMethod: '',
                referenceNumber: '',
                paymentDate: new Date().toISOString().split('T')[0],
                notes: '',
                processing: false,
                
                get processingFee() {
                    const amount = parseFloat(this.amount) || 0;
                    if (this.paymentMethod === 'credit') return amount * 0.035; // 3.5% for credit card
                    if (this.paymentMethod === 'gcash' || this.paymentMethod === 'maya') return amount * 0.02; // 2% for e-wallets
                    return 0; // No fee for cash and bank transfer
                },
                
                get totalAmount() {
                    return (parseFloat(this.amount) || 0) + this.processingFee;
                },
                
                get isFormValid() {
                    return this.amount && this.paymentMethod && this.paymentDate && !this.processing;
                },
                
                init() {
                    // Get customer info from URL parameters
                    const urlParams = new URLSearchParams(window.location.search);
                    this.customerId = urlParams.get('customerId') || '';
                    this.customerName = urlParams.get('customerName') || '';
                },
                
                formatCurrency(amount) {
                    return '₱ ' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
                },
                
                async processPayment() {
                    if (!this.isFormValid) return;
                    
                    this.processing = true;
                    
                    // Simulate payment processing
                    await new Promise(resolve => setTimeout(resolve, 2000));
                    
                    // Create payment record (mock)
                    const paymentData = {
                        id: 'PAY-' + Date.now(),
                        customerId: this.customerId,
                        customerName: this.customerName,
                        amount: parseFloat(this.amount),
                        paymentMethod: this.paymentMethod,
                        referenceNumber: this.referenceNumber,
                        paymentDate: this.paymentDate,
                        notes: this.notes,
                        processingFee: this.processingFee,
                        totalAmount: this.totalAmount,
                        status: 'completed',
                        createdAt: new Date().toISOString()
                    };
                    
                    // Store in localStorage for demo purposes
                    const payments = JSON.parse(localStorage.getItem('payments') || '[]');
                    payments.push(paymentData);
                    localStorage.setItem('payments', JSON.stringify(payments));
                    
                    this.processing = false;
                    
                    // Show success message and redirect
                    alert('Payment processed successfully! Redirecting to customer approval page...');
                    
                    // Redirect to customer approval page
                    window.location.href = '/customer/approve-customer';
                }
            }
        }
    </script>
</x-app-layout>