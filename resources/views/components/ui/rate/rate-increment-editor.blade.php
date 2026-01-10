<!-- Rate Increment Editor Component -->
@props([
    'id' => 'rate-increment-editor',
    'rateId' => null,
    'increments' => []
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            <i class="fas fa-layer-group mr-2"></i>Manage Rate Increments
        </h3>
        <button 
            onclick="addNewIncrement()" 
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition"
        >
            <i class="fas fa-plus mr-2"></i>Add Increment
        </button>
    </div>

    <p class="text-sm text-gray-600 dark:text-gray-400">
        Define consumption tiers and rates. Increments are applied progressively - the first tier applies to the first m³, second tier to the next m³, etc.
    </p>

    <!-- Existing Increments List -->
    <div id="increments-list" class="space-y-3">
        @forelse ($increments as $index => $increment)
        <div class="increment-row bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-3">
                <!-- Tier Label -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tier Name</label>
                    <input 
                        type="text" 
                        value="{{ $increment['tier_name'] ?? '' }}"
                        placeholder="Tier 1"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <!-- Min Consumption -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Min (m³)</label>
                    <input 
                        type="number" 
                        value="{{ $increment['min_consumption'] ?? 0 }}"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <!-- Max Consumption -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Max (m³)</label>
                    <input 
                        type="number" 
                        value="{{ $increment['max_consumption'] ?? 10 }}"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <!-- Rate per m³ -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Rate/m³ (₱)</label>
                    <input 
                        type="number" 
                        value="{{ $increment['rate_per_cubic_meter'] ?? 0 }}"
                        min="0"
                        step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <!-- Action -->
                <div class="flex items-end gap-2">
                    <button 
                        type="button"
                        onclick="removeIncrement(this)" 
                        class="flex-1 px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-medium transition"
                    >
                        <i class="fas fa-trash mr-1"></i>Remove
                    </button>
                </div>
            </div>

            <!-- Increment Info -->
            <div class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded p-2">
                <i class="fas fa-info-circle mr-1"></i>
                When consumption reaches <strong>{{ $increment['min_consumption'] ?? 0 }}-{{ $increment['max_consumption'] ?? 10 }} m³</strong>, 
                charge <strong>₱{{ number_format($increment['rate_per_cubic_meter'] ?? 0, 2) }}/m³</strong>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <i class="fas fa-inbox text-3xl mb-2 block opacity-50"></i>
            <p>No increments defined yet. Add one to get started.</p>
        </div>
        @endforelse
    </div>

    <!-- Add New Increment Template (Hidden) -->
    <template id="increment-template">
        <div class="increment-row bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tier Name</label>
                    <input 
                        type="text" 
                        placeholder="Tier 2"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Min (m³)</label>
                    <input 
                        type="number" 
                        placeholder="11"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Max (m³)</label>
                    <input 
                        type="number" 
                        placeholder="20"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Rate/m³ (₱)</label>
                    <input 
                        type="number" 
                        placeholder="12.00"
                        min="0"
                        step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div class="flex items-end gap-2">
                    <button 
                        type="button"
                        onclick="removeIncrement(this)" 
                        class="flex-1 px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-medium transition"
                    >
                        <i class="fas fa-trash mr-1"></i>Remove
                    </button>
                </div>
            </div>

            <div class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded p-2">
                <i class="fas fa-info-circle mr-1"></i>
                <span class="increment-description">When consumption reaches ...</span>
            </div>
        </div>
    </template>

    <!-- Summary -->
    <div id="increment-summary" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">
            <i class="fas fa-chart-bar mr-2"></i>Increment Summary
        </h4>
        <p class="text-sm text-blue-800 dark:text-blue-200">
            <strong id="total-increments">{{ count($increments) }}</strong> tier(s) configured
        </p>
    </div>

    <!-- Save Button -->
    <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button 
            onclick="saveAllIncrements()" 
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
        >
            <i class="fas fa-save mr-2"></i>Save All Increments
        </button>
        <button 
            type="reset" 
            class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition"
        >
            <i class="fas fa-undo mr-2"></i>Reset
        </button>
    </div>
</div>

<script>
function addNewIncrement() {
    const template = document.getElementById('increment-template');
    const clone = template.content.cloneNode(true);
    document.getElementById('increments-list').appendChild(clone);
    updateIncrementSummary();
}

function removeIncrement(button) {
    if (confirm('Remove this increment?')) {
        button.closest('.increment-row').remove();
        updateIncrementSummary();
    }
}

function updateIncrementSummary() {
    const count = document.querySelectorAll('.increment-row').length;
    document.getElementById('total-increments').textContent = count;
}

function saveAllIncrements() {
    const increments = [];
    
    document.querySelectorAll('.increment-row').forEach(row => {
        const inputs = row.querySelectorAll('input');
        increments.push({
            tier_name: inputs[0].value,
            min_consumption: parseFloat(inputs[1].value),
            max_consumption: parseFloat(inputs[2].value),
            rate_per_cubic_meter: parseFloat(inputs[3].value)
        });
    });

    // Validation
    if (increments.length === 0) {
        alert('Please add at least one increment');
        return;
    }

    // Check for overlaps and gaps
    increments.sort((a, b) => a.min_consumption - b.min_consumption);
    for (let i = 0; i < increments.length - 1; i++) {
        if (increments[i].max_consumption !== increments[i + 1].min_consumption) {
            alert('Increments must be continuous with no gaps');
            return;
        }
    }

    console.log('Saving increments:', increments);
    alert('Increments saved successfully!');
}

window.addNewIncrement = addNewIncrement;
window.removeIncrement = removeIncrement;
window.saveAllIncrements = saveAllIncrements;
</script>
