<!-- Rate Parent Form Component -->
@props([
    'id' => 'rate-parent-form',
    'action' => '/rate/parent/store',
    'method' => 'POST',
    'period' => null
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 space-y-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
        <i class="fas fa-calendar mr-2"></i>{{ isset($period) ? 'Edit Billing Period' : 'Create Billing Period' }}
    </h3>

    <form id="{{ $id }}" action="{{ $action }}" method="{{ strtoupper($method) }}" class="space-y-6">
        @csrf
        @if ($method === 'PUT')
            @method('PUT')
        @endif

        <!-- Period Name -->
        <div>
            <label for="period-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Period Name <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="period-name" 
                name="period_name"
                value="{{ $period->period_name ?? '' }}"
                placeholder="e.g., January 2024"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Must be unique per billing period</p>
        </div>

        <!-- Period ID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="period-id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Period ID <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="period-id" 
                    name="period_id"
                    value="{{ $period->period_id ?? '' }}"
                    placeholder="BP-2024-01"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
            </div>

            <!-- Status -->
            <div>
                <label for="period-status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select 
                    id="period-status" 
                    name="status"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="open" {{ ($period->status ?? '') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ ($period->status ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="archived" {{ ($period->status ?? '') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="start-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Start Date <span class="text-red-500">*</span>
                </label>
                <input 
                    type="date" 
                    id="start-date" 
                    name="start_date"
                    value="{{ $period->start_date ?? '' }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
            </div>

            <div>
                <label for="end-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    End Date <span class="text-red-500">*</span>
                </label>
                <input 
                    type="date" 
                    id="end-date" 
                    name="end_date"
                    value="{{ $period->end_date ?? '' }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Description
            </label>
            <textarea 
                id="description" 
                name="description"
                rows="3"
                placeholder="Additional notes about this billing period..."
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >{{ $period->description ?? '' }}</textarea>
        </div>

        <!-- Associated Rates Info -->
        @if (isset($period))
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Associated Rates:</strong> After saving this period, you can assign rates to it from the Rate Details page.
            </p>
        </div>
        @endif

        <!-- Form Actions -->
        <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button 
                type="submit" 
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
            >
                <i class="fas fa-save mr-2"></i>{{ isset($period) ? 'Update Period' : 'Create Period' }}
            </button>
            <button 
                type="reset" 
                class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition"
            >
                <i class="fas fa-undo mr-2"></i>Reset
            </button>
            <a 
                href="/rate" 
                class="px-6 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg font-medium transition"
            >
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>
</div>

<script>
// Auto-generate Period ID from Period Name if not manually changed
document.getElementById('period-name').addEventListener('blur', function() {
    const periodIdField = document.getElementById('period-id');
    if (!periodIdField.value) {
        const name = this.value.trim().replace(/\s+/g, '-').toUpperCase();
        const year = new Date().getFullYear();
        const month = String(new Date().getMonth() + 1).padStart(2, '0');
        periodIdField.value = `BP-${year}-${month}`;
    }
});

// Validate date range
document.getElementById('{{ $id }}').addEventListener('submit', function(e) {
    const startDate = new Date(document.getElementById('start-date').value);
    const endDate = new Date(document.getElementById('end-date').value);
    
    if (startDate >= endDate) {
        e.preventDefault();
        alert('End date must be after start date');
        return false;
    }
});
</script>
