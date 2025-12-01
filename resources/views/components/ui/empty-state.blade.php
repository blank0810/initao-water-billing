<!-- Standardized Empty State Component -->
<div class="flex flex-col items-center justify-center py-12 px-4">
    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
        <i class="fas {{ $icon ?? 'fa-inbox' }} text-3xl text-gray-400 dark:text-gray-600"></i>
    </div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $title ?? 'No data found' }}</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 text-center max-w-sm">{{ $message ?? 'There are no records to display at this time.' }}</p>
    @if(isset($action))
    <button onclick="{{ $actionClick }}" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
        <i class="fas {{ $actionIcon ?? 'fa-plus' }} mr-2"></i>{{ $action }}
    </button>
    @endif
</div>
