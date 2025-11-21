@props([
    'type' => 'table', // 'table' or 'chart'
    'targetId' => '',
    'filename' => '',
    'title' => ''
])

@php
$defaultFilename = $type === 'table' ? 'table-export' : 'chart-export';
$actualFilename = $filename ?: $defaultFilename;
$actualTitle = $title ?: ucfirst($type) . ' Report';
@endphp

<div class="flex items-center space-x-2">
    @if($type === 'table')
        <button 
            onclick="ExportPrint.exportTableToCSV('{{ $targetId }}', '{{ $actualFilename }}.csv')"
            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
            title="Export to CSV"
        >
            <i class="fas fa-download mr-1"></i>
            CSV
        </button>
        
        <button 
            onclick="ExportPrint.printTable('{{ $targetId }}', '{{ $actualTitle }}')"
            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
            title="Print Table"
        >
            <i class="fas fa-print mr-1"></i>
            Print
        </button>
    @else
        <button 
            onclick="ExportPrint.exportChartAsImage('{{ $targetId }}', '{{ $actualFilename }}.png')"
            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
            title="Export as Image"
        >
            <i class="fas fa-image mr-1"></i>
            PNG
        </button>
        
        <button 
            onclick="ExportPrint.printChart('{{ $targetId }}', '{{ $actualTitle }}')"
            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
            title="Print Chart"
        >
            <i class="fas fa-print mr-1"></i>
            Print
        </button>
    @endif
</div>