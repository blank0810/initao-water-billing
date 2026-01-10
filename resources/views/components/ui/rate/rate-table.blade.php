<!-- Reusable Rate Table Component -->
@props([
    'id' => 'rate-table',
    'title' => 'Rates',
    'headers' => [],
    'rows' => [],
    'searchable' => true,
    'exportable' => true,
    'icon' => 'fas fa-list'
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="{{ $icon }} mr-2"></i>{{ $title }}
            </h3>
            <div class="flex gap-2">
                @if ($exportable)
                    <button onclick="new ActionFunctionsManager('{{ $id }}').exportToExcel()" 
                        class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-medium transition">
                        <i class="fas fa-file-excel mr-1"></i>Excel
                    </button>
                    <button onclick="new ActionFunctionsManager('{{ $id }}').exportToPDF()" 
                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-medium transition">
                        <i class="fas fa-file-pdf mr-1"></i>PDF
                    </button>
                @endif
            </div>
        </div>

        <!-- Search Bar -->
        @if ($searchable)
            <div class="flex gap-2">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="search-{{ $id }}"
                        placeholder="Search rates..." 
                        class="w-full px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <i class="fas fa-search absolute right-3 top-2.5 text-gray-400"></i>
                </div>
                <button onclick="new ActionFunctionsManager('{{ $id }}').clearFilters()" 
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition">
                    <i class="fas fa-times mr-1"></i>Clear
                </button>
            </div>
        @endif
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="{{ $id }}" class="w-full">
            <thead>
                <tr class="border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    @foreach ($headers as $header)
                        <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        @foreach ($row as $cell)
                            <td class="py-3 px-4 text-gray-900 dark:text-white">
                                {!! $cell !!}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2 block opacity-50"></i>
                            No rates found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
        <span>Total Rates: <strong>{{ count($rows) }}</strong></span>
        <div id="pagination-{{ $id }}" class="flex gap-2"></div>
    </div>
</div>

@vite(['resources/js/utils/action-functions.js'])

<script>
document.addEventListener('DOMContentLoaded', function() {
    const manager = new ActionFunctionsManager('{{ $id }}');
    
    @if ($searchable)
    document.getElementById('search-{{ $id }}').addEventListener('keyup', function() {
        manager.searchTable(this.value);
    });
    @endif
});
</script>
