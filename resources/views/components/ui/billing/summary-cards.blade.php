<div id="billingSummaryCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm font-medium">Outstanding Balance</p>
                <p id="card-outstanding" class="text-3xl font-bold mt-2">₱0.00</p>
            </div>
            <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-exclamation-circle text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Total Paid</p>
                <p id="card-total-paid" class="text-3xl font-bold mt-2">₱0.00</p>
            </div>
            <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm font-medium">Overdue Bills</p>
                <p id="card-overdue-bills" class="text-3xl font-bold mt-2">0</p>
            </div>
            <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Total Adjustments</p>
                <p id="card-total-adjustments" class="text-3xl font-bold mt-2">₱0.00</p>
            </div>
            <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-edit text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">Active Consumers</p>
                <p id="card-active-consumers" class="text-3xl font-bold mt-2">0</p>
            </div>
            <div class="bg-white/20 p-3 rounded-lg">
                <i class="fas fa-users text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<script>
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (window.billing) {
            billing.updateSummaryCards();
        }
    });
} else {
    if (window.billing) {
        billing.updateSummaryCards();
    }
}
</script>
