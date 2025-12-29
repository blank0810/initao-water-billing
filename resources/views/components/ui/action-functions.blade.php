@props([
    'searchPlaceholder' => 'Search...',
    'filterLabel' => 'Filter',
    'filterOptions' => [],
    'showDateFilter' => false,
    'showExport' => true,
    'tableId' => 'dataTable'
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
    <div class="flex flex-wrap gap-3 items-center">
        <!-- Search -->
        <div class="flex-1 min-w-[250px]">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input 
                    type="text" 
                    id="{{ $tableId }}_search"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
        </div>

        @if(count($filterOptions) > 0)
        <!-- Custom Filter -->
        <div class="w-48">
            <select 
                id="{{ $tableId }}_filter"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500"
            >
                <option value="">{{ $filterLabel }}</option>
                @foreach($filterOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if($showDateFilter)
        <!-- Date Filters -->
        <div class="w-40">
            <input 
                type="date" 
                id="{{ $tableId }}_dateFrom"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500"
            >
        </div>
        <div class="w-40">
            <input 
                type="date" 
                id="{{ $tableId }}_dateTo"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500"
            >
        </div>
        @endif

        <!-- Clear Filters -->
        <button 
            id="{{ $tableId }}_clearBtn"
            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition"
        >
            <i class="fas fa-times mr-1"></i>Clear
        </button>

        @if($showExport)
        <!-- Export Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button 
                @click="open = !open"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition flex items-center gap-2"
            >
                <i class="fas fa-download"></i>
                Export
                <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <div 
                x-show="open" 
                @click.away="open = false"
                x-transition
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-20"
            >
                <button 
                    id="{{ $tableId }}_exportExcel"
                    @click="open = false"
                    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-t-lg flex items-center gap-2"
                >
                    <i class="fas fa-file-excel text-green-600"></i>
                    Export to Excel
                </button>
                <button 
                    id="{{ $tableId }}_exportPDF"
                    @click="open = false"
                    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-b-lg flex items-center gap-2"
                >
                    <i class="fas fa-file-pdf text-red-600"></i>
                    Export to PDF
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
