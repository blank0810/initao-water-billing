@props([
    'canEdit' => true,
    'canView' => true,
    'canDelete' => true,
])

<div class="flex items-center gap-2">
    @if($canView)
    <button
        @click="$dispatch('view')"
        class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
        title="View Details"
    >
        <i class="fas fa-eye text-sm"></i>
    </button>
    @endif

    @if($canEdit)
    <button
        @click="$dispatch('edit')"
        class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
        title="Edit"
    >
        <i class="fas fa-edit text-sm"></i>
    </button>
    @endif

    @if($canDelete)
    <button
        @click="$dispatch('delete')"
        class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
        title="Delete"
    >
        <i class="fas fa-trash text-sm"></i>
    </button>
    @endif
</div>
