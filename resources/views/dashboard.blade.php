<x-app-layout>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Main Content -->
        <main class="p-6">


            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Customers -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Customers</p>
                            <p id="totalCustomers" class="text-3xl font-bold mt-2">0</p>
                            <p class="text-xs text-blue-100 mt-2">
                                <span class="font-medium">+5.2%</span> from last month
                            </p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-user-friends text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Total Consumers -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total Consumers</p>
                            <p id="totalConsumers" class="text-3xl font-bold mt-2">0</p>
                            <p class="text-xs text-green-100 mt-2">
                                <span class="font-medium">+8.1%</span> from last month
                            </p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Total Users</p>
                            <p id="totalUsers" class="text-3xl font-bold mt-2">0</p>
                            <p class="text-xs text-orange-100 mt-2">
                                <span class="font-medium">+3</span> new this month
                            </p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-user-cog text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Overall Bill Paid -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Overall Bill Paid</p>
                            <p id="totalBillPaid" class="text-3xl font-bold mt-2">₱0</p>
                            <p class="text-xs text-purple-100 mt-2">
                                <span class="font-medium">+8%</span> from last month
                            </p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-file-invoice-dollar text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- First Row: Revenue & Payment Status -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Revenue Trend</h3>
                        <a href="{{ route('billing.overall-data') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View Details</a>
                    </div>
                    <div class="relative h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Status Distribution</h3>
                        <a href="{{ route('billing.overall-data') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View Details</a>
                    </div>
                    <div class="relative h-64">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Second Row: Consumption Analytics -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Consumption Trend</h3>
                    <a href="{{ route('meter.overall-data') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View Details</a>
                </div>
                <div class="relative h-80">
                    <canvas id="consumptionChart"></canvas>
                </div>
            </div>

            <!-- Third Row: Income vs Expenses & Rate Structure -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Income vs Expenses</h3>
                        <a href="{{ route('ledger.overall-data') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View Details</a>
                    </div>
                    <div class="relative h-64">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Rate Structure by Tier</h3>
                        <a href="{{ route('rate.overall-data') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View Details</a>
                    </div>
                    <div class="relative h-64">
                        <canvas id="rateStructureChart"></canvas>
                    </div>
                </div>
            </div>

        </main>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/customer-data.js') }}"></script>
<script src="{{ asset('js/data/consumer/consumer.js') }}"></script>
<script src="{{ asset('js/data/user/user.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateCounts();
        initializeDashboardCharts();
    });
    
    function updateCounts() {
        // Update Total Customers
        const customers = window.customerAllData || [];
        document.getElementById('totalCustomers').textContent = customers.length;
        
        // Update Total Consumers
        const consumers = window.consumerData || [];
        document.getElementById('totalConsumers').textContent = consumers.length;
        
        // Update Total Users
        const users = window.userAllData || [];
        document.getElementById('totalUsers').textContent = users.length;
        
        // Calculate Overall Bill Paid from consumers
        const totalBill = consumers.reduce((sum, consumer) => {
            const billAmount = parseFloat(consumer.total_bill.replace(/[₱,]/g, ''));
            return sum + billAmount;
        }, 0);
        document.getElementById('totalBillPaid').textContent = `₱${(totalBill / 1000).toFixed(1)}K`;
    }
    
    window.updateCounts = updateCounts;

function initializeDashboardCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [320000, 285000, 340000, 310000, 365000, 380000],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });

    // Payment Status Chart
    const paymentCtx = document.getElementById('paymentStatusChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Overdue', 'Pending'],
            datasets: [{
                data: [65, 20, 15],
                backgroundColor: ['#10B981', '#EF4444', '#F59E0B']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Consumption Chart
    const consumptionCtx = document.getElementById('consumptionChart').getContext('2d');
    new Chart(consumptionCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Consumption (m³)',
                data: [42000, 38500, 45200, 41800, 47300, 45680],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });

    // Income vs Expenses Chart
    const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
    new Chart(incomeExpenseCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Income',
                data: [520000, 485000, 560000, 530000, 595000, 560000],
                backgroundColor: '#10B981'
            }, {
                label: 'Expenses',
                data: [320000, 285000, 340000, 310000, 365000, 330000],
                backgroundColor: '#EF4444'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });

    // Rate Structure Chart
    const rateStructureCtx = document.getElementById('rateStructureChart').getContext('2d');
    new Chart(rateStructureCtx, {
        type: 'bar',
        data: {
            labels: ['0-10 m³', '11-20 m³', '21-30 m³', '31-50 m³', '50+ m³'],
            datasets: [{
                label: 'Rate (₱/m³)',
                data: [15.00, 22.50, 28.00, 35.00, 42.50],
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });
}
</script>