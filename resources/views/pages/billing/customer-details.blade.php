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
            <x-ui.card class="mb-6">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-user-circle mr-2"></i>Customer Profile
                    </h3>
                    <x-ui.badge id="overdueBadge" color="red" class="hidden">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Overdue by <span id="overdueDays">0</span> day(s)
                    </x-ui.badge>
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
                                <x-ui.status-badge id="customerStatus" status="Active" />
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
            </x-ui.card>

            <!-- Current Bill Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <x-ui.stat-card
                    title="Current Amount Due"
                    value="₱739.20"
                    subtitle="Billing Period: Jan 2024"
                    icon="file-invoice-dollar"
                    color="blue"
                    id="currentAmount"
                />
                
                <x-ui.stat-card
                    title="Total Consumption"
                    value="25 m³"
                    subtitle="This Billing Period"
                    icon="tint"
                    color="green"
                    id="totalConsumption"
                />
                
                <x-ui.stat-card
                    title="Ledger Balance"
                    value="₱0.00"
                    subtitle="Outstanding Balance"
                    icon="book"
                    color="purple"
                    id="ledgerBalance"
                />
            </div>

            <!-- Rate Schedule Applied -->
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-calculator mr-2"></i>Applied Rate Schedule
                </h3>
                <x-ui.alert type="info" class="mb-4">
                    <div class="text-sm">
                        <strong>Rate Structure:</strong> Residential Standard | <strong>Billing Period:</strong> January 2024 | <strong>Status:</strong> <x-ui.badge color="green">Posted & Locked</x-ui.badge>
                    </div>
                </x-ui.alert>
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
            </x-ui.card>

            <!-- Customer Ledger History -->
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-book mr-2"></i>Customer Ledger History
                </h3>
                <x-ui.alert type="warning" class="mb-4">
                    <div class="text-sm">
                        <strong>COA Compliance:</strong> This ledger history is permanent and non-deletable as required by Commission on Audit (COA) regulations. All transactions are recorded and cannot be modified.
                    </div>
                </x-ui.alert>
                
                <x-ui.action-functions 
                    searchPlaceholder="Search ledger entries..."
                    filterLabel="All Types"
                    :filterOptions="[
                        ['value' => 'bill', 'label' => 'Bills'],
                        ['value' => 'payment', 'label' => 'Payments'],
                        ['value' => 'adjustment', 'label' => 'Adjustments']
                    ]"
                    :showDateFilter="true"
                    :showExport="true"
                    tableId="customerLedgerTable"
                />
                
                @php
                    $ledgerHeaders = [
                        ['key' => 'date', 'label' => 'Date', 'html' => false],
                        ['key' => 'reference', 'label' => 'Reference', 'html' => true],
                        ['key' => 'description', 'label' => 'Description', 'html' => false],
                        ['key' => 'debit', 'label' => 'Debit', 'html' => true],
                        ['key' => 'credit', 'label' => 'Credit', 'html' => true],
                        ['key' => 'balance', 'label' => 'Balance', 'html' => true],
                    ];
                @endphp
                
                <x-table
                    id="customerLedgerTable"
                    :headers="$ledgerHeaders"
                    :data="[]"
                    :searchable="false"
                    :paginated="true"
                    :pageSize="20"
                    :actions="false"
                />
            </x-ui.card>

        </div>
    </div>

    @vite(['resources/js/data/billing/customer-ledger-data.js'])

    <script>
    const customerId = {{ $customer_id ?? 1 }};
    
    function downloadCustomerBill() {
        alert('Downloading bill for customer: ' + customerId);
        // Implementation will trigger PDF download
    }
    
    function editCustomer() {
        window.location.href = '/customer/edit/' + customerId;
    }
    </script>
</x-app-layout>
