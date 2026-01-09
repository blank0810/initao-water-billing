<!-- Assign Meter Modal -->
<div id="assignMeterModal" x-data="assignMeterModalData()" x-cloak class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-link text-blue-600 mr-2"></i>Assign Meter to Service Connection
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

        <div x-show="!loading" class="space-y-6">
            <!-- Service Connection Selection -->
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-user-circle mr-2 text-blue-600"></i>Service Connection
                </h4>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Connection *</label>
                    <select x-model="form.connection_id" @change="onConnectionChange()"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Choose service connection...</option>
                        <template x-for="conn in unassignedConnections" :key="conn.connection_id">
                            <option :value="conn.connection_id" x-text="conn.label"></option>
                        </template>
                    </select>
                    <template x-if="unassignedConnections.length === 0 && !loading">
                        <p class="mt-2 text-sm text-orange-600 dark:text-orange-400">
                            <i class="fas fa-exclamation-triangle mr-1"></i>No unassigned connections available
                        </p>
                    </template>
                </div>

                <!-- Selected Connection Info -->
                <template x-if="selectedConnection">
                    <div class="mt-3 p-3 bg-white dark:bg-gray-700 rounded-lg">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Customer:</span>
                                <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="selectedConnection.customer_name"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Type:</span>
                                <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="selectedConnection.account_type"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-500 dark:text-gray-400">Location:</span>
                                <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="selectedConnection.barangay"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Meter Selection -->
            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-tachometer-alt mr-2 text-green-600"></i>Available Meters
                </h4>
                <template x-if="availableMeters.length > 0">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <template x-for="meter in availableMeters" :key="meter.mtr_id">
                            <div @click="selectMeter(meter.mtr_id)"
                                :class="form.meter_id == meter.mtr_id
                                    ? 'border-green-500 dark:border-green-400 bg-green-100 dark:bg-green-900/40'
                                    : 'border-gray-300 dark:border-gray-600 hover:border-green-400'"
                                class="p-3 border-2 rounded-lg cursor-pointer transition-all">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-mono font-medium text-gray-900 dark:text-white" x-text="meter.mtr_serial"></div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400" x-text="meter.mtr_brand"></div>
                                    </div>
                                    <div x-show="form.meter_id == meter.mtr_id" class="text-green-600 dark:text-green-400">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="availableMeters.length === 0 && !loading">
                    <div class="text-center py-6">
                        <i class="fas fa-inbox text-3xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-orange-600 dark:text-orange-400">
                            No available meters. Add new meters in the Inventory tab first.
                        </p>
                    </div>
                </template>
            </div>

            <!-- Installation Details -->
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-calendar-alt mr-2 text-gray-600"></i>Installation Details
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Installation Date *</label>
                        <input type="date" x-model="form.installed_at"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Initial Reading (m<sup>3</sup>)</label>
                        <input type="number" x-model="form.install_read" step="0.001" min="0" placeholder="0.000"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter the meter reading at installation</p>
                    </div>
                </div>
            </div>

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
                <button @click="submitAssignment()" :disabled="submitting || !canSubmit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <span x-show="!submitting"><i class="fas fa-link mr-2"></i>Assign Meter</span>
                    <span x-show="submitting"><i class="fas fa-spinner fa-spin mr-2"></i>Assigning...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function assignMeterModalData() {
    return {
        loading: false,
        submitting: false,
        errorMessage: '',
        availableMeters: [],
        unassignedConnections: [],
        form: {
            connection_id: '',
            meter_id: '',
            installed_at: new Date().toISOString().split('T')[0],
            install_read: 0
        },

        get selectedConnection() {
            if (!this.form.connection_id) return null;
            return this.unassignedConnections.find(c => c.connection_id == this.form.connection_id);
        },

        get canSubmit() {
            return this.form.connection_id && this.form.meter_id && this.form.installed_at;
        },

        async loadData() {
            this.loading = true;
            this.errorMessage = '';
            try {
                const [metersRes, connectionsRes] = await Promise.all([
                    fetch('/meter-assignments/available-meters'),
                    fetch('/meter-assignments/unassigned-connections')
                ]);

                const metersData = await metersRes.json();
                const connectionsData = await connectionsRes.json();

                if (metersData.success) {
                    this.availableMeters = metersData.data;
                }
                if (connectionsData.success) {
                    this.unassignedConnections = connectionsData.data;
                }
            } catch (error) {
                console.error('Error loading data:', error);
                this.errorMessage = 'Failed to load data. Please try again.';
            } finally {
                this.loading = false;
            }
        },

        selectMeter(meterId) {
            this.form.meter_id = meterId;
        },

        onConnectionChange() {
            // Could add additional logic here if needed
        },

        async submitAssignment() {
            this.errorMessage = '';

            if (!this.form.connection_id) {
                this.errorMessage = 'Please select a service connection';
                return;
            }
            if (!this.form.meter_id) {
                this.errorMessage = 'Please select a meter';
                return;
            }
            if (!this.form.installed_at) {
                this.errorMessage = 'Please enter installation date';
                return;
            }

            this.submitting = true;
            try {
                const response = await fetch('/meter-assignments', {
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
                    if (window.showAlert) {
                        window.showAlert('Meter assigned successfully!', 'success');
                    } else {
                        alert('Meter assigned successfully!');
                    }
                    // Refresh the consumer meters table
                    if (typeof window.refreshConsumerMeters === 'function') {
                        window.refreshConsumerMeters();
                    }
                    // Refresh the inventory data if available
                    if (typeof window.refreshInventoryData === 'function') {
                        window.refreshInventoryData();
                    }
                } else {
                    this.errorMessage = result.message || 'Failed to assign meter';
                }
            } catch (error) {
                console.error('Error assigning meter:', error);
                this.errorMessage = 'Failed to assign meter. Please try again.';
            } finally {
                this.submitting = false;
            }
        },

        resetForm() {
            this.form = {
                connection_id: '',
                meter_id: '',
                installed_at: new Date().toISOString().split('T')[0],
                install_read: 0
            };
            this.errorMessage = '';
        },

        closeModal() {
            document.getElementById('assignMeterModal').classList.add('hidden');
            this.resetForm();
        }
    }
}

function openAssignMeterModal() {
    const modal = document.getElementById('assignMeterModal');
    modal.classList.remove('hidden');

    // Get Alpine.js component and load data
    const alpineComponent = Alpine.$data(modal);
    if (alpineComponent) {
        alpineComponent.resetForm();
        alpineComponent.loadData();
    }
}

function closeAssignMeterModal() {
    const modal = document.getElementById('assignMeterModal');
    modal.classList.add('hidden');

    const alpineComponent = Alpine.$data(modal);
    if (alpineComponent) {
        alpineComponent.resetForm();
    }
}

window.openAssignMeterModal = openAssignMeterModal;
window.closeAssignMeterModal = closeAssignMeterModal;
</script>
