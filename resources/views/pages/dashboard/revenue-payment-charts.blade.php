<!-- Monthly Payment Summary -->
<div class="rounded-2xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 p-6">
    <!-- Header with Icon -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <i class="fas fa-chart-line text-blue-600 dark:text-blue-400"></i>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Monthly Payment Summary</h3>
        </div>
        <select id="monthSelector" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
            <option value="0">January 2024</option>
            <option value="1">February 2024</option>
            <option value="2">March 2024</option>
            <option value="3">April 2024</option>
            <option value="4">May 2024</option>
            <option value="5" selected>June 2024</option>
        </select>
    </div>

    <!-- Summary Stats -->
    <div class="flex items-center justify-center gap-8 mb-4">
        <div class="text-center">
            <div class="flex items-center justify-center gap-1.5 mb-1">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">Total Paid</span>
            </div>
            <p id="totalPaid" class="text-xl font-bold text-green-600 dark:text-green-400">₱380K</p>
        </div>
        <div class="text-center">
            <div class="flex items-center justify-center gap-1.5 mb-1">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">Total Unpaid</span>
            </div>
            <p id="totalUnpaid" class="text-xl font-bold text-red-600 dark:text-red-400">₱120K</p>
        </div>
    </div>

    <!-- Chart -->
    <div class="relative" style="height: 300px;">
        <!-- Skeleton Loader -->
        <div id="paymentChartSkeleton" class="absolute inset-0 animate-pulse bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
            <div class="text-gray-400 dark:text-gray-600">
                <i class="fas fa-chart-line text-3xl mb-2"></i>
                <p class="text-xs">Loading...</p>
            </div>
        </div>
        <canvas id="paymentSummaryChart" class="opacity-0 transition-opacity duration-300"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    'use strict';
    
    const monthlyData = [
        { paid: 1178.5, unpaid: 66.8 },
        { paid: 1125.3, unpaid: 64.4 },
        { paid: 1245.8, unpaid: 66.7 },
        { paid: 1198.6, unpaid: 80.3 },
        { paid: 1289.4, unpaid: 66.8 },
        { paid: 1334.2, unpaid: 67.6 }
    ];

    let myChart = null;
    let selectedMonth = 5;

    function isDarkMode() {
        return document.documentElement.classList.contains('dark');
    }

    function createChart() {
        const ctx = document.getElementById('paymentSummaryChart');
        if (!ctx) return;
        
        const skeleton = document.getElementById('paymentChartSkeleton');
        const canvas = document.getElementById('paymentSummaryChart');

        const dark = isDarkMode();
        const textColor = dark ? '#9CA3AF' : '#6B7280';
        const gridColor = dark ? '#374151' : 'rgba(229, 231, 235, 0.3)';

        const pointRadii = [0, 0, 0, 0, 0, 0];
        const pointBgColors = ['transparent', 'transparent', 'transparent', 'transparent', 'transparent', 'transparent'];
        pointRadii[selectedMonth] = 6;
        pointBgColors[selectedMonth] = '#3B82F6';

        const config = {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Unpaid',
                    data: monthlyData.map(d => d.unpaid),
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.2)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: pointRadii,
                    pointBackgroundColor: pointBgColors,
                    pointBorderColor: pointBgColors,
                    pointHoverRadius: 5
                }, {
                    label: 'Paid',
                    data: monthlyData.map(d => d.paid),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: pointRadii,
                    pointBackgroundColor: pointBgColors,
                    pointBorderColor: pointBgColors,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: dark ? '#1F2937' : '#FFFFFF',
                        titleColor: dark ? '#F9FAFB' : '#111827',
                        bodyColor: dark ? '#F9FAFB' : '#111827',
                        borderColor: dark ? '#374151' : '#E5E7EB',
                        borderWidth: 1,
                        callbacks: { label: (c) => c.dataset.label + ': ₱' + c.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 'K' }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor } },
                    y: {
                        grid: { color: gridColor, lineWidth: 0.5 },
                        ticks: { color: textColor, callback: (v) => '₱' + v.toLocaleString('en-PH', {minimumFractionDigits: 0, maximumFractionDigits: 1}) + 'K' }
                    }
                }
            }
        };

        if (myChart) myChart.destroy();
        myChart = new Chart(ctx, config);
        
        // Hide skeleton, show chart
        setTimeout(() => {
            if (skeleton) skeleton.style.display = 'none';
            if (canvas) canvas.classList.remove('opacity-0');
        }, 100);
    }

    document.addEventListener('DOMContentLoaded', function() {
        createChart();

        document.getElementById('monthSelector').addEventListener('change', function() {
            selectedMonth = parseInt(this.value);
            const data = monthlyData[selectedMonth];
            
            // Optimistic update: Update stats immediately
            document.getElementById('totalPaid').textContent = '₱' + data.paid.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 'K';
            document.getElementById('totalUnpaid').textContent = '₱' + data.unpaid.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 'K';
            
            // Smooth transition
            const canvas = document.getElementById('paymentSummaryChart');
            canvas.classList.add('opacity-0');
            setTimeout(() => {
                createChart();
            }, 150);
        });
    });
})();
</script>
