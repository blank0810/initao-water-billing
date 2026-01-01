<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Process Payment</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Complete payment transaction</p>
                </div>
                <button onclick="history.back()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </button>
            </div>

            <div x-data="paymentForm()" class="space-y-6">
                
                <!-- Status-Driven Content -->
                <template x-if="customerStatus === 'Applicant'">
                    @include('pages.payment.payment-processing.applicant')
                </template>

                <template x-if="customerStatus === 'Active / Connected'">
                    @include('pages.payment.payment-processing.active')
                </template>

                <template x-if="customerStatus === 'Delinquent'">
                    @include('pages.payment.payment-processing.delinquent')
                </template>

                <template x-if="customerStatus === 'Overdue'">
                    @include('pages.payment.payment-processing.overdue')
                </template>

                <template x-if="customerStatus === 'Disconnected'">
                    @include('pages.payment.payment-processing.disconnected')
                </template>

                <template x-if="customerStatus === 'Reconnection Pending'">
                    @include('pages.payment.payment-processing.reconnection-pending')
                </template>

                <template x-if="customerStatus === 'Suspended'">
                    @include('pages.payment.payment-processing.suspended')
                </template>

                <template x-if="!['Applicant', 'Active / Connected', 'Delinquent', 'Overdue', 'Disconnected', 'Reconnection Pending', 'Suspended'].includes(customerStatus)">
                    @include('pages.payment.payment-processing.default')
                </template>


                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="history.back()" class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition">
                        Cancel
                    </button>
                    <button type="button" @click="showConfirmation()" :disabled="!isValid" :class="isValid ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'" class="flex-1 px-6 py-3 text-white rounded-lg font-medium transition">
                        <i class="fas fa-check-circle mr-2"></i>Process Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <x-ui.payment.confirmation-modal />

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('paymentData', {});
        });

        function paymentForm() {
            return {
                customerStatus: '',
                customerName: '',
                customerCode: '',
                accountNo: '',
                meterNo: '',
                address: '',
                applicationType: 'New Water Service Connection',
                applicationDate: '',
                charges: [],
                availablePurposes: [],
                selectedPurpose: '',
                customAmount: 0,
                paymentMethod: '',
                paymentDate: new Date().toISOString().split('T')[0],
                referenceNumber: '',
                remarks: '',

                get totalCharges() {
                    return this.charges.reduce((sum, c) => sum + c.amount, 0);
                },

                get isValid() {
                    return this.selectedPurpose && this.paymentMethod && this.paymentDate && this.totalCharges > 0;
                },

                init() {
                    const stored = sessionStorage.getItem('selectedCustomer');
                    if (stored) {
                        const customer = JSON.parse(stored);
                        this.customerStatus = customer.status || 'Applicant';
                        this.customerName = `${customer.cust_first_name} ${customer.cust_last_name}`;
                        this.customerCode = customer.customer_code;
                        this.accountNo = customer.account_no || 'N/A';
                        this.meterNo = customer.meter_no || 'N/A';
                        this.address = customer.address || 'N/A';
                        this.applicationDate = customer.create_date ? new Date(customer.create_date).toLocaleDateString() : new Date().toLocaleDateString();
                        
                        this.loadAvailablePurposes();
                        this.loadChargesByStatus();
                    }

                    window.addEventListener('payment-confirmed', () => {
                        this.processPayment();
                    });
                },

                loadAvailablePurposes() {
                    const purposeMap = {
                        'Applicant': [
                            { value: 'application_fee', label: 'Application Processing Fee' },
                            { value: 'installation_fee', label: 'Installation & Inspection Fee' },
                            { value: 'full_payment', label: 'Full Payment (All Charges)' }
                        ],
                        'Active / Connected': [
                            { value: 'current_bill', label: 'Current Bill Payment' },
                            { value: 'full_settlement', label: 'Full Settlement' }
                        ],
                        'Delinquent': [
                            { value: 'overdue_settlement', label: 'Overdue Settlement' },
                            { value: 'partial_payment', label: 'Partial Payment' }
                        ],
                        'Overdue': [
                            { value: 'overdue_settlement', label: 'Overdue Settlement' },
                            { value: 'partial_payment', label: 'Partial Payment' }
                        ],
                        'Disconnected': [
                            { value: 'reconnection_payment', label: 'Reconnection Payment (Full Settlement)' }
                        ],
                        'Reconnection Pending': [
                            { value: 'reconnection_settlement', label: 'Reconnection Settlement' }
                        ],
                        'Suspended': [
                            { value: 'eligible_settlement', label: 'Eligible Settlement' }
                        ]
                    };
                    this.availablePurposes = purposeMap[this.customerStatus] || [
                        { value: 'general_payment', label: 'General Payment' }
                    ];
                },

                loadChargesByStatus() {
                    if (this.customerStatus === 'Applicant') {
                        this.charges = [
                            { name: '1. Registration Fee', amount: 50.00 },
                            { name: '2. Installation Fee', amount: 200.00 },
                            { name: '3. Tapping Fee', amount: 50.00 },
                            { name: '4. Excavation Fee', amount: 50.00 }
                        ];
                        this.selectedPurpose = 'full_payment';
                    }
                },

                loadPurposeCharges() {
                    if (this.selectedPurpose === 'custom') {
                        this.charges = [];
                        return;
                    }

                    const chargesMap = {
                        'application_fee': [{ name: 'Application Processing Fee', amount: 100.00 }],
                        'installation_fee': [{ name: 'Installation & Inspection Fee', amount: 300.00 }],
                        'full_payment': [
                            { name: 'Registration Fee', amount: 50.00 },
                            { name: 'Installation Fee', amount: 200.00 },
                            { name: 'Tapping Fee', amount: 50.00 },
                            { name: 'Excavation Fee', amount: 50.00 }
                        ],
                        'current_bill': [{ name: 'Water Consumption', amount: 420.50 }],
                        'full_settlement': [
                            { name: 'Water Consumption', amount: 420.50 },
                            { name: 'Late Payment Penalty', amount: 30.00 }
                        ],
                        'overdue_settlement': [
                            { name: 'Previous Balance', amount: 850.00 },
                            { name: 'Current Bill', amount: 420.50 },
                            { name: 'Late Payment Penalty', amount: 127.50 },
                            { name: 'Surcharge', amount: 50.00 }
                        ],
                        'partial_payment': [{ name: 'Partial Payment', amount: 500.00 }],
                        'reconnection_payment': [
                            { name: 'Outstanding Balance', amount: 1200.00 },
                            { name: 'Reconnection Fee', amount: 500.00 },
                            { name: 'Service Restoration Fee', amount: 200.00 },
                            { name: 'Administrative Fee', amount: 100.00 }
                        ],
                        'reconnection_settlement': [
                            { name: 'Reconnection Fee', amount: 500.00 },
                            { name: 'Past Due Balance', amount: 3000.00 }
                        ],
                        'eligible_settlement': [{ name: 'Eligible Balance Settlement', amount: 900.00 }],
                        'general_payment': [{ name: 'Payment', amount: 0.00 }]
                    };

                    this.charges = chargesMap[this.selectedPurpose] || [];
                },

                updateCustomCharges() {
                    const amount = parseFloat(this.customAmount) || 0;
                    this.charges = [{ name: 'Custom Payment', amount: amount }];
                },

                formatCurrency(amount) {
                    return '₱ ' + parseFloat(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                getPurposeLabel() {
                    const purpose = this.availablePurposes.find(p => p.value === this.selectedPurpose);
                    return purpose ? purpose.label : this.selectedPurpose;
                },

                getMethodLabel() {
                    const labels = {
                        'cash': 'Cash',
                        'check': 'Check',
                        'bank_transfer': 'Bank Transfer',
                        'gcash': 'GCash',
                        'maya': 'Maya',
                        'credit_card': 'Credit Card'
                    };
                    return labels[this.paymentMethod] || this.paymentMethod;
                },

                showConfirmation() {
                    if (!this.isValid) return;
                    
                    Alpine.store('paymentData', {
                        customerName: this.customerName,
                        customerCode: this.customerCode,
                        address: this.address,
                        purposeLabel: this.getPurposeLabel(),
                        methodLabel: this.getMethodLabel(),
                        paymentDate: this.paymentDate,
                        referenceNumber: this.referenceNumber,
                        remarks: this.remarks,
                        charges: this.charges,
                        totalAmount: this.totalCharges
                    });
                    
                    this.$dispatch('open-payment-confirmation');
                },

                async processPayment() {
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    
                    alert(`Payment of ${this.formatCurrency(this.totalCharges)} processed successfully!`);
                    sessionStorage.removeItem('selectedCustomer');
                    window.location.href = '/customer/approve-customer';
                }
            }
        }
    </script>
</x-app-layout>
