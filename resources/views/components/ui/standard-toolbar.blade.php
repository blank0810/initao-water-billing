@props([
    'searchId' => 'search',
    'searchPlaceholder' => 'Search...',
    'showFilters' => true,
    'showExport' => true,
    'primaryAction' => null,
    'primaryActionText' => 'Add',
    'primaryActionIcon' => 'plus'
])

<div {{ $attributes->merge(['class' => 'mb-4 flex items-center justify-between']) }}>
    <div class="flex-1 max-w-md">
        <input type="text" 
               id="{{ $searchId }}" 
               placeholder="{{ $searchPlaceholder }}" 
               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    </div>
    <div class="flex items-center gap-2">
        @if($showFilters && isset($filters))
            {{ $filters }}
        @endif
        
        @if($showExport)
            <button onclick="window.print()" class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm" title="Print">
                <i class="fas fa-print"></i>
            </button>
            <button onclick="exportPDF()" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" title="Download PDF">
                <i class="fas fa-download"></i>
            </button>
            <button onclick="exportExcel()" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm" title="Export to Excel">
                <i class="fas fa-file-export"></i>
            </button>
        @endif
        
        @if($primaryAction)
            <a href="{{ $primaryAction }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                <i class="fas fa-{{ $primaryActionIcon }} mr-2"></i>{{ $primaryActionText }}
            </a>
        @endif
        
        {{ $slot }}
    </div>
</div>
