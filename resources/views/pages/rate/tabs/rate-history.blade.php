<!-- Rate History Tab (Audit Trail) -->
<div>
    <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-history text-orange-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-orange-800 dark:text-orange-300">
                <strong>Rate Change History:</strong> Complete audit trail of all rate changes, amendments, and consumer rate updates. Immutable record for COA compliance.
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="mb-6 space-y-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-filter mr-2"></i>Filter Rate Changes
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                    <input type="date" id="rate-history-from" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                    <input type="date" id="rate-history-to" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Change Type</label>
                    <select id="rate-history-type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500">
                        <option value="">All Types</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="amended">Amended</option>
                        <option value="consumer_assigned">Consumer Assigned</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Changed By</label>
                    <input type="text" placeholder="Username..." id="rate-history-user" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                <button onclick="applyRateHistoryFilter()" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-search mr-2"></i>Apply Filter
                </button>
                <button onclick="resetRateHistoryFilter()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-900 rounded-lg font-medium transition">
                    <i class="fas fa-redo mr-2"></i>Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Rate History Table -->
    <x-ui.action-functions 
        searchPlaceholder="Search by change ID, rate, consumer..."
        filterLabel="All Statuses"
        :filterOptions="[
            ['value' => 'Approved', 'label' => 'Approved'],
            ['value' => 'Pending', 'label' => 'Pending'],
            ['value' => 'Rejected', 'label' => 'Rejected']
        ]"
        :showDateFilter="true"
        :showExport="true"
        tableId="rateHistoryTable"
    />

    @php
        $rateHistoryHeaders = [
            ['key' => 'change_date', 'label' => 'Change Date', 'html' => false],
            ['key' => 'change_id', 'label' => 'Change ID', 'html' => false],
            ['key' => 'change_type', 'label' => 'Type', 'html' => true],
            ['key' => 'rate_parent', 'label' => 'Rate Parent', 'html' => false],
            ['key' => 'detail', 'label' => 'Change Detail', 'html' => false],
            ['key' => 'changed_by', 'label' => 'Changed By', 'html' => false],
            ['key' => 'approved_by', 'label' => 'Approved By', 'html' => false],
            ['key' => 'status', 'label' => 'Status', 'html' => true],
        ];
    @endphp

    <x-table
        id="rateHistoryTable"
        :headers="$rateHistoryHeaders"
        :data="[]"
        :searchable="false"
        :paginated="true"
        :pageSize="20"
        :actions="false"
    />
</div>

<script>
function applyRateHistoryFilter() {
    alert('Applying rate history filter...');
}

function resetRateHistoryFilter() {
    document.getElementById('rate-history-from').value = '';
    document.getElementById('rate-history-to').value = '';
    document.getElementById('rate-history-type').value = '';
    document.getElementById('rate-history-user').value = '';
}

window.applyRateHistoryFilter = applyRateHistoryFilter;
window.resetRateHistoryFilter = resetRateHistoryFilter;
</script>
