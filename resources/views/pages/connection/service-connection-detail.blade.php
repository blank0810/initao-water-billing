<x-app-layout>
    @php
        $connData = $connection ?? null;
        $statusName = $connData?->status?->stat_desc ?? 'ACTIVE';

        // Build customer name with null-safe access
        $firstName = data_get($connData, 'customer.cust_first_name', '');
        $middleName = data_get($connData, 'customer.cust_middle_name', '');
        $lastName = data_get($connData, 'customer.cust_last_name', '');
        $middleInitial = $middleName ? substr($middleName, 0, 1) . '.' : '';
        $customerName = trim(implode(' ', array_filter([$firstName, $middleInitial, $lastName]))) ?: '-';

        // Build full address with null-safe access
        $purokDesc = data_get($connData, 'address.purok.p_desc', '');
        $barangayDesc = data_get($connData, 'address.barangay.b_desc', '');
        $addressParts = array_filter([$purokDesc, $barangayDesc]);
        $fullAddress = $addressParts ? implode(', ', $addressParts) : '-';

        $balanceData = $balance ?? ['total_bills' => 0, 'total_charges' => 0, 'total_payments' => 0, 'balance' => 0];
        $meterData = $currentMeter ?? null;
        $meterHistoryData = $meterHistory ?? [];
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="connectionDetail(@js([
        'id' => $connData?->connection_id,
        'account_number' => $connData?->account_no,
        'status' => $statusName,
        'customer_name' => $customerName,
        'customer' => $connData?->customer ? [
            'resolution_no' => data_get($connData, 'customer.resolution_no'),
            'contact_number' => data_get($connData, 'customer.contact_number'),
            'email' => data_get($connData, 'customer.email'),
        ] : null,
        'full_address' => $fullAddress,
        'barangay' => $barangayDesc ? ['b_name' => $barangayDesc] : null,
        'account_type' => $connData?->accountType?->at_desc ?? 'Residential',
        'started_at' => $connData?->started_at,
        'suspended_at' => $connData?->suspended_at,
        'disconnected_at' => $connData?->disconnected_at,
        'suspension_reason' => $connData?->suspension_reason,
        'disconnection_reason' => $connData?->disconnection_reason,
        'application_id' => $connData?->serviceApplication?->application_id,
    ]), @js($balanceData), @js($meterData), @js($meterHistoryData))">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header with Back Button -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('service.connection') }}"
                            class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                                <div class="p-2 bg-teal-100 dark:bg-teal-900 rounded-lg">
                                    <i class="fas fa-plug text-teal-600 dark:text-teal-400 text-xl"></i>
                                </div>
                                <span>Account #{{ $connData?->account_no ?? '-' }}</span>
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Service Connection Details</p>
                        </div>
                    </div>
                    <div>
                        <x-ui.status-badge :status="strtolower($statusName)" />
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-6">
                <!-- Stats Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Account Info Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-user mr-2 text-blue-600 dark:text-blue-400"></i>
                            Account Information
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Customer</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="connection.customer_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Type</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="connection.account_type"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Since</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="formatDate(connection.started_at)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Meter Info Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-tachometer-alt mr-2 text-purple-600 dark:text-purple-400"></i>
                            Current Meter
                        </h3>
                        <template x-if="currentMeter">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Serial #</span>
                                    <span class="text-sm font-medium font-mono text-gray-900 dark:text-white" x-text="currentMeter.meter?.mtr_serial || '-'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Brand</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="currentMeter.meter?.mtr_brand || '-'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Install Reading</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="formatNumber(currentMeter.install_read) + ' cu.m.'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Installed</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="formatDate(currentMeter.installed_at)"></span>
                                </div>
                            </div>
                        </template>
                        <template x-if="!currentMeter">
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-circle text-3xl text-yellow-500 mb-2"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400">No meter assigned</p>
                                <button @click="openAssignMeterModal()"
                                    class="mt-3 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Assign Meter
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Balance Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-peso-sign mr-2 text-green-600 dark:text-green-400"></i>
                            Account Balance
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Billed</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="formatCurrency((balance.total_bills || 0) + (balance.total_charges || 0))"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Paid</span>
                                <span class="text-sm font-medium text-green-600 dark:text-green-400" x-text="formatCurrency(balance.total_payments)"></span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Outstanding</span>
                                    <span class="text-lg font-bold"
                                        :class="balance.balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'"
                                        x-text="formatCurrency(balance.balance)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Address -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-red-600 dark:text-red-400"></i>
                        Service Address
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm text-gray-600 dark:text-gray-400 block">Full Address</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="connection.full_address"></span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 dark:text-gray-400 block">Barangay</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="connection.barangay?.b_name || '-'"></span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 dark:text-gray-400 block">Municipality</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Initao, Misamis Oriental</span>
                        </div>
                    </div>
                </div>

                <!-- Meter History -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-history mr-2 text-indigo-600 dark:text-indigo-400"></i>
                        Meter History
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full" x-show="meterHistory.length > 0">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Serial #</th>
                                    <th class="text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Brand</th>
                                    <th class="text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Install Reading</th>
                                    <th class="text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Removal Reading</th>
                                    <th class="text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Install Date</th>
                                    <th class="text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Removal Date</th>
                                    <th class="text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="(meter, index) in meterHistory" :key="meter.assignment_id || index">
                                    <tr @click="selectMeter(meter.assignment_id)" @keydown.enter="selectMeter(meter.assignment_id)" @keydown.space.prevent="selectMeter(meter.assignment_id)" tabindex="0" role="button" class="cursor-pointer transition-colors"
                                        :class="selectedAssignmentId === meter.assignment_id
                                            ? 'bg-blue-50 dark:bg-blue-900/20 ring-1 ring-inset ring-blue-200 dark:ring-blue-700'
                                            : 'hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                                        <td class="py-3 px-3 text-sm font-mono text-gray-900 dark:text-white" x-text="meter.meter?.mtr_serial || '-'"></td>
                                        <td class="py-3 px-3 text-sm text-gray-700 dark:text-gray-300" x-text="meter.meter?.mtr_brand || '-'"></td>
                                        <td class="py-3 px-3 text-sm text-right text-gray-900 dark:text-white" x-text="formatNumber(meter.install_read)"></td>
                                        <td class="py-3 px-3 text-sm text-right text-gray-900 dark:text-white" x-text="meter.removal_read ? formatNumber(meter.removal_read) : '-'"></td>
                                        <td class="py-3 px-3 text-sm text-gray-700 dark:text-gray-300" x-text="formatDate(meter.installed_at)"></td>
                                        <td class="py-3 px-3 text-sm text-gray-700 dark:text-gray-300" x-text="meter.removed_at ? formatDate(meter.removed_at) : '-'"></td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full"
                                                :class="meter.removed_at ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'"
                                                x-text="meter.removed_at ? 'Removed' : 'Active'"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div x-show="meterHistory.length === 0" class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No meter history available</p>
                        </div>
                    </div>
                </div>

                <!-- Meter Readings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6"
                    x-show="selectedAssignmentId">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-chart-line mr-2 text-teal-600 dark:text-teal-400"></i>
                            Meter Readings
                            <span class="ml-2 text-xs font-normal text-gray-500 dark:text-gray-400">
                                — <span class="font-mono" x-text="selectedMeterSerial"></span>
                            </span>
                        </h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400" x-show="selectedMeterReadings.length > 0"
                            x-text="selectedMeterReadings.length + ' reading' + (selectedMeterReadings.length !== 1 ? 's' : '')"></span>
                    </div>

                    <div class="overflow-x-auto" x-show="selectedMeterReadings.length > 0">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Period</th>
                                    <th class="text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Reading Date</th>
                                    <th class="text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Reading (cu.m.)</th>
                                    <th class="text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Consumption</th>
                                    <th class="text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Estimated</th>
                                    <th class="text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2 px-3">Reader</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="(reading, idx) in paginatedReadings" :key="reading.reading_id || idx">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="py-3 px-3 text-sm text-gray-900 dark:text-white" x-text="reading.period?.per_name || '-'"></td>
                                        <td class="py-3 px-3 text-sm text-gray-700 dark:text-gray-300" x-text="formatDate(reading.reading_date)"></td>
                                        <td class="py-3 px-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="formatNumber(reading.reading_value)"></td>
                                        <td class="py-3 px-3 text-sm text-right font-mono text-gray-900 dark:text-white" x-text="getConsumption(idx)"></td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full"
                                                :class="reading.is_estimated
                                                    ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
                                                    : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'"
                                                x-text="reading.is_estimated ? 'Yes' : 'No'"></span>
                                        </td>
                                        <td class="py-3 px-3 text-sm text-gray-700 dark:text-gray-300" x-text="reading.reading_responsibility?.responsible_user_name || '-'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div x-show="totalReadingsPages > 1"
                            class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                            <span class="text-xs text-gray-500 dark:text-gray-400"
                                x-text="'Page ' + readingsPage + ' of ' + totalReadingsPages"></span>
                            <div class="flex gap-2">
                                <button @click="readingsPage--" :disabled="readingsPage <= 1"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors"
                                    :class="readingsPage <= 1
                                        ? 'border-gray-200 text-gray-400 cursor-not-allowed dark:border-gray-700 dark:text-gray-600'
                                        : 'border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'">
                                    <i class="fas fa-chevron-left mr-1"></i>Previous
                                </button>
                                <button @click="readingsPage++" :disabled="readingsPage >= totalReadingsPages"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors"
                                    :class="readingsPage >= totalReadingsPages
                                        ? 'border-gray-200 text-gray-400 cursor-not-allowed dark:border-gray-700 dark:text-gray-600'
                                        : 'border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'">
                                    Next<i class="fas fa-chevron-right ml-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="selectedMeterReadings.length === 0" class="text-center py-8">
                        <i class="fas fa-chart-line text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No billing readings recorded for this meter</p>
                    </div>
                </div>

                <!-- Transaction Ledger -->
                @include('pages.connection.tabs.ledger-tab')

                <!-- Suspension/Disconnection Info -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-200 dark:border-yellow-700 p-6"
                    x-show="connection.status === 'SUSPENDED'">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-pause-circle text-yellow-600 dark:text-yellow-400 text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-yellow-900 dark:text-yellow-100">Connection Suspended</h4>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                Suspended on: <span class="font-medium" x-text="formatDate(connection.suspended_at)"></span>
                            </p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1" x-show="connection.suspension_reason">
                                Reason: <span x-text="connection.suspension_reason"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-700 p-6"
                    x-show="connection.status === 'DISCONNECTED'">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-unlink text-red-600 dark:text-red-400 text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-red-900 dark:text-red-100">Connection Disconnected</h4>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                Disconnected on: <span class="font-medium" x-text="formatDate(connection.disconnected_at)"></span>
                            </p>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1" x-show="connection.disconnection_reason">
                                Reason: <span x-text="connection.disconnection_reason"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <!-- Documents Section -->
                    <div class="mb-6">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Documents</p>
                        <div class="flex flex-wrap gap-3">
                            <template x-if="connection.application_id">
                                <a :href="'/connection/service-application/' + connection.application_id + '/print'"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="px-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-file-alt mr-2"></i>Print Application
                                </a>
                            </template>
                            <template x-if="connection.application_id">
                                <a :href="'/connection/service-application/' + connection.application_id + '/contract'"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="px-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-file-contract mr-2"></i>Print Contract
                                </a>
                            </template>
                            <a :href="'/customer/service-connection/' + connection.id + '/statement'"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="px-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
                                <i class="fas fa-file-invoice mr-2"></i>Account Statement
                            </a>
                        </div>
                    </div>

                    <!-- Actions Section -->
                    <div class="pt-6 border-t border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Actions</p>
                        <div class="flex flex-wrap gap-3">
                        <!-- ACTIVE Status Actions -->
                        <template x-if="connection.status === 'ACTIVE'">
                            <div class="flex flex-wrap gap-3">
                                <button @click="openAssignMeterModal()"
                                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Assign/Replace Meter
                                </button>
                                <button @click="openSuspendModal()"
                                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors">
                                    <i class="fas fa-pause-circle mr-2"></i>Suspend
                                </button>
                                <button @click="openDisconnectModal()"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    <i class="fas fa-unlink mr-2"></i>Disconnect
                                </button>
                            </div>
                        </template>

                        <!-- SUSPENDED Status Actions -->
                        <template x-if="connection.status === 'SUSPENDED'">
                            <div class="flex flex-wrap gap-3">
                                <button @click="reconnect()"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                                    <i class="fas fa-plug mr-2"></i>Reconnect
                                </button>
                                <button @click="openDisconnectModal()"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                    <i class="fas fa-unlink mr-2"></i>Disconnect Permanently
                                </button>
                            </div>
                        </template>

                        <!-- DISCONNECTED Status - Read Only -->
                        <template x-if="connection.status === 'DISCONNECTED'">
                            <div class="text-gray-500 dark:text-gray-400 italic">
                                <i class="fas fa-ban mr-2"></i>This connection has been permanently disconnected. No actions available.
                            </div>
                        </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suspend Modal -->
    <div id="suspendModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <i class="fas fa-pause-circle text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Suspend Connection</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Temporarily suspend service</p>
                    </div>
                </div>
                <button onclick="closeSuspendModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200 font-medium">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        This will temporarily suspend water service for this connection.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Suspension Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea id="suspendReason" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                        placeholder="Enter reason for suspension..."></textarea>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
                <button onclick="closeSuspendModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button onclick="submitSuspend()" id="suspendSubmitBtn" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-pause-circle mr-2"></i>Suspend Connection
                </button>
            </div>
        </div>
    </div>

    <!-- Disconnect Modal -->
    <div id="disconnectModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i class="fas fa-unlink text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Disconnect Connection</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Permanently disconnect service</p>
                    </div>
                </div>
                <button onclick="closeDisconnectModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded">
                    <p class="text-sm text-red-800 dark:text-red-200 font-medium">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Warning: This action cannot be undone. The connection will be permanently disconnected.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Disconnection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea id="disconnectReason" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Enter reason for disconnection..."></textarea>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
                <button onclick="closeDisconnectModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button onclick="submitDisconnect()" id="disconnectSubmitBtn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-unlink mr-2"></i>Disconnect
                </button>
            </div>
        </div>
    </div>

    <!-- Assign Meter Modal -->
    <div id="assignMeterModal"
         class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4"
         data-connection-id="{{ $connData?->connection_id }}"
         data-current-meter="{{ $meterData ? json_encode(['serial' => $meterData->meter->mtr_serial ?? '', 'brand' => $meterData->meter->mtr_brand ?? '', 'install_read' => $meterData->install_read ?? 0]) : '' }}">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <i class="fas fa-tachometer-alt text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="assignMeterModalTitle">Assign Meter</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400" id="assignMeterModalSubtitle">Assign a meter to this connection</p>
                    </div>
                </div>
                <button onclick="closeAssignMeterModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <!-- Replacing Existing Meter Section (shown only when meter exists) -->
                <div id="replacingMeterSection" class="hidden">
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exchange-alt text-amber-600 dark:text-amber-400 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Replacing Existing Meter</p>
                                <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                    Current: <span id="currentMeterInfo" class="font-mono font-medium">-</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Old Meter Final Reading <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="oldMeterFinalReading" step="0.001" min="0" placeholder="Enter final reading"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Must be >= install reading (<span id="minRemovalRead">0.000</span>)
                        </p>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="flex rounded-lg bg-gray-100 dark:bg-gray-700 p-1">
                    <button type="button" id="tabSelectExisting"
                        onclick="switchMeterTab('existing')"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors bg-white dark:bg-gray-600 text-purple-600 dark:text-purple-400 shadow-sm">
                        <i class="fas fa-list mr-2"></i>Select Existing
                    </button>
                    <button type="button" id="tabAddNew"
                        onclick="switchMeterTab('new')"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-plus mr-2"></i>Add New
                    </button>
                </div>

                <!-- Select Existing Tab Content -->
                <div id="tabContentExisting" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Meter <span class="text-red-500">*</span>
                        </label>
                        <select id="meterSelect"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Loading available meters...</option>
                        </select>
                    </div>
                </div>

                <!-- Add New Tab Content -->
                <div id="tabContentNew" class="space-y-4 hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Serial Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="newMeterSerial" maxlength="50" placeholder="Enter meter serial number"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Brand <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="newMeterBrand" maxlength="100" placeholder="Enter meter brand"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>

                <!-- Shared Initial Reading Field -->
                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span id="initialReadingLabel">Initial Reading</span> <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="meterInitialReading" step="0.001" min="0" value="0.000"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
                <button onclick="closeAssignMeterModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button onclick="submitAssignMeter()" id="assignMeterSubmitBtn" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-check mr-2"></i><span id="assignMeterBtnText">Assign Meter</span>
                </button>
            </div>
        </div>
    </div>

    <script>
    function connectionDetail(connData, balanceData, meterData, historyData) {
        return {
            connection: connData || {},
            balance: balanceData || { total_bills: 0, total_charges: 0, total_payments: 0, balance: 0 },
            currentMeter: meterData,
            meterHistory: historyData || [],
            selectedAssignmentId: null,
            readingsPage: 1,
            readingsPerPage: 12,

            init() {
                this.selectedAssignmentId = this.currentMeter?.assignment_id || null;

                // Initialize connection ledger tab
                this.$nextTick(() => {
                    if (window.initializeConnectionLedgerTab && this.connection.id) {
                        window.initializeConnectionLedgerTab(this.connection.id);
                    }
                });
            },

            get selectedAssignment() {
                if (!this.selectedAssignmentId) return null;
                if (this.currentMeter?.assignment_id === this.selectedAssignmentId) return this.currentMeter;
                return this.meterHistory.find(m => m.assignment_id === this.selectedAssignmentId) || null;
            },

            get selectedMeterReadings() {
                return this.selectedAssignment?.meter_readings || [];
            },

            get paginatedReadings() {
                const start = (this.readingsPage - 1) * this.readingsPerPage;
                return this.selectedMeterReadings.slice(start, start + this.readingsPerPage);
            },

            get totalReadingsPages() {
                return Math.ceil(this.selectedMeterReadings.length / this.readingsPerPage) || 1;
            },

            get selectedMeterSerial() {
                return this.selectedAssignment?.meter?.mtr_serial || '-';
            },

            selectMeter(assignmentId) {
                this.selectedAssignmentId = assignmentId;
                this.readingsPage = 1;
            },

            getConsumption(pageIndex) {
                const globalIndex = (this.readingsPage - 1) * this.readingsPerPage + pageIndex;
                const readings = this.selectedMeterReadings;
                const current = parseFloat(readings[globalIndex]?.reading_value || 0);
                if (globalIndex === 0) {
                    const installRead = parseFloat(this.selectedAssignment?.install_read || 0);
                    return (current - installRead).toFixed(3);
                }
                const previous = parseFloat(readings[globalIndex - 1]?.reading_value || 0);
                return (current - previous).toFixed(3);
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('en-PH', {
                    style: 'currency',
                    currency: 'PHP'
                }).format(amount || 0);
            },

            formatDate(date) {
                if (!date) return '-';
                return new Date(date).toLocaleDateString('en-PH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            },

            formatNumber(num) {
                if (num === null || num === undefined) return '0.000';
                return parseFloat(num).toFixed(3);
            },

            openSuspendModal() {
                document.getElementById('suspendModal').classList.remove('hidden');
            },

            openDisconnectModal() {
                document.getElementById('disconnectModal').classList.remove('hidden');
            },

            openAssignMeterModal() {
                initializeAssignMeterModal();
                loadAvailableMetersForConnection();
                document.getElementById('assignMeterModal').classList.remove('hidden');
            },

            async reconnect() {
                if (!confirm('Are you sure you want to reconnect this connection?')) return;

                try {
                    const response = await fetch(`/customer/service-connection/${this.connection.id}/reconnect`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        if (window.showToast) {
                            window.showToast('Connection reconnected successfully!', 'success');
                        }
                        location.reload();
                    } else {
                        throw new Error(data.message || 'Reconnection failed');
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            }
        };
    }

    let connectionId = null;

    document.addEventListener('alpine:init', () => {
        const el = document.querySelector('[x-data]');
        if (el && el.__x) {
            connectionId = el.__x.$data.connection.id;
        }
    });

    function closeSuspendModal() {
        document.getElementById('suspendModal').classList.add('hidden');
    }

    function closeDisconnectModal() {
        document.getElementById('disconnectModal').classList.add('hidden');
    }

    function closeAssignMeterModal() {
        document.getElementById('assignMeterModal').classList.add('hidden');
        // Reset to default tab
        switchMeterTab('existing');
        // Clear new meter fields
        document.getElementById('newMeterSerial').value = '';
        document.getElementById('newMeterBrand').value = '';
        // Clear old meter final reading
        document.getElementById('oldMeterFinalReading').value = '';
        // Reset initial reading
        document.getElementById('meterInitialReading').value = '0.000';
    }

    // Track current meter tab mode and replacement mode
    let currentMeterTabMode = 'existing';
    let isReplacingMeter = false;

    /**
     * Initialize the assign meter modal based on whether a meter already exists
     */
    function initializeAssignMeterModal() {
        const modal = document.getElementById('assignMeterModal');
        const currentMeterData = modal.dataset.currentMeter;
        const replacingSection = document.getElementById('replacingMeterSection');
        const modalTitle = document.getElementById('assignMeterModalTitle');
        const modalSubtitle = document.getElementById('assignMeterModalSubtitle');
        const initialReadingLabel = document.getElementById('initialReadingLabel');

        if (currentMeterData) {
            try {
                const meter = JSON.parse(currentMeterData);
                isReplacingMeter = true;

                // Show replacement section
                replacingSection.classList.remove('hidden');

                // Update current meter info display
                document.getElementById('currentMeterInfo').textContent =
                    `${meter.serial} (${meter.brand})`;
                document.getElementById('minRemovalRead').textContent =
                    parseFloat(meter.install_read).toFixed(3);

                // Update modal title/subtitle
                modalTitle.textContent = 'Replace Meter';
                modalSubtitle.textContent = 'Replace the current meter with a new one';

                // Update label for clarity
                initialReadingLabel.textContent = 'New Meter Initial Reading';

            } catch (e) {
                console.error('Error parsing current meter data:', e);
                isReplacingMeter = false;
                replacingSection.classList.add('hidden');
            }
        } else {
            isReplacingMeter = false;
            replacingSection.classList.add('hidden');
            modalTitle.textContent = 'Assign Meter';
            modalSubtitle.textContent = 'Assign a meter to this connection';
            initialReadingLabel.textContent = 'Initial Reading';
        }

        // Update button text
        updateMeterButtonText();
    }

    /**
     * Get the correct button text based on current state
     */
    function getMeterButtonText() {
        if (isReplacingMeter) {
            return currentMeterTabMode === 'existing' ? 'Replace Meter' : 'Register & Replace';
        } else {
            return currentMeterTabMode === 'existing' ? 'Assign Meter' : 'Register & Assign';
        }
    }

    /**
     * Update the submit button text based on current state
     */
    function updateMeterButtonText() {
        const btnText = document.getElementById('assignMeterBtnText');
        if (btnText) {
            btnText.textContent = getMeterButtonText();
        }
    }

    function switchMeterTab(tab) {
        currentMeterTabMode = tab;

        const tabExisting = document.getElementById('tabSelectExisting');
        const tabNew = document.getElementById('tabAddNew');
        const contentExisting = document.getElementById('tabContentExisting');
        const contentNew = document.getElementById('tabContentNew');

        // Active tab styles
        const activeClasses = 'bg-white dark:bg-gray-600 text-purple-600 dark:text-purple-400 shadow-sm';
        const inactiveClasses = 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white';

        if (tab === 'existing') {
            // Activate existing tab
            tabExisting.className = `flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors ${activeClasses}`;
            tabNew.className = `flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors ${inactiveClasses}`;
            contentExisting.classList.remove('hidden');
            contentNew.classList.add('hidden');
        } else {
            // Activate new tab
            tabNew.className = `flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors ${activeClasses}`;
            tabExisting.className = `flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors ${inactiveClasses}`;
            contentNew.classList.remove('hidden');
            contentExisting.classList.add('hidden');
        }

        // Update button text based on replacement mode
        updateMeterButtonText();
    }

    async function loadAvailableMetersForConnection() {
        const select = document.getElementById('meterSelect');
        select.innerHTML = '<option value="">Loading...</option>';

        try {
            const response = await fetch('/customer/service-connection/meters/available', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const data = await response.json();

            if (data.success) {
                select.innerHTML = '<option value="">Select a meter</option>';
                data.data.forEach(meter => {
                    const option = document.createElement('option');
                    option.value = meter.mtr_id;
                    option.textContent = `${meter.mtr_serial} - ${meter.mtr_brand || 'Unknown Brand'}`;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            select.innerHTML = '<option value="">Error loading meters</option>';
            console.error('Error loading meters:', error);
        }
    }

    async function submitSuspend() {
        const reason = document.getElementById('suspendReason').value.trim();
        const btn = document.getElementById('suspendSubmitBtn');

        if (!reason) {
            alert('Please provide a suspension reason');
            return;
        }

        const el = document.querySelector('[x-data]');
        const connId = el?.__x?.$data?.connection?.id;

        if (!connId) {
            alert('Connection ID not found');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

        try {
            const response = await fetch(`/customer/service-connection/${connId}/suspend`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason })
            });

            const data = await response.json();

            if (data.success) {
                closeSuspendModal();
                if (window.showToast) {
                    window.showToast('Connection suspended', 'warning');
                }
                location.reload();
            } else {
                throw new Error(data.message || 'Suspension failed');
            }
        } catch (error) {
            alert('Error: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-pause-circle mr-2"></i>Suspend Connection';
        }
    }

    async function submitDisconnect() {
        const reason = document.getElementById('disconnectReason').value.trim();
        const btn = document.getElementById('disconnectSubmitBtn');

        if (!reason) {
            alert('Please provide a disconnection reason');
            return;
        }

        if (!confirm('Are you sure you want to permanently disconnect this connection? This action cannot be undone.')) {
            return;
        }

        const el = document.querySelector('[x-data]');
        const connId = el?.__x?.$data?.connection?.id;

        if (!connId) {
            alert('Connection ID not found');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

        try {
            const response = await fetch(`/customer/service-connection/${connId}/disconnect`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason })
            });

            const data = await response.json();

            if (data.success) {
                closeDisconnectModal();
                if (window.showToast) {
                    window.showToast('Connection disconnected', 'error');
                }
                location.reload();
            } else {
                throw new Error(data.message || 'Disconnection failed');
            }
        } catch (error) {
            alert('Error: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-unlink mr-2"></i>Disconnect';
        }
    }

    async function submitAssignMeter() {
        const initialReading = document.getElementById('meterInitialReading').value;
        const btn = document.getElementById('assignMeterSubmitBtn');

        if (!initialReading || parseFloat(initialReading) < 0) {
            alert('Please enter a valid initial reading');
            return;
        }

        // Get connection ID from data attribute
        const modal = document.getElementById('assignMeterModal');
        const connId = modal?.dataset?.connectionId;

        if (!connId) {
            alert('Connection ID not found');
            return;
        }

        // Handle replacement validation
        let removalRead = null;
        if (isReplacingMeter) {
            const oldMeterFinalReading = document.getElementById('oldMeterFinalReading').value;
            if (!oldMeterFinalReading || oldMeterFinalReading === '') {
                alert('Please enter the old meter\'s final reading');
                return;
            }
            removalRead = parseFloat(oldMeterFinalReading);
            if (isNaN(removalRead) || removalRead < 0) {
                alert('Please enter a valid final reading');
                return;
            }
        }

        let meterId;

        // Check which mode we're in
        if (currentMeterTabMode === 'existing') {
            meterId = document.getElementById('meterSelect').value;
            if (!meterId) {
                alert('Please select a meter');
                return;
            }
        } else {
            // Add New mode - first create the meter
            const serial = document.getElementById('newMeterSerial').value.trim();
            const brand = document.getElementById('newMeterBrand').value.trim();

            if (!serial) {
                alert('Please enter a serial number');
                return;
            }
            if (!brand) {
                alert('Please enter a brand');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating meter...';

            try {
                const createResponse = await fetch('/meters', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        mtr_serial: serial,
                        mtr_brand: brand
                    })
                });

                const createData = await createResponse.json();

                if (!createData.success) {
                    throw new Error(createData.message || 'Failed to create meter');
                }

                meterId = createData.data.mtr_id;
                btn.innerHTML = isReplacingMeter
                    ? '<i class="fas fa-spinner fa-spin mr-2"></i>Replacing meter...'
                    : '<i class="fas fa-spinner fa-spin mr-2"></i>Assigning meter...';
            } catch (error) {
                alert('Error creating meter: ' + error.message);
                btn.disabled = false;
                resetMeterButtonState();
                return;
            }
        }

        btn.disabled = true;
        btn.innerHTML = isReplacingMeter
            ? '<i class="fas fa-spinner fa-spin mr-2"></i>Replacing meter...'
            : '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

        // Build payload
        const payload = {
            meter_id: parseInt(meterId),
            install_read: parseFloat(initialReading)
        };

        // Add removal_read if replacing
        if (isReplacingMeter && removalRead !== null) {
            payload.removal_read = removalRead;
        }

        try {
            const response = await fetch(`/customer/service-connection/${connId}/assign-meter`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                closeAssignMeterModal();
                if (window.showToast) {
                    let message;
                    if (isReplacingMeter) {
                        message = currentMeterTabMode === 'new'
                            ? 'Meter registered and replaced successfully!'
                            : 'Meter replaced successfully!';
                    } else {
                        message = currentMeterTabMode === 'new'
                            ? 'Meter registered and assigned successfully!'
                            : 'Meter assigned successfully!';
                    }
                    window.showToast(message, 'success');
                }
                location.reload();
            } else {
                throw new Error(data.message || 'Meter assignment failed');
            }
        } catch (error) {
            alert('Error: ' + error.message);
            btn.disabled = false;
            resetMeterButtonState();
        }
    }

    /**
     * Reset the meter button to its correct state based on current mode
     */
    function resetMeterButtonState() {
        const btn = document.getElementById('assignMeterSubmitBtn');
        const btnText = getMeterButtonText();
        btn.innerHTML = `<i class="fas fa-check mr-2"></i><span id="assignMeterBtnText">${btnText}</span>`;
    }
    </script>

    @vite(['resources/js/data/connection/connection-ledger-data.js'])
</x-app-layout>
