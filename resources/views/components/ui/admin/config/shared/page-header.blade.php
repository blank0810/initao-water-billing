@props([
    'title' => '',
    'subtitle' => '',
    'canCreate' => false,
    'createLabel' => 'Add New',
])

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $title }}</h1>
        @if($subtitle)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
        @endif
    </div>

    @if($canCreate)
    <button
        @click="openCreateModal()"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center gap-2"
    >
        <i class="fas fa-plus text-sm"></i>
        <span>{{ $createLabel }}</span>
    </button>
    @endif
</div>
