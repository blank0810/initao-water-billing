<!-- Rate Detail Table Component -->
@props([
    'id' => 'rate-detail-table',
    'title' => 'Rate Details (Increments)',
    'details' => [],
    'rateId' => null,
    'editable' => false,
    'icon' => 'fas fa-layer-group'
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="{{ $icon }} mr-2"></i>{{ $title }}
            </h3>
            @if ($editable)
            <button 
                onclick="openRateDetailModal()" 
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
            >
                <i class="fas fa-plus mr-2"></i>Add Increment
            </button>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Tier</th>
                    <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Range (m³)</th>
                    <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Rate/m³</th>
                    <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Status</th>
                    @if ($editable)
                    <th class="text-center py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($details as $detail)
                <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="py-3 px-4 text-gray-900 dark:text-white font-semibold">{{ $detail['tier'] ?? 'N/A' }}</td>
                    <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $detail['min'] }} - {{ $detail['max'] }} m³</td>
                    <td class="py-3 px-4 text-right text-gray-900 dark:text-white font-bold text-blue-600 dark:text-blue-400">
                        ₱{{ number_format($detail['rate'], 2) }}
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-xs font-semibold">
                            {{ $detail['status'] ?? 'Active' }}
                        </span>
                    </td>
                    @if ($editable)
                    <td class="py-3 px-4 text-center">
                        <button 
                            onclick="editRateDetail('{{ $detail['id'] ?? '' }}')" 
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 font-medium text-sm mr-3"
                        >
                            <i class="fas fa-edit"></i>
                        </button>
                        <button 
                            onclick="deleteRateDetail('{{ $detail['id'] ?? '' }}')" 
                            class="text-red-600 dark:text-red-400 hover:text-red-800 font-medium text-sm"
                        >
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $editable ? 5 : 4 }}" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-3xl mb-2 block opacity-50"></i>
                        No rate details configured
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer Info -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <strong>Total Tiers:</strong> {{ count($details) }} | 
            <strong>Lowest Rate:</strong> ₱{{ isset($details[0]) ? number_format($details[0]['rate'], 2) : '0.00' }}/m³
        </p>
    </div>
</div>

<script>
function openRateDetailModal() {
    // Trigger modal for adding new rate detail
    const modal = document.getElementById('rate-detail-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function editRateDetail(detailId) {
    // Load detail data and open modal in edit mode
    console.log('Edit rate detail:', detailId);
    openRateDetailModal();
}

function deleteRateDetail(detailId) {
    if (confirm('Are you sure you want to delete this rate detail? This action cannot be undone.')) {
        // Perform delete action
        console.log('Delete rate detail:', detailId);
    }
}
</script>
