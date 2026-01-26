@props([
    'statuses' => [],
    'placeholder' => 'Search...',
])

<div class="flex items-center gap-4 mb-4">
    <!-- Search Input -->
    <div class="flex-1">
        <div class="relative">
            <input
                type="text"
                x-model="search"
                @input.debounce.500ms="$dispatch('search')"
                placeholder="{{ $placeholder }}"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            />
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    @if(count($statuses) > 0)
    <!-- Status Filter -->
    <div class="w-48">
        <select
            x-model="statusFilter"
            @change="$dispatch('search')"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
        >
            @foreach($statuses as $status)
            <option value="{{ is_array($status) ? $status['value'] : $status }}">
                {{ is_array($status) ? $status['label'] : $status }}
            </option>
            @endforeach
        </select>
    </div>
    @endif
</div>
