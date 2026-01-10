<!-- Rate Detail Modal Component -->
@props([
    'id' => 'rate-detail-modal',
    'title' => 'Add Rate Detail',
    'rateId' => null
])

<div id="{{ $id }}" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-md w-full max-h-screen overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-layer-group mr-2"></i>{{ $title }}
            </h3>
            <button 
                onclick="closeRateDetailModal()" 
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tier Level <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="tier-level" 
                    placeholder="e.g., Tier 1"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Min (m³) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="min-consumption" 
                        placeholder="0"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Max (m³) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="max-consumption" 
                        placeholder="10"
                        min="0"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Amount per m³ <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 dark:text-gray-400">₱</span>
                    <input 
                        type="number" 
                        id="rate-amount" 
                        placeholder="0.00"
                        min="0"
                        step="0.01"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <span class="text-gray-500 dark:text-gray-400">/m³</span>
                </div>
            </div>

            <!-- Effective Date Range -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Effective From <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="effective-from"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Effective To
                    </label>
                    <input 
                        type="date" 
                        id="effective-to"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select 
                    id="detail-status"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                <p class="text-xs text-blue-800 dark:text-blue-200">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Range Example:</strong> Tier 1 covers 0-10 m³. If a consumer uses 15 m³, they pay for 10 m³ in Tier 1 and 5 m³ in Tier 2.
                </p>
            </div>

            <!-- Calculation Preview -->
            <div id="calc-preview" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 hidden">
                <p class="text-xs text-green-800 dark:text-green-200">
                    <strong>Calculation:</strong> <span id="calc-text"></span>
                </p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex gap-3">
            <button 
                onclick="saveRateDetail()" 
                class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
            >
                <i class="fas fa-save mr-2"></i>Save Detail
            </button>
            <button 
                onclick="closeRateDetailModal()" 
                class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg font-medium transition"
            >
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
        </div>
    </div>
</div>

<script>
function openRateDetailModal() {
    document.getElementById('{{ $id }}').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRateDetailModal() {
    document.getElementById('{{ $id }}').classList.add('hidden');
    document.body.style.overflow = 'auto';
    resetRateDetailForm();
}

function resetRateDetailForm() {
    document.getElementById('tier-level').value = '';
    document.getElementById('min-consumption').value = '';
    document.getElementById('max-consumption').value = '';
    document.getElementById('rate-amount').value = '';
    document.getElementById('effective-from').value = '';
    document.getElementById('effective-to').value = '';
    document.getElementById('detail-status').value = 'active';
    document.getElementById('calc-preview').classList.add('hidden');
}

function saveRateDetail() {
    const tierLevel = document.getElementById('tier-level').value;
    const minConsumption = parseFloat(document.getElementById('min-consumption').value);
    const maxConsumption = parseFloat(document.getElementById('max-consumption').value);
    const rateAmount = parseFloat(document.getElementById('rate-amount').value);
    const effectiveFrom = document.getElementById('effective-from').value;

    // Validation
    if (!tierLevel || isNaN(minConsumption) || isNaN(maxConsumption) || isNaN(rateAmount)) {
        alert('Please fill all required fields');
        return;
    }

    if (minConsumption >= maxConsumption) {
        alert('Maximum consumption must be greater than minimum');
        return;
    }

    if (rateAmount <= 0) {
        alert('Rate amount must be greater than 0');
        return;
    }

    if (!effectiveFrom) {
        alert('Effective from date is required');
        return;
    }

    // Save action
    console.log({
        tierLevel,
        minConsumption,
        maxConsumption,
        rateAmount,
        effectiveFrom
    });

    alert('Rate detail saved successfully!');
    closeRateDetailModal();
}

// Update calculation preview on input change
document.addEventListener('DOMContentLoaded', function() {
    const inputs = [
        document.getElementById('min-consumption'),
        document.getElementById('max-consumption'),
        document.getElementById('rate-amount')
    ];

    inputs.forEach(input => {
        input.addEventListener('change', function() {
            const min = parseFloat(document.getElementById('min-consumption').value) || 0;
            const max = parseFloat(document.getElementById('max-consumption').value) || 0;
            const rate = parseFloat(document.getElementById('rate-amount').value) || 0;

            if (max > 0 && rate > 0) {
                const calcText = `${min}-${max} m³ @ ₱${rate.toFixed(2)}/m³ = ₱${((max - min) * rate).toFixed(2)} max charge`;
                document.getElementById('calc-text').textContent = calcText;
                document.getElementById('calc-preview').classList.remove('hidden');
            }
        });
    });
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRateDetailModal();
    }
});

// Close on background click
document.getElementById('{{ $id }}').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRateDetailModal();
    }
});

window.openRateDetailModal = openRateDetailModal;
window.closeRateDetailModal = closeRateDetailModal;
window.saveRateDetail = saveRateDetail;
</script>
