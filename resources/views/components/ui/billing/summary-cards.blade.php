<div id="billingSummaryCards" x-data="billingSummaryData()" x-init="loadSummary()" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 w-full">
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm font-medium">Outstanding Balance</p>
                <p id="card-outstanding" class="text-3xl font-bold mt-2" x-text="'₱' + formatNumber(summary.outstanding_balance)">₱0.00</p>
            </div>
            <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-exclamation-circle text-2xl"></i>
            </div>
        </div>
        <p class="text-red-100 text-xs mt-2">Total unpaid bills</p>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Total Paid</p>
                <p id="card-total-paid" class="text-3xl font-bold mt-2" x-text="'₱' + formatNumber(summary.total_paid)">₱0.00</p>
            </div>
            <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
        </div>
        <p class="text-green-100 text-xs mt-2">All time collections</p>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm font-medium">Overdue Bills</p>
                <p id="card-overdue-bills" class="text-3xl font-bold mt-2" x-text="summary.overdue_bills">0</p>
            </div>
            <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
        </div>
        <p class="text-orange-100 text-xs mt-2">Past due date</p>
    </div>

    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Total Adjustments</p>
                <p id="card-total-adjustments" class="text-3xl font-bold mt-2" x-text="'₱' + formatNumber(summary.total_adjustments)">₱0.00</p>
            </div>
            <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-edit text-2xl"></i>
            </div>
        </div>
        <p class="text-blue-100 text-xs mt-2">Discounts & penalties</p>
    </div>
</div>

<script>
function billingSummaryData() {
    return {
        summary: {
            outstanding_balance: 0,
            total_paid: 0,
            overdue_bills: 0,
            total_adjustments: 0
        },

        async loadSummary() {
            try {
                const response = await fetch('/water-bills/summary');
                const result = await response.json();
                if (result.success && result.data) {
                    this.summary = result.data;
                }
            } catch (error) {
                console.error('Error loading billing summary:', error);
            }
        },

        formatNumber(value) {
            const num = parseFloat(value);
            return isNaN(num) ? '0.00' : num.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}

// Expose refresh function for external calls
window.refreshBillingSummary = function() {
    const component = document.querySelector('[x-data="billingSummaryData()"]');
    if (component && component.__x) {
        component.__x.$data.loadSummary();
    }
};
</script>
