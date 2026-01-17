<x-app-layout>
    @php
        $appData = $application ?? null;
        $timelineData = $timeline ?? [];
        $chargesInfo = $chargesData ?? ['charges' => collect(), 'total_amount' => 0, 'paid_amount' => 0, 'remaining_amount' => 0, 'is_fully_paid' => false];
        $statusName = $appData?->status?->stat_desc ?? 'PENDING';
        $customerName = $appData?->customer
            ? trim(($appData->customer->cust_first_name ?? '') . ' ' . ($appData->customer->cust_middle_name ? $appData->customer->cust_middle_name[0] . '. ' : '') . ($appData->customer->cust_last_name ?? ''))
            : '-';
        $fullAddress = $appData?->address
            ? trim(($appData->address->purok?->p_desc ?? '') . ', ' . ($appData->address->barangay?->b_desc ?? ''))
            : '-';
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="applicationDetail(@js([
        'id' => $appData?->application_id,
        'application_number' => $appData?->application_number,
        'status' => $statusName,
        'customer_name' => $customerName,
        'customer' => $appData?->customer ? [
            'resolution_number' => $appData->customer->resolution_no,
            'contact_number' => $appData->customer->contact_number,
            'email' => $appData->customer->email ?? null,
        ] : null,
        'full_address' => $fullAddress,
        'barangay' => $appData?->address?->barangay ? ['b_desc' => $appData->address->barangay->b_desc] : null,
        'landmark' => $appData?->address?->land_mark,
        'charges' => $chargesInfo['charges']->map(fn($c) => [
            'id' => $c->charge_id,
            'description' => $c->description,
            'quantity' => $c->quantity,
            'unit_amount' => $c->unit_amount,
            'total_amount' => $c->total_amount,
            'paid_amount' => $c->paid_amount,
            'remaining_amount' => $c->remaining_amount,
            'is_paid' => $c->isPaid(),
        ])->values()->toArray(),
        'total_amount' => $chargesInfo['total_amount'],
        'paid_amount' => $chargesInfo['paid_amount'],
        'remaining_amount' => $chargesInfo['remaining_amount'],
        'is_fully_paid' => $chargesInfo['is_fully_paid'],
        'payment_status' => $chargesInfo['is_fully_paid'] ? 'PAID' : 'PENDING',
        'payment_date' => $appData?->paid_at,
        'payment_id' => $appData?->payment_id,
        'payment_reference' => $appData?->payment?->receipt_no,
        'scheduled_date' => $appData?->scheduled_connection_date,
        'rejection_reason' => $appData?->rejection_reason,
        'connection_id' => $appData?->connection_id,
        'created_at' => $appData?->submitted_at,
    ]), @js($timelineData))">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header with Back Button -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('connection.service-application.index') }}"
                            class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                                <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                                    <i class="fas fa-file-alt text-indigo-600 dark:text-indigo-400 text-xl"></i>
                                </div>
                                <span>Application #{{ $appData?->application_number ?? '-' }}</span>
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Service Application Details</p>
                        </div>
                    </div>
                    <div>
                        <x-ui.status-badge :status="strtolower($statusName)" />
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-6">
                <!-- Workflow Progress Stepper -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-tasks mr-2 text-blue-600 dark:text-blue-400"></i>
                        Application Progress
                    </h3>

                    <div class="flex items-center justify-between relative">
                        <!-- Progress Line Background -->
                        <div class="absolute left-0 right-0 top-5 h-0.5 bg-gray-200 dark:bg-gray-700 mx-12"></div>
                        <!-- Progress Line Active -->
                        <div class="absolute left-0 top-5 h-0.5 bg-green-500 mx-12 transition-all duration-500"
                            :style="'width: ' + getProgressWidth() + '%'"></div>

                        <!-- Step 1: Submitted -->
                        <div class="relative flex flex-col items-center z-10">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                :class="getStepClass(1)">
                                <i class="fas fa-paper-plane text-sm"></i>
                            </div>
                            <span class="mt-2 text-xs font-medium text-gray-600 dark:text-gray-400">Submitted</span>
                        </div>

                        <!-- Step 2: Verified -->
                        <div class="relative flex flex-col items-center z-10">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                :class="getStepClass(2)">
                                <i class="fas fa-clipboard-check text-sm"></i>
                            </div>
                            <span class="mt-2 text-xs font-medium text-gray-600 dark:text-gray-400">Verified</span>
                        </div>

                        <!-- Step 3: Paid -->
                        <div class="relative flex flex-col items-center z-10">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                :class="getStepClass(3)">
                                <i class="fas fa-money-bill-wave text-sm"></i>
                            </div>
                            <span class="mt-2 text-xs font-medium text-gray-600 dark:text-gray-400">Paid</span>
                        </div>

                        <!-- Step 4: Scheduled -->
                        <div class="relative flex flex-col items-center z-10">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                :class="getStepClass(4)">
                                <i class="fas fa-calendar-check text-sm"></i>
                            </div>
                            <span class="mt-2 text-xs font-medium text-gray-600 dark:text-gray-400">Scheduled</span>
                        </div>

                        <!-- Step 5: Connected -->
                        <div class="relative flex flex-col items-center z-10">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                :class="getStepClass(5)">
                                <i class="fas fa-plug text-sm"></i>
                            </div>
                            <span class="mt-2 text-xs font-medium text-gray-600 dark:text-gray-400">Connected</span>
                        </div>
                    </div>
                </div>

                <!-- Info Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Info Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-user mr-2 text-blue-600 dark:text-blue-400"></i>
                            Customer Information
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Name</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="application.customer_name || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Resolution #</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white font-mono" x-text="application.customer?.resolution_number || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Contact</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="application.customer?.contact_number || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Email</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="application.customer?.email || '-'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Service Address Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-green-600 dark:text-green-400"></i>
                            Service Address
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Address</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white text-right" x-text="application.full_address || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Barangay</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="application.barangay?.b_desc || '-'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Municipality</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Initao, Misamis Oriental</span>
                            </div>
                            <div class="flex justify-between" x-show="application.landmark">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Landmark</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white text-right" x-text="application.landmark || '-'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Fees Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-receipt mr-2 text-purple-600 dark:text-purple-400"></i>
                            Application Fees
                        </h3>
                        <!-- Print Order of Payment / View Receipt Button -->
                        <template x-if="application.charges && application.charges.length > 0">
                            <div>
                                <a x-show="application.is_fully_paid && application.payment_id"
                                    :href="'/payment/receipt/' + application.payment_id"
                                    target="_blank"
                                    class="px-3 py-1.5 text-sm bg-green-100 hover:bg-green-200 dark:bg-green-700 dark:hover:bg-green-600 text-green-700 dark:text-green-300 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-receipt mr-1"></i>View Receipt
                                </a>
                                <a x-show="!application.is_fully_paid"
                                    :href="'/connection/service-application/' + application.id + '/order-of-payment'"
                                    target="_blank"
                                    class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-print mr-1"></i>Print Order of Payment
                                </a>
                            </div>
                        </template>
                    </div>

                    <!-- No Charges Yet Notice -->
                    <div x-show="!application.charges || application.charges.length === 0"
                        class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-file-invoice text-4xl mb-3 opacity-50"></i>
                        <p class="text-sm">Charges will be generated after verification</p>
                    </div>

                    <!-- Charges Table -->
                    <div x-show="application.charges && application.charges.length > 0" class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2">Description</th>
                                    <th class="text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2">Amount</th>
                                    <th class="text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="charge in application.charges" :key="charge.id">
                                    <tr>
                                        <td class="py-3 text-sm text-gray-700 dark:text-gray-300" x-text="charge.description"></td>
                                        <td class="py-3 text-sm text-gray-900 dark:text-white text-right font-medium" x-text="formatCurrency(charge.total_amount)"></td>
                                        <td class="py-3 text-right">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                                :class="charge.is_paid ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'">
                                                <span x-text="charge.is_paid ? 'Paid' : 'Unpaid'"></span>
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                    <td class="py-3 text-sm font-bold text-gray-900 dark:text-white">Total Amount</td>
                                    <td class="py-3 text-lg font-bold text-gray-900 dark:text-white text-right" x-text="formatCurrency(application.total_amount || 0)"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Payment Status -->
                    <div x-show="application.charges && application.charges.length > 0" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Payment Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="application.is_fully_paid ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'">
                                <i class="fas mr-1" :class="application.is_fully_paid ? 'fa-check-circle' : 'fa-clock'"></i>
                                <span x-text="application.is_fully_paid ? 'PAID' : 'PENDING'"></span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between mt-2" x-show="application.payment_date">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Payment Date</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="formatDate(application.payment_date)"></span>
                        </div>
                        <div class="flex items-center justify-between mt-2" x-show="application.payment_reference">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Receipt #</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white font-mono" x-text="application.payment_reference"></span>
                        </div>
                    </div>
                </div>

                <!-- Scheduled Date (if applicable) -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-700 p-6"
                    x-show="application.scheduled_date">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-check text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100">Scheduled Connection Date</h4>
                            <p class="text-lg font-bold text-blue-700 dark:text-blue-300" x-text="formatDate(application.scheduled_date)"></p>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-history mr-2 text-indigo-600 dark:text-indigo-400"></i>
                        Activity Timeline
                    </h3>

                    <div class="relative">
                        <!-- Timeline Line -->
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

                        <!-- Timeline Items -->
                        <div class="space-y-4">
                            <template x-for="event in timeline" :key="event.id">
                                <div class="relative flex gap-4 pl-10">
                                    <div class="absolute left-2 w-4 h-4 rounded-full border-2 border-white dark:border-gray-800"
                                        :class="getTimelineColor(event.type)"></div>
                                    <div class="flex-1 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="event.description"></span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="formatDateTime(event.created_at)"></span>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1" x-show="event.user">
                                            By: <span x-text="event.user?.name || 'System'"></span>
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1" x-show="event.notes" x-text="event.notes"></p>
                                    </div>
                                </div>
                            </template>

                            <!-- Empty State -->
                            <div x-show="timeline.length === 0" class="text-center py-8">
                                <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400">No timeline events yet</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rejection Reason (if rejected) -->
                <div class="bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-700 p-6"
                    x-show="application.status === 'REJECTED'">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-red-900 dark:text-red-100">Rejection Reason</h4>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1" x-text="application.rejection_reason || 'No reason provided'"></p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-cogs mr-2 text-gray-600 dark:text-gray-400"></i>
                        Actions
                    </h3>

                    <!-- Documents Section -->
                    <div class="mb-6">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Documents</p>
                        <div class="flex flex-wrap gap-3">
                            <a :href="'/connection/service-application/' + application.id + '/print'"
                                target="_blank"
                                class="px-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 rounded-lg font-medium transition-colors dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                <i class="fas fa-file-alt mr-2"></i>Print Application
                            </a>
                            <a :href="'/connection/service-application/' + application.id + '/contract'"
                                target="_blank"
                                class="px-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 rounded-lg font-medium transition-colors dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                <i class="fas fa-file-contract mr-2"></i>Print Contract
                            </a>
                            <!-- Order of Payment - Show when verified or has charges -->
                            <a x-show="application.charges && application.charges.length > 0 && !application.is_fully_paid"
                                :href="'/connection/service-application/' + application.id + '/order-of-payment'"
                                target="_blank"
                                class="px-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 rounded-lg font-medium transition-colors dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                <i class="fas fa-file-invoice mr-2"></i>Print Order of Payment
                            </a>
                            <!-- View Receipt - Show when paid -->
                            <a x-show="application.is_fully_paid && application.payment_id"
                                :href="'/payment/receipt/' + application.payment_id"
                                target="_blank"
                                class="px-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100 rounded-lg font-medium transition-colors dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                <i class="fas fa-receipt mr-2"></i>View Receipt
                            </a>
                        </div>
                    </div>

                    <!-- Workflow Section - Status dependent -->
                    <template x-if="!['REJECTED', 'CANCELLED'].includes(application.status)">
                        <div class="mb-6 pt-6 border-t border-dashed border-gray-200 dark:border-gray-700">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Workflow</p>

                            <!-- Status Banner -->
                            <div class="mb-4 p-4 rounded-lg" :class="getStatusBannerClass()">
                                <div class="flex items-center gap-2">
                                    <i class="fas" :class="getStatusIcon()"></i>
                                    <span class="font-medium" x-text="getStatusMessage()"></span>
                                </div>
                                <p class="text-sm mt-1 opacity-80" x-text="getStatusHint()" x-show="getStatusHint()"></p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-3 items-center">
                                <!-- PENDING Status Actions -->
                                <template x-if="application.status === 'PENDING'">
                                    <button @click="openVerifyModal()"
                                        class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-medium transition-colors">
                                        <i class="fas fa-clipboard-check mr-2"></i>Verify Application
                                    </button>
                                </template>

                                <!-- PAID Status Actions -->
                                <template x-if="application.status === 'PAID'">
                                    <button @click="openScheduleModal()"
                                        class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-medium transition-colors">
                                        <i class="fas fa-calendar-check mr-2"></i>Schedule Connection
                                    </button>
                                </template>

                                <!-- SCHEDULED Status Actions -->
                                <template x-if="application.status === 'SCHEDULED'">
                                    <button @click="openCompleteConnectionModal()"
                                        class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-medium transition-colors">
                                        <i class="fas fa-plug mr-2"></i>Complete Connection
                                    </button>
                                </template>

                                <!-- CONNECTED Status -->
                                <template x-if="application.status === 'CONNECTED'">
                                    <a x-show="application.connection_id"
                                        :href="'/customer/service-connection/' + application.connection_id"
                                        class="px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 rounded-lg font-medium transition-colors dark:border-blue-500 dark:text-blue-400 dark:hover:bg-blue-900/20">
                                        <i class="fas fa-eye mr-2"></i>View Connection
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Danger Zone - Show for statuses that can be rejected/cancelled -->
                    <template x-if="['PENDING', 'VERIFIED'].includes(application.status)">
                        <div class="pt-6 border-t border-dashed border-gray-200 dark:border-gray-700">
                            <p class="text-xs font-medium text-red-500 uppercase tracking-wide mb-3">Danger Zone</p>
                            <div class="flex flex-wrap gap-3">
                                <button @click="openRejectModal()"
                                    class="px-4 py-2 bg-red-50 border border-red-200 text-red-700 hover:bg-red-100 rounded-lg font-medium transition-colors dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/40">
                                    <i class="fas fa-times-circle mr-2"></i>Reject Application
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- REJECTED/CANCELLED Status - No Actions Message -->
                    <template x-if="['REJECTED', 'CANCELLED'].includes(application.status)">
                        <div class="pt-6 border-t border-dashed border-gray-200 dark:border-gray-700">
                            <div class="text-gray-500 dark:text-gray-400 italic">
                                <i class="fas fa-ban mr-2"></i>No actions available for this application
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modal Components -->
    <x-ui.connection.verify-modal />
    <x-ui.connection.schedule-modal />
    <x-ui.connection.reject-modal />
    <x-ui.connection.complete-connection-modal />

    <script>
    function applicationDetail(appData, timelineData) {
        return {
            application: appData || {},
            timeline: timelineData || [],

            getStepNumber() {
                const statusMap = {
                    'PENDING': 1,
                    'VERIFIED': 2,
                    'PAID': 3,
                    'SCHEDULED': 4,
                    'CONNECTED': 5,
                    'REJECTED': 0,
                    'CANCELLED': 0
                };
                return statusMap[this.application.status] || 1;
            },

            getProgressWidth() {
                const step = this.getStepNumber();
                if (step === 0) return 0;
                return ((step - 1) / 4) * 100;
            },

            getStepClass(step) {
                const currentStep = this.getStepNumber();
                if (currentStep === 0) {
                    return 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400';
                }
                if (step < currentStep) {
                    return 'bg-green-500 text-white';
                }
                if (step === currentStep) {
                    return 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900';
                }
                return 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400';
            },

            getTimelineColor(type) {
                const colors = {
                    'created': 'bg-blue-500',
                    'verified': 'bg-indigo-500',
                    'payment': 'bg-green-500',
                    'scheduled': 'bg-purple-500',
                    'connected': 'bg-green-600',
                    'rejected': 'bg-red-500',
                    'cancelled': 'bg-gray-500'
                };
                return colors[type] || 'bg-gray-500';
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

            formatDateTime(date) {
                if (!date) return '-';
                return new Date(date).toLocaleString('en-PH', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },

            getStatusBannerClass() {
                const classes = {
                    'PENDING': 'bg-yellow-50 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                    'VERIFIED': 'bg-blue-50 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                    'PAID': 'bg-green-50 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                    'SCHEDULED': 'bg-purple-50 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
                    'CONNECTED': 'bg-teal-50 text-teal-800 dark:bg-teal-900/20 dark:text-teal-400'
                };
                return classes[this.application.status] || 'bg-gray-50 text-gray-800';
            },

            getStatusMessage() {
                const messages = {
                    'PENDING': 'Ready for Verification',
                    'VERIFIED': 'Awaiting Payment',
                    'PAID': 'Ready to Schedule',
                    'SCHEDULED': 'Scheduled for ' + this.formatDate(this.application.scheduled_date),
                    'CONNECTED': 'Connection Completed'
                };
                return messages[this.application.status] || '';
            },

            getStatusHint() {
                const hints = {
                    'VERIFIED': 'Payment processing available at Cashier',
                    'SCHEDULED': 'Ready for installation'
                };
                return hints[this.application.status] || '';
            },

            getStatusIcon() {
                const icons = {
                    'PENDING': 'fa-clipboard-list',
                    'VERIFIED': 'fa-clock',
                    'PAID': 'fa-calendar-plus',
                    'SCHEDULED': 'fa-truck',
                    'CONNECTED': 'fa-check-circle'
                };
                return icons[this.application.status] || 'fa-info-circle';
            },

            openVerifyModal() {
                openVerifyModal(
                    this.application.id,
                    this.application.application_number,
                    this.application.customer_name,
                    this.application.full_address
                );
            },

            openScheduleModal() {
                openScheduleModal(
                    this.application.id,
                    this.application.application_number,
                    this.application.customer_name,
                    this.application.full_address
                );
            },

            openRejectModal() {
                openRejectModal(
                    this.application.id,
                    this.application.application_number,
                    this.application.customer_name,
                    this.application.status,
                    this.formatDate(this.application.created_at)
                );
            },

            openCompleteConnectionModal() {
                openCompleteConnectionModal(
                    this.application.id,
                    this.application.application_number,
                    this.application.customer_name,
                    this.application.full_address,
                    this.formatDate(this.application.scheduled_date)
                );
            }
        };
    }
    </script>
</x-app-layout>
