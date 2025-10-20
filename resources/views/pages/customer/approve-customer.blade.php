<x-app-layout>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900">


        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-auto">

            <main class="flex-1 p-6 overflow-auto">
                <div class="max-w-7xl mx-auto flex flex-col space-y-6" x-data="approvalPage()">

                    <!-- Page Header -->
                    <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                                Eligible for Approval (<span x-text="filteredCustomers.length"></span>)
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">
                                Customers pending final approval decision
                            </p>
                        </div>

                        <!-- Search & Add Customer -->
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Search Box -->
                            <div class="flex items-center border border-gray-300 rounded-md px-3 py-2 w-72 bg-white dark:bg-gray-700 shadow-sm focus-within:ring-2 focus-within:ring-blue-400 transition">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                                </svg>
                                <input type="text" placeholder="Search by ID, name, location, or invoice..."
                                    class="outline-none w-full text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700"
                                    x-model="searchQuery">
                            </div>

                            <!-- Add Customer Button -->
                            <a href="{{ route('customer.add') }}"
                                class="inline-flex items-center gap-2 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-medium shadow-sm transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add Customer
                            </a>
                        </div>
                    </div>

                    <!-- Alerts -->
                    <template x-if="filteredCustomers.length === 0 && searchQuery !== ''">
                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300 px-4 py-3 rounded-md mb-4">
                            No eligible customers found matching your search. Try different keywords.
                        </div>
                    </template>
                    <template x-if="filteredCustomers.length === 0 && searchQuery === ''">
                        <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded-md mb-4">
                            No customers are currently eligible for approval. All applications have been processed.
                        </div>
                    </template>

                    <!-- Table -->
                    <div class="overflow-x-auto border rounded-md shadow-sm bg-white dark:bg-gray-800">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">ID</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Customer Name</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Location</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Date Applied</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Payment Method</th>
                                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-300">Invoice</th>
                                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="customer in filteredCustomers" :key="customer.ConsuId">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition">
                                        <td class="px-4 py-2 font-mono text-sm text-gray-800 dark:text-gray-200" x-text="customer.ConsuId"></td>
                                        <td class="px-4 py-2">
                                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="customer.PersonName"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">ID: <span x-text="customer.ConsuId"></span></div>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300" x-text="customer.LocaName"></td>
                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300" x-text="customer.DateApplied"></td>
                                        <td class="px-4 py-2 text-sm">
                                            <span
                                                :class="{
                                                    'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200': customer.PaymentMethod === 'Credit Card',
                                                    'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200': customer.PaymentMethod === 'Bank Transfer',
                                                    'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200': customer.PaymentMethod === 'Cash',
                                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': !customer.PaymentMethod
                                                }"
                                                class="px-2 py-1 rounded text-xs font-medium"
                                                x-text="customer.PaymentMethod || '--'">
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button @click.stop="viewInvoice(customer)"
                                                class="text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-500 transition">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 17v-6h6v6m2 4H7a2 2 0 01-2-2V7a2 2 0 012-2h5l2 2h5a2 2 0 012 2v10a2 2 0 01-2 2z" />
                                                </svg>
                                            </button>
                                        </td>
                                        <td class="px-4 py-2 flex justify-center gap-2">
                                            <button @click.stop="approveCustomer(customer)"
                                                class="px-2 py-1 text-white bg-green-500 hover:bg-green-600 text-xs rounded flex items-center gap-1 transition">
                                                Approve
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button @click.stop="declineCustomer(customer)"
                                                class="px-2 py-1 text-red-500 border border-red-500 hover:bg-red-50 dark:hover:bg-red-800 text-xs rounded flex items-center gap-1 transition">
                                                Decline
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-4 flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Show</span>
                            <select x-model="pageSize" class="text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                            </select>
                        </div>
                        <div class="flex gap-2 items-center">
                            <button @click="prevPage()" :disabled="pageIndex === 0"
                                class="px-3 py-1 border rounded text-sm hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 transition">Prev</button>
                            <button @click="nextPage()" :disabled="(pageIndex + 1) * pageSize >= filteredCustomers.length"
                                class="px-3 py-1 border rounded text-sm hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 transition">Next</button>
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Page <span x-text="pageIndex + 1"></span> of <span x-text="Math.ceil(filteredCustomers.length / pageSize)"></span>
                        </div>
                    </div>

                    <!-- Invoice Modal -->
                    <div x-show="invoiceModalOpen" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50" x-transition>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 relative shadow-lg">
                            <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">Invoice Details</h2>
                            <p><strong>Customer:</strong> <span x-text="selectedCustomer?.PersonName" class="text-gray-700 dark:text-gray-200"></span></p>
                            <p><strong>Invoice:</strong> <span x-text="selectedCustomer?.InvoiceNumber" class="text-gray-700 dark:text-gray-200"></span></p>
                            <p><strong>Payment Method:</strong> <span x-text="selectedCustomer?.PaymentMethod" class="text-gray-700 dark:text-gray-200"></span></p>
                            <p><strong>Date Applied:</strong> <span x-text="selectedCustomer?.DateApplied" class="text-gray-700 dark:text-gray-200"></span></p>
                            <button @click="invoiceModalOpen = false"
                                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 text-2xl font-bold">&times;</button>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- Alpine.js Approval Logic -->
    <script>
        function approvalPage() {
            return {
                searchQuery: '',
                pageSize: 10,
                pageIndex: 0,
                invoiceModalOpen: false,
                selectedCustomer: null,
                customers: @json($tableData ?? []),
                get filteredCustomers() {
                    let data = this.customers.filter(c =>
                        c.PersonName.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        c.LocaName.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        c.ConsuId.toString().toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        (c.InvoiceNumber ?? '').toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                    return data.slice(this.pageIndex * this.pageSize, (this.pageIndex + 1) * this.pageSize);
                },
                approveCustomer(customer) { alert(`Customer ${customer.PersonName} approved!`); },
                declineCustomer(customer) { alert(`Customer ${customer.PersonName} declined!`); },
                viewInvoice(customer) { this.selectedCustomer = customer; this.invoiceModalOpen = true; },
                prevPage() { if (this.pageIndex > 0) this.pageIndex--; },
                nextPage() { if ((this.pageIndex + 1) * this.pageSize < this.customers.length) this.pageIndex++; }
            }
        }
    </script>
</x-app-layout>
