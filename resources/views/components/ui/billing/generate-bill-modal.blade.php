<!-- Generate Water Bill Modal -->
<div id="generateBillModal" x-data="generateBillModalData()" x-cloak class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-file-invoice-dollar text-blue-600 mr-2"></i>Generate Water Bill
            </h3>
            <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600 dark:text-gray-400">Loading data...</span>
        </div>

        <div x-show="!loading" class="space-y-5">
            <!-- Account Search and Selection -->
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium text-gray-900 dark:text-white">
                        <i class="fas fa-user-circle mr-2 text-blue-600"></i>Account Selection
                    </h4>
                    <template x-if="selectedConnection">
                        <button @click="clearSelection()" class="text-xs text-red-600 hover:text-red-800 dark:text-red-400">
                            <i class="fas fa-times mr-1"></i>Clear Selection
                        </button>
                    </template>
                </div>

                <!-- Selected Account Display -->
                <template x-if="selectedConnection">
                    <div class="p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg mb-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-green-100 dark:bg-green-800 rounded-full">
                                    <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white" x-text="selectedConnection.customer_name"></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-mono" x-text="selectedConnection.account_no"></span>
                                        <span class="mx-1">|</span>
                                        <span x-text="selectedConnection.account_type"></span>
                                        <span class="mx-1">|</span>
                                        <span x-text="selectedConnection.barangay"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right text-sm">
                                <p class="text-gray-500 dark:text-gray-400">Meter: <span class="font-mono font-medium text-gray-900 dark:text-white" x-text="selectedConnection.meter_serial || 'N/A'"></span></p>
                                <p class="text-gray-500 dark:text-gray-400">Install Read: <span class="font-mono font-medium text-gray-900 dark:text-white" x-text="parseFloat(selectedConnection.install_read || 0).toFixed(3)"></span></p>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Search Bar (show when no selection) -->
                <template x-if="!selectedConnection">
                    <div>
                        <div class="relative mb-3">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" x-model="searchQuery" @input.debounce.300ms="searchAccounts()"
                                placeholder="Search by account number, name, or address..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <template x-if="searching">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                </div>
                            </template>
                        </div>

                        <!-- Results Table -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <div class="max-h-64 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Account</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Customer</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Type</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Location</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Meter</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                        <!-- Loading search results -->
                                        <template x-if="searching">
                                            <tr>
                                                <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                                                    <p>Searching accounts...</p>
                                                </td>
                                            </tr>
                                        </template>
                                        <!-- Results -->
                                        <template x-if="!searching">
                                            <template x-for="conn in connections" :key="conn.connection_id">
                                                <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" @click="selectAccount(conn)">
                                                    <td class="px-3 py-2 text-sm font-mono text-gray-900 dark:text-white" x-text="conn.account_no"></td>
                                                    <td class="px-3 py-2 text-sm text-gray-900 dark:text-white" x-text="conn.customer_name"></td>
                                                    <td class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400" x-text="conn.account_type"></td>
                                                    <td class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400" x-text="conn.barangay"></td>
                                                    <td class="px-3 py-2 text-sm font-mono text-gray-600 dark:text-gray-400" x-text="conn.meter_serial || '-'"></td>
                                                    <td class="px-3 py-2 text-center">
                                                        <button @click.stop="selectAccount(conn)" class="px-2 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded transition">
                                                            Select
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                        <!-- No results -->
                                        <template x-if="!searching && connections.length === 0">
                                            <tr>
                                                <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    <i class="fas fa-inbox text-2xl mb-2 opacity-50"></i>
                                                    <p x-text="searchQuery ? 'No accounts found matching your search' : 'Type to search for accounts or showing top 100 billable accounts'"></p>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="connections.length"></span> accounts shown
                                <span x-show="connections.length >= 100" class="ml-1">(limited to 100 results)</span>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Billing Period -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Billing Period *</label>
                    <select x-model="form.period_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select period...</option>
                        <template x-for="period in periods" :key="period.per_id">
                            <option :value="period.per_id" :selected="period.per_id == activePeriodId"
                                x-text="period.per_name + (period.is_active ? ' (Current)' : '')"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Meter Readings (only show when account is selected) -->
            <template x-if="selectedConnection">
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">
                        <i class="fas fa-tachometer-alt mr-2 text-green-600"></i>Meter Readings
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Previous Reading (m<sup>3</sup>) *</label>
                            <input type="number" x-model="form.prev_reading" @input="calculateConsumption()" step="0.001" min="0" placeholder="0.000"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono">
                            <template x-if="lastReading">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Last reading: <span class="font-mono" x-text="parseFloat(lastReading.reading_value).toFixed(3)"></span> m<sup>3</sup>
                                    <span x-show="lastReading.reading_date">(<span x-text="lastReading.reading_date"></span>)</span>
                                </p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Reading (m<sup>3</sup>) *</label>
                            <input type="number" x-model="form.curr_reading" @input="calculateConsumption()" step="0.001" min="0" placeholder="0.000"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consumption (m<sup>3</sup>)</label>
                            <input type="text" :value="consumption.toFixed(3)" readonly
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white font-mono font-bold">
                        </div>
                    </div>
                </div>
            </template>

            <!-- Bill Calculation (only show when account is selected) -->
            <template x-if="selectedConnection">
                <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">
                        <i class="fas fa-calculator mr-2 text-purple-600"></i>Bill Calculation
                    </h4>

                    <!-- Loading calculation -->
                    <div x-show="calculating" class="flex items-center py-4">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-purple-600"></div>
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Calculating...</span>
                    </div>

                    <!-- Calculation Breakdown -->
                    <template x-if="breakdown && !calculating">
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                <div class="p-2 bg-white dark:bg-gray-700 rounded">
                                    <p class="text-gray-500 dark:text-gray-400 text-xs uppercase">Range</p>
                                    <p class="font-medium text-gray-900 dark:text-white" x-text="breakdown.range"></p>
                                </div>
                                <div class="p-2 bg-white dark:bg-gray-700 rounded">
                                    <p class="text-gray-500 dark:text-gray-400 text-xs uppercase">Base Amount</p>
                                    <p class="font-medium text-gray-900 dark:text-white">&#8369;<span x-text="parseFloat(breakdown.base_amount).toFixed(2)"></span></p>
                                </div>
                                <div class="p-2 bg-white dark:bg-gray-700 rounded">
                                    <p class="text-gray-500 dark:text-gray-400 text-xs uppercase">Excess (<span x-text="breakdown.excess_consumption"></span> x &#8369;<span x-text="breakdown.rate_increment"></span>)</p>
                                    <p class="font-medium text-gray-900 dark:text-white">&#8369;<span x-text="parseFloat(breakdown.excess_amount).toFixed(2)"></span></p>
                                </div>
                                <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded">
                                    <p class="text-purple-600 dark:text-purple-300 text-xs uppercase font-semibold">Total Bill</p>
                                    <p class="font-bold text-purple-700 dark:text-purple-200 text-lg">&#8369;<span x-text="parseFloat(breakdown.total_amount).toFixed(2)"></span></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- No calculation yet -->
                    <template x-if="!breakdown && !calculating">
                        <div class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                            Enter readings to calculate bill amount
                        </div>
                    </template>
                </div>
            </template>

            <!-- Due Date (only show when account is selected) -->
            <template x-if="selectedConnection">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reading Date</label>
                        <input type="date" x-model="form.reading_date"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Due Date</label>
                        <input type="date" x-model="form.due_date"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </template>

            <!-- Error Message -->
            <template x-if="errorMessage">
                <div class="p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-sm text-red-600 dark:text-red-400" x-text="errorMessage"></p>
                </div>
            </template>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button @click="closeModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button @click="submitBill()" :disabled="submitting || !canSubmit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <span x-show="!submitting"><i class="fas fa-file-invoice mr-2"></i>Generate Bill</span>
                    <span x-show="submitting"><i class="fas fa-spinner fa-spin mr-2"></i>Generating...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function generateBillModalData() {
    return {
        loading: false,
        searching: false,
        calculating: false,
        submitting: false,
        errorMessage: '',
        searchQuery: '',
        connections: [],
        periods: [],
        activePeriodId: null,
        lastReading: null,
        breakdown: null,
        consumption: 0,
        selectedConnection: null,
        form: {
            connection_id: '',
            period_id: '',
            prev_reading: 0,
            curr_reading: 0,
            reading_date: new Date().toISOString().split('T')[0],
            due_date: new Date(Date.now() + 15 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
        },

        get canSubmit() {
            return this.form.connection_id &&
                   this.form.period_id &&
                   this.form.prev_reading !== '' &&
                   this.form.curr_reading !== '' &&
                   this.consumption >= 0 &&
                   this.breakdown;
        },

        async loadData() {
            this.loading = true;
            this.errorMessage = '';
            try {
                const [connectionsRes, periodsRes] = await Promise.all([
                    fetch('/water-bills/billable-connections?limit=100'),
                    fetch('/water-bills/billing-periods')
                ]);

                const connectionsData = await connectionsRes.json();
                const periodsData = await periodsRes.json();

                if (connectionsData.success) {
                    this.connections = connectionsData.data;
                }
                if (periodsData.success) {
                    this.periods = periodsData.data;
                    this.activePeriodId = periodsData.activePeriodId;
                    if (this.activePeriodId) {
                        this.form.period_id = this.activePeriodId;
                    }
                }
            } catch (error) {
                console.error('Error loading data:', error);
                this.errorMessage = 'Failed to load data. Please try again.';
            } finally {
                this.loading = false;
            }
        },

        async searchAccounts() {
            this.searching = true;
            try {
                const url = this.searchQuery
                    ? `/water-bills/billable-connections?search=${encodeURIComponent(this.searchQuery)}&limit=100`
                    : '/water-bills/billable-connections?limit=100';

                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    this.connections = result.data;
                }
            } catch (error) {
                console.error('Error searching accounts:', error);
            } finally {
                this.searching = false;
            }
        },

        async selectAccount(conn) {
            this.selectedConnection = conn;
            this.form.connection_id = conn.connection_id;
            this.lastReading = null;
            this.breakdown = null;
            this.consumption = 0;
            this.form.prev_reading = 0;
            this.form.curr_reading = 0;

            // Load last reading for selected account
            try {
                const response = await fetch(`/water-bills/last-reading/${conn.connection_id}`);
                const result = await response.json();
                if (result.success) {
                    this.lastReading = result.data;
                    this.form.prev_reading = result.data.reading_value;
                }
            } catch (error) {
                console.error('Error loading last reading:', error);
            }
        },

        clearSelection() {
            this.selectedConnection = null;
            this.form.connection_id = '';
            this.lastReading = null;
            this.breakdown = null;
            this.consumption = 0;
            this.form.prev_reading = 0;
            this.form.curr_reading = 0;
            this.searchQuery = '';
            this.searchAccounts();
        },

        calculateConsumption() {
            const prev = parseFloat(this.form.prev_reading) || 0;
            const curr = parseFloat(this.form.curr_reading) || 0;
            this.consumption = Math.max(0, curr - prev);

            if (this.consumption > 0 && this.form.connection_id) {
                this.previewCalculation();
            } else {
                this.breakdown = null;
            }
        },

        async previewCalculation() {
            if (!this.form.connection_id || this.consumption <= 0) {
                this.breakdown = null;
                return;
            }

            this.calculating = true;
            try {
                const response = await fetch('/water-bills/preview', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        connection_id: this.form.connection_id,
                        period_id: this.form.period_id || null,
                        prev_reading: this.form.prev_reading,
                        curr_reading: this.form.curr_reading
                    })
                });

                const result = await response.json();
                if (result.success) {
                    this.breakdown = result.breakdown;
                } else {
                    this.breakdown = null;
                    this.errorMessage = result.message || 'Failed to calculate bill';
                }
            } catch (error) {
                console.error('Error calculating bill:', error);
                this.breakdown = null;
            } finally {
                this.calculating = false;
            }
        },

        async submitBill() {
            this.errorMessage = '';

            if (!this.form.connection_id) {
                this.errorMessage = 'Please select a service account';
                return;
            }
            if (!this.form.period_id) {
                this.errorMessage = 'Please select a billing period';
                return;
            }
            if (this.consumption < 0) {
                this.errorMessage = 'Current reading cannot be less than previous reading';
                return;
            }

            this.submitting = true;
            try {
                const response = await fetch('/water-bills/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();

                if (result.success) {
                    this.closeModal();
                    if (window.showToast) {
                        window.showToast('Bill generated successfully!', 'success');
                    } else if (window.showAlert) {
                        window.showAlert('Bill generated successfully!', 'success');
                    } else {
                        alert('Bill generated successfully!');
                    }
                    document.dispatchEvent(new CustomEvent('bill-generated'));
                    if (typeof window.refreshBillingSummary === 'function') {
                        window.refreshBillingSummary();
                    }
                    if (typeof window.refreshConsumerBilling === 'function') {
                        window.refreshConsumerBilling();
                    }
                    if (window.billing && typeof window.billing.renderConsumerBilling === 'function') {
                        window.billing.renderConsumerBilling();
                    }
                } else {
                    this.errorMessage = result.message || 'Failed to generate bill';
                }
            } catch (error) {
                console.error('Error generating bill:', error);
                this.errorMessage = 'Failed to generate bill. Please try again.';
            } finally {
                this.submitting = false;
            }
        },

        resetForm() {
            this.form = {
                connection_id: '',
                period_id: this.activePeriodId || '',
                prev_reading: 0,
                curr_reading: 0,
                reading_date: new Date().toISOString().split('T')[0],
                due_date: new Date(Date.now() + 15 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
            };
            this.errorMessage = '';
            this.searchQuery = '';
            this.selectedConnection = null;
            this.lastReading = null;
            this.breakdown = null;
            this.consumption = 0;
            this.connections = [];
        },

        closeModal() {
            document.getElementById('generateBillModal').classList.add('hidden');
            this.resetForm();
        }
    }
}

function openGenerateBillModal() {
    const modal = document.getElementById('generateBillModal');
    modal.classList.remove('hidden');

    const alpineComponent = Alpine.$data(modal);
    if (alpineComponent) {
        alpineComponent.resetForm();
        alpineComponent.loadData();
    }
}

function closeGenerateBillModal() {
    const modal = document.getElementById('generateBillModal');
    modal.classList.add('hidden');

    const alpineComponent = Alpine.$data(modal);
    if (alpineComponent) {
        alpineComponent.resetForm();
    }
}

window.openGenerateBillModal = openGenerateBillModal;
window.closeGenerateBillModal = closeGenerateBillModal;
</script>
