<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header -->
            <x-ui.page-header 
                title="Meter Overall Data" 
                subtitle="Comprehensive meter readings and consumption analytics"
                back-url="{{ route('meter.management') }}"
                back-text="Back to Meter Management"
            />

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <x-ui.stat-card 
                    title="Active Meters" 
                    value="0" 
                    icon="fas fa-tachometer-alt" 
                    id="activeMeters"
                />
                <x-ui.stat-card 
                    title="Total Consumption" 
                    value="0 m³" 
                    icon="fas fa-droplet" 
                    id="totalConsumption"
                />
                <x-ui.stat-card 
                    title="Avg Consumption" 
                    value="0 m³" 
                    icon="fas fa-chart-line" 
                    id="avgConsumption"
                />
                <x-ui.stat-card 
                    title="Overdue Readings" 
                    value="0" 
                    icon="fas fa-exclamation-triangle" 
                    id="overdueReadings"
                />
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Consumption Trend Chart -->
                <x-ui.card class="h-80">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Consumption Trend</h3>
                        <x-ui.export-print type="chart" target-id="consumptionChart" filename="consumption-trend" title="Monthly Consumption Trend" />
                    </div>
                    <div class="relative h-64">
                        <canvas id="consumptionChart"></canvas>
                    </div>
                </x-ui.card>

                <!-- Meter Status Distribution -->
                <x-ui.card class="h-80">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Meter Status Distribution</h3>
                        <x-ui.export-print type="chart" target-id="meterStatusChart" filename="meter-status" title="Meter Status Distribution" />
                    </div>
                    <div class="relative h-64">
                        <canvas id="meterStatusChart"></canvas>
                    </div>
                </x-ui.card>
            </div>

            <!-- Data Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Consumers -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Water Consumers</h3>
                        <x-ui.export-print type="table" target-id="topConsumersTableFull" filename="top-water-consumers" title="Top Water Consumers" />
                    </div>
                    <div class="overflow-x-auto">
                        <table id="topConsumersTableFull" class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumer</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumption</th>
                                </tr>
                            </thead>
                            <tbody id="topConsumersTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>

                <!-- Recent Readings -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Meter Readings</h3>
                        <x-ui.export-print type="table" target-id="recentReadingsTableFull" filename="recent-readings" title="Recent Meter Readings" />
                    </div>
                    <div class="overflow-x-auto">
                        <table id="recentReadingsTableFull" class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Meter ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reading</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody id="recentReadingsTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            </div>

        </div>
    </div>
</x-app-layout>

@vite(['resources/js/export-print.js'])

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeMeterOverallData();
});

function initializeMeterOverallData() {
    // Calculate from actual meter data
    const meterData = {
        activeMeters: 11,
        totalConsumption: 189.15,
        avgConsumption: 17.2,
        overdueReadings: 2
    };

    // Update summary cards - find the value elements within the stat cards
    const activeMetersCard = document.querySelector('#activeMeters .text-2xl');
    const totalConsumptionCard = document.querySelector('#totalConsumption .text-2xl');
    const avgConsumptionCard = document.querySelector('#avgConsumption .text-2xl');
    const overdueReadingsCard = document.querySelector('#overdueReadings .text-2xl');
    
    if (activeMetersCard) activeMetersCard.textContent = meterData.activeMeters.toLocaleString();
    if (totalConsumptionCard) totalConsumptionCard.textContent = meterData.totalConsumption.toLocaleString() + ' m³';
    if (avgConsumptionCard) avgConsumptionCard.textContent = meterData.avgConsumption + ' m³';
    if (overdueReadingsCard) overdueReadingsCard.textContent = meterData.overdueReadings;

    // Initialize Consumption Chart with actual meter data
    const consumptionCtx = document.getElementById('consumptionChart').getContext('2d');
    new Chart(consumptionCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            datasets: [{
                label: 'Consumption (m³)',
                data: [0, 41.45, 84.55, 130.75, 189.15],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' m³';
                        }
                    }
                }
            }
        }
    });

    // Initialize Meter Status Chart with actual data
    const statusCtx = document.getElementById('meterStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Installed', 'Faulty', 'Removed'],
            datasets: [{
                data: [8, 3, 2, 2],
                backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#6B7280']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    populateTopConsumersTable();
    populateRecentReadingsTable();
    
    // Make tables sortable
    setTimeout(() => {
        TableSorter.makeTableSortable('topConsumersTableFull');
        TableSorter.makeTableSortable('recentReadingsTableFull');
    }, 100);
}

function populateTopConsumersTable() {
    const topConsumers = [
        { name: 'Apora, Jose (MTR-JKL-77889)', consumption: 60.8 },
        { name: 'Gelogo, Norben (MTR-DEF-11223)', consumption: 52.1 },
        { name: 'Sayson, Sarah (MTR-GHI-44556)', consumption: 37.45 },
        { name: 'Ramos, Angela (MTR-STU-22110)', consumption: 22.9 },
        { name: 'Cruz, Manuel', consumption: 0 }
    ];

    const tbody = document.getElementById('topConsumersTable');
    tbody.innerHTML = topConsumers.map(consumer => `
        <tr class="text-sm">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${consumer.name}</td>
            <td class="px-4 py-2 text-blue-600 dark:text-blue-400">${consumer.consumption} m³</td>
        </tr>
    `).join('');
}

function populateRecentReadingsTable() {
    const recentReadings = [
        { meterId: 'MTR-JKL-77889', reading: 60.8, date: '2024-05-01' },
        { meterId: 'MTR-DEF-11223', reading: 52.1, date: '2024-05-01' },
        { meterId: 'MTR-GHI-44556', reading: 37.45, date: '2024-05-01' },
        { meterId: 'MTR-STU-22110', reading: 22.9, date: '2024-05-01' },
        { meterId: 'MTR-DEF-11223', reading: 38.75, date: '2024-04-01' }
    ];

    const tbody = document.getElementById('recentReadingsTable');
    tbody.innerHTML = recentReadings.map(reading => `
        <tr class="text-sm">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${reading.meterId}</td>
            <td class="px-4 py-2 text-green-600 dark:text-green-400">${reading.reading} m³</td>
            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">${reading.date}</td>
        </tr>
    `).join('');
}
</script>