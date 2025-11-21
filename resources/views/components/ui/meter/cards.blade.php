<div id="meterSummaryWrapper" class="mb-6" x-data="meterSummaryData()" x-init="updateSummary()">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Meter Overview</h3>
        <a href="{{ route('meter.overall-data') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View Overall Data</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Total Meters</div>
            <div class="text-2xl font-bold text-gray-800 dark:text-gray-100" x-text="totalMeters"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Active</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="activeMeters"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Faulty / Offline</div>
            <div class="text-2xl font-bold text-red-600 dark:text-red-400" x-text="faultyMeters"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Avg Reading (m³)</div>
            <div class="text-2xl font-bold text-gray-800 dark:text-gray-100" x-text="avgReading"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Available</div>
            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" x-text="availableMeters"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Occupied</div>
            <div class="text-2xl font-bold text-gray-800 dark:text-gray-100" x-text="occupiedMeters"></div>
        </div>
    </div>
</div>

<script>
function meterSummaryData() {
    return {
        totalMeters: 0,
        activeMeters: 0,
        faultyMeters: 0,
        avgReading: 0,
        availableMeters: 0,
        occupiedMeters: 0,
        
        updateSummary() {
            const data = [
                { status: 'Active', last_read: 1250, consumer: 'Juan Dela Cruz' },
                { status: 'Active', last_read: 980, consumer: 'Maria Santos' },
                { status: 'Maintenance', last_read: 1420, consumer: 'Pedro Garcia' },
                { status: 'Active', last_read: 750, consumer: 'Ana Rodriguez' },
                { status: 'Inactive', last_read: 1100, consumer: 'Carlos Lopez' },
                { status: 'Active', last_read: 890, consumer: 'Lisa Chen' },
                { status: 'Active', last_read: 1350, consumer: 'Mark Johnson' },
                { status: 'Active', last_read: 670, consumer: 'Sofia Martinez' }
            ];
            
            this.totalMeters = data.length;
            this.activeMeters = data.filter(m => m.status === 'Active').length;
            this.faultyMeters = data.filter(m => m.status === 'Maintenance' || m.status === 'Inactive').length;
            this.avgReading = Math.round(data.reduce((sum, m) => sum + m.last_read, 0) / data.length);
            this.occupiedMeters = data.filter(m => m.consumer && m.consumer.trim() !== '').length;
            this.availableMeters = this.totalMeters - this.occupiedMeters;
        }
    }
}
</script>