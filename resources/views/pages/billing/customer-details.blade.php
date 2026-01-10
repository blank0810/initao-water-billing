<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <x-ui.page-header 
                title="Customer Billing Details" 
                subtitle="View detailed billing information and ledger history for this customer account">
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('billing.main') }}" icon="fas fa-arrow-left">
                        Back to Billing
                    </x-ui.button>
                    <x-ui.button variant="secondary" icon="fas fa-edit" onclick="editCustomer()">
                        Edit Customer
                    </x-ui.button>
                    <x-ui.button variant="primary" icon="fas fa-download" onclick="downloadCustomerBill()">
                        Download Bill
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Customer Profile Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-user-circle mr-2"></i>Customer Profile
                    </h3>
                    <div id="overdueBadge" class="hidden inline-flex items-center px-3 py-1 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-full text-sm font-medium">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Overdue by <span id="overdueDays">0</span> day(s)
                    </div>
                </div>
                <div class="flex items-start space-x-6 mb-6">
                    <div class="h-16 w-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                        <span id="customerInitials">JD</span>
                    </div>
                    <div class="flex-1">
                        <h4 id="customerName" class="text-xl font-bold text-gray-900 dark:text-white mb-3">Juan Dela Cruz</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-id-card text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-500 dark:text-gray-400 w-24">Account No:</span>
                                <span id="customerId" class="text-gray-900 dark:text-white font-mono font-medium">ACC-2024-001</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-tag text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-500 dark:text-gray-400 w-24">Rate Class:</span>
                                <span id="customerClass" class="text-gray-900 dark:text-white font-medium">Residential</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-tachometer-alt text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-500 dark:text-gray-400 w-24">Meter No:</span>
                                <span id="meterNo" class="text-gray-900 dark:text-white font-mono font-medium">MTR-001</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-circle text-green-500 w-5 mr-3"></i>
                                <span class="text-gray-500 dark:text-gray-400 w-24">Status:</span>
                                <span class="inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full text-xs font-medium">Active</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-500 dark:text-gray-400 w-24">Address:</span>
                                <span id="customerAddress" class="text-gray-900 dark:text-white">Purok 1, Poblacion</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 w-5 mr-3"></i>
                                <span class="text-gray-500 dark:text-gray-400 w-24">Contact:</span>
                                <span id="customerPhone" class="text-gray-900 dark:text-white">09123456789</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Bill Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Amount Due</h3>
                        <i class="fas fa-file-invoice-dollar text-blue-500 text-2xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">₱739.20</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Billing Period: Jan 2024</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Consumption</h3>
                        <i class="fas fa-tint text-green-500 text-2xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">25 m³</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This Billing Period</p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Ledger Balance</h3>
                        <i class="fas fa-book text-purple-500 text-2xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">₱0.00</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Outstanding Balance</p>
                </div>
            </div>

            <!-- Rate Schedule Applied -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-calculator mr-2"></i>Applied Rate Schedule
                </h3>
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900 rounded-lg p-4 mb-4">
                    <div class="text-sm text-blue-800 dark:text-blue-300">
                        <strong>Rate Structure:</strong> Residential Standard | <strong>Billing Period:</strong> January 2024 | <strong>Status:</strong> <span class="inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded text-xs font-medium">Posted & Locked</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tier</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Range (m³)</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Rate/m³</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Consumption</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Tier 1</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">0 - 10</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">₱15.00</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">10 m³</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-white">₱150.00</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Tier 2</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">11 - 20</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">₱25.00</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">10 m³</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-white">₱250.00</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Tier 3</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">21 - 30</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">₱35.00</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">5 m³</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-white">₱175.00</td>
                            </tr>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <td colspan="4" class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-white">Subtotal (Consumption)</td>
                                <td class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-white">₱575.00</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300">Fixed Charges</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">₱85.00</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-sm text-right text-gray-700 dark:text-gray-300">VAT (12%)</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">₱79.20</td>
                            </tr>
                            <tr class="bg-blue-50 dark:bg-blue-900/20">
                                <td colspan="4" class="px-4 py-3 text-sm text-right font-bold text-gray-900 dark:text-white">TOTAL AMOUNT DUE</td>
                                <td class="px-4 py-3 text-lg text-right font-bold text-blue-600 dark:text-blue-400">₱739.20</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Customer Ledger History -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-book mr-2"></i>Customer Ledger History
                </h3>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-900 rounded-lg p-4 mb-4">
                    <div class="text-sm text-yellow-800 dark:text-yellow-300">
                        <strong>COA Compliance:</strong> This ledger history is permanent and non-deletable as required by Commission on Audit (COA) regulations. All transactions are recorded and cannot be modified.
                    </div>
                </div>
                
                @php
                    $ledgerData = [
                        [
                            'date' => '2024-01-05',
                            'reference' => '<span class="font-mono text-blue-600 dark:text-blue-400">BILL-2024-001</span>',
                            'description' => 'Bill Generated - January 2024',
                            'debit' => '<span class="text-red-600 font-semibold">₱739.20</span>',
                            'credit' => '-',
                            'balance' => '<span class="font-semibold">₱739.20</span>'
                        ],
                        [
                            'date' => '2024-01-15',
                            'reference' => '<span class="font-mono text-green-600 dark:text-green-400">PAY-2024-001</span>',
                            'description' => 'Payment Received - Check #12345',
                            'debit' => '-',
                            'credit' => '<span class="text-green-600 font-semibold">₱500.00</span>',
                            'balance' => '<span class="font-semibold">₱239.20</span>'
                        ]
                    ];

                    $ledgerHeaders = [
                        ['key' => 'date', 'label' => 'Date', 'html' => false],
                        ['key' => 'reference', 'label' => 'Reference', 'html' => true],
                        ['key' => 'description', 'label' => 'Description', 'html' => false],
                        ['key' => 'debit', 'label' => 'Debit', 'html' => true],
                        ['key' => 'credit', 'label' => 'Credit', 'html' => true],
                        ['key' => 'balance', 'label' => 'Balance', 'html' => true],
                    ];
                @endphp
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                @foreach($ledgerHeaders as $header)
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ $header['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($ledgerData as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    @foreach($ledgerHeaders as $header)
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                            @if($header['html'])
                                                {!! $row[$header['key']] !!}
                                            @else
                                                {{ $row[$header['key']] }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('js/data/billing/customer-profile-data.js') }}"></script>
    
    <script>
    // Load customer data on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Get query parameters from URL
        const params = new URLSearchParams(window.location.search);
        const customerName = params.get('customer');
        const accountNo = params.get('account');

        let customer = null;

        // Try to find customer by name first
        if (customerName) {
            customer = getCustomerByName(customerName);
        }
        
        // If not found by name, try by account number
        if (!customer && accountNo) {
            customer = getCustomerByAccountNo(accountNo);
        }

        // If customer found, populate the page with their data
        if (customer) {
            document.getElementById('customerName').textContent = customer.name;
            document.getElementById('customerId').textContent = customer.account_no;
            document.getElementById('customerClass').textContent = customer.rate_class;
            document.getElementById('meterNo').textContent = customer.meter_no;
            document.getElementById('customerAddress').textContent = customer.address;
            document.getElementById('customerPhone').textContent = customer.contact;
            document.getElementById('customerInitials').textContent = customer.initials;
        }
    });

    function downloadCustomerBill() {
        alert('Downloading bill for customer');
        // Implementation will trigger PDF download
    }
    
    function editCustomer() {
        window.location.href = '/customer/edit/';
    }
    </script>
</x-app-layout>
