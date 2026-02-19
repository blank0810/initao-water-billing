<!-- Comprehensive Dashboard with Multiple Charts -->
<div class="rounded-2xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 p-6 mb-8">
    <!-- Header with Dropdowns -->
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div class="flex items-center gap-2">
            <i class="fas fa-chart-bar text-blue-600 dark:text-blue-400"></i>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Analytics Dashboard</h3>
        </div>
        <div class="flex gap-3">
            <select id="chartSelector" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                <option value="billing" selected>Billing History</option>
                <option value="payment">Payment History</option>
                <option value="consumption">Consumption History</option>
                <option value="revenue">Revenue / Collection Trend</option>
                <option value="arrears">Outstanding Balance / Arrears</option>
            </select>
            <select id="periodSelector" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                <option value="weekly">Weekly</option>
                <option value="monthly" selected>Monthly</option>
                <option value="annually">Annually</option>
            </select>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="flex items-center justify-center gap-8 mb-4">
        <div class="text-center">
            <span id="metricLabel" class="text-sm text-gray-600 dark:text-gray-400">Total Billed</span>
            <p id="metricValue" class="text-2xl font-bold text-blue-600 dark:text-blue-400">₱2,730K</p>
        </div>
        <div class="text-center">
            <span id="metric2Label" class="text-sm text-gray-600 dark:text-gray-400">Average Bill</span>
            <p id="metric2Value" class="text-2xl font-bold text-gray-700 dark:text-gray-300">₱228K</p>
        </div>
    </div>

    <!-- Chart Container -->
    <div class="relative" style="height: 450px;">
        <!-- Skeleton Loader -->
        <div id="chartSkeleton" class="absolute inset-0 flex items-center justify-center">
            <div class="w-full h-full animate-pulse bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                <div class="text-gray-400 dark:text-gray-600">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p class="text-sm">Loading chart...</p>
                </div>
            </div>
        </div>
        <canvas id="mainChart" class="opacity-0 transition-opacity duration-300"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    'use strict';
    
    const chartData = {
        billing: {
            weekly: { labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], data: [285.5, 312.8, 298.2, 325.6] },
            monthly: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], data: [1245.3, 1189.7, 1312.5, 1278.9, 1356.2, 1401.8, 1389.4, 1425.6, 1398.2, 1467.3, 1445.8, 1512.4] },
            annually: { labels: ['2020', '2021', '2022', '2023', '2024'], data: [14250.8, 15123.4, 15876.2, 16542.9, 17223.1] }
        },
        payment: {
            weekly: { labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], data: [268.3, 295.6, 281.4, 307.2] },
            monthly: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], data: [1178.5, 1125.3, 1245.8, 1198.6, 1289.4, 1334.2, 1312.7, 1356.8, 1328.5, 1398.2, 1367.9, 1445.3] },
            annually: { labels: ['2020', '2021', '2022', '2023', '2024'], data: [13456.7, 14389.2, 15123.8, 15789.4, 16481.2] }
        },
        consumption: {
            weekly: { labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], data: [18250, 19875, 19120, 20540] },
            monthly: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], data: [78450, 75230, 82150, 79680, 85420, 88950, 87230, 91240, 89560, 94180, 92340, 97850] },
            annually: { labels: ['2020', '2021', '2022', '2023', '2024'], data: [945280, 1012450, 1068920, 1125680, 1182290] }
        },
        revenue: {
            weekly: { labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], data: [276.9, 304.2, 289.8, 316.4] },
            monthly: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], data: [1211.9, 1157.5, 1279.2, 1238.8, 1322.8, 1368.0, 1351.1, 1391.2, 1363.4, 1432.8, 1406.9, 1478.9] },
            annually: { labels: ['2020', '2021', '2022', '2023', '2024'], data: [13853.8, 14756.3, 15500.0, 16166.2, 16852.2] }
        },
        arrears: {
            weekly: { labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], current: [45.2, 52.8, 48.6, 56.3], overdue: [22.6, 26.4, 24.3, 28.2] },
            monthly: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], current: [185.5, 195.2, 205.8, 198.4, 215.6, 228.3, 221.7, 235.4, 229.8, 242.6, 238.1, 251.9], overdue: [92.8, 97.6, 102.9, 99.2, 107.8, 114.2, 110.9, 117.7, 114.9, 121.3, 119.1, 126.0] },
            annually: { labels: ['2020', '2021', '2022', '2023', '2024'], current: [2145.8, 2289.4, 2412.6, 2534.2, 2678.5], overdue: [1072.9, 1144.7, 1206.3, 1267.1, 1339.3] }
        }
    };

    let myChart = null;
    let state = {
        chartType: 'billing',
        period: 'monthly'
    };

    function isDarkMode() {
        return document.documentElement.classList.contains('dark');
    }

    function createChart() {
        const ctx = document.getElementById('mainChart');
        if (!ctx) return;
        
        // Show skeleton
        const skeleton = document.getElementById('chartSkeleton');
        const canvas = document.getElementById('mainChart');

        const dark = isDarkMode();
        const textColor = dark ? '#9CA3AF' : '#6B7280';
        const gridColor = dark ? '#374151' : 'rgba(229, 231, 235, 0.3)';
        const data = chartData[state.chartType][state.period];

        let config = {
            type: 'bar',
            data: { labels: data.labels, datasets: [] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: dark ? '#1F2937' : '#FFFFFF',
                        titleColor: dark ? '#F9FAFB' : '#111827',
                        bodyColor: dark ? '#F9FAFB' : '#111827',
                        borderColor: dark ? '#374151' : '#E5E7EB',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor } },
                    y: { grid: { color: gridColor, lineWidth: 0.5 }, ticks: { color: textColor } }
                }
            }
        };

        if (state.chartType === 'billing') {
            config.type = 'bar';
            config.data.datasets = [{ label: 'Billing', data: data.data, backgroundColor: '#3B82F6', borderRadius: 6 }];
            config.options.scales.y.ticks.callback = (v) => '₱' + v.toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 1}) + 'K';
            config.options.plugins.tooltip.callbacks = { label: (c) => 'Billing: ₱' + c.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 'K' };
        } else if (state.chartType === 'payment') {
            config.type = 'line';
            config.data.datasets = [{ label: 'Payment', data: data.data, borderColor: '#10B981', backgroundColor: 'rgba(16, 185, 129, 0.1)', tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 5 }];
            config.options.scales.y.ticks.callback = (v) => '₱' + v.toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 1}) + 'K';
            config.options.plugins.tooltip.callbacks = { label: (c) => 'Payment: ₱' + c.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 'K' };
        } else if (state.chartType === 'consumption') {
            config.type = 'line';
            config.data.datasets = [{ label: 'Consumption', data: data.data, borderColor: '#06B6D4', backgroundColor: 'rgba(6, 182, 212, 0.2)', fill: true, tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 5 }];
            config.options.scales.y.ticks.callback = (v) => v.toLocaleString('en-PH') + ' m³';
            config.options.plugins.tooltip.callbacks = { label: (c) => 'Consumption: ' + c.parsed.y.toLocaleString('en-PH') + ' m³' };
        } else if (state.chartType === 'revenue') {
            config.type = 'line';
            config.data.datasets = [{ label: 'Revenue', data: data.data, borderColor: '#8B5CF6', backgroundColor: 'rgba(139, 92, 246, 0.2)', fill: true, tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 5 }];
            config.options.scales.y.ticks.callback = (v) => '₱' + v.toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 1}) + 'K';
            config.options.plugins.tooltip.callbacks = { label: (c) => 'Revenue: ₱' + c.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 'K' };
        } else if (state.chartType === 'arrears') {
            config.type = 'bar';
            config.data.datasets = [
                { label: 'Current Arrears', data: data.current, backgroundColor: '#F59E0B', borderRadius: 6 },
                { label: 'Overdue (>90 days)', data: data.overdue, backgroundColor: '#EF4444', borderRadius: 6 }
            ];
            config.options.plugins.legend = { display: true, position: 'top', align: 'start', labels: { color: textColor } };
            config.options.scales.x.stacked = true;
            config.options.scales.y.stacked = true;
            config.options.scales.y.ticks.callback = (v) => '₱' + v.toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 1}) + 'K';
            config.options.plugins.tooltip.callbacks = { label: (c) => c.dataset.label + ': ₱' + c.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 'K' };
        }

        if (myChart) myChart.destroy();
        myChart = new Chart(ctx, config);
        updateMetrics();
        
        // Hide skeleton, show chart
        setTimeout(() => {
            if (skeleton) skeleton.style.display = 'none';
            if (canvas) canvas.classList.remove('opacity-0');
        }, 100);
    }

    function updateMetrics() {
        const data = chartData[state.chartType][state.period];
        let label1, value1, label2, value2;

        if (state.chartType === 'arrears') {
            label1 = 'Total Arrears';
            value1 = '₱' + data.current.reduce((a, b) => a + b, 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 'K';
            label2 = 'Overdue Amount';
            value2 = '₱' + data.overdue.reduce((a, b) => a + b, 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 'K';
        } else if (state.chartType === 'consumption') {
            const total = data.data.reduce((a, b) => a + b, 0);
            label1 = 'Total Consumption';
            value1 = total.toLocaleString('en-PH') + ' m³';
            label2 = 'Average';
            value2 = Math.round(total / data.data.length).toLocaleString('en-PH') + ' m³';
        } else {
            const total = data.data.reduce((a, b) => a + b, 0);
            const labels = { billing: 'Total Billed', payment: 'Total Collected', revenue: 'Total Revenue' };
            label1 = labels[state.chartType];
            value1 = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 'K';
            label2 = 'Average';
            value2 = '₱' + (total / data.data.length).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 'K';
        }

        document.getElementById('metricLabel').textContent = label1;
        document.getElementById('metricValue').textContent = value1;
        document.getElementById('metric2Label').textContent = label2;
        document.getElementById('metric2Value').textContent = value2;
    }

    document.addEventListener('DOMContentLoaded', function() {
        createChart();

        document.getElementById('chartSelector').addEventListener('change', function() {
            // Optimistic update
            const canvas = document.getElementById('mainChart');
            canvas.classList.add('opacity-0');
            setTimeout(() => {
                state.chartType = this.value;
                createChart();
            }, 150);
        });

        document.getElementById('periodSelector').addEventListener('change', function() {
            // Optimistic update
            const canvas = document.getElementById('mainChart');
            canvas.classList.add('opacity-0');
            setTimeout(() => {
                state.period = this.value;
                createChart();
            }, 150);
        });
    });
})();
</script>
