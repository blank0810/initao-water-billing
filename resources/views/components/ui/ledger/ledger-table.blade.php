<!-- Ledger Table Component -->
@props([
    'id' => 'ledger-table',
    'title' => 'Ledger Entries',
    'entries' => [],
    'searchable' => true,
    'filterable' => true,
    'icon' => 'fas fa-list'
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="{{ $icon }} mr-2"></i>{{ $title }}
            </h3>
            <div class="flex gap-2">
                <button class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-medium transition">
                    <i class="fas fa-file-excel mr-1"></i>Excel
                </button>
                <button class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-medium transition">
                    <i class="fas fa-file-pdf mr-1"></i>PDF
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        @if ($searchable)
            <div class="flex gap-2">
                <div class="flex-1 relative">
                    <input 
                        type="text" 
                        id="search-{{ $id }}"
                        placeholder="Search entries..." 
                        class="w-full px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <i class="fas fa-search absolute right-3 top-2.5 text-gray-400"></i>
                </div>
                <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition">
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
                    <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Entry ID</th>
                    <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Customer</th>
                    <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Type</th>
                    <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Period</th>
                    <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Debit</th>
                    <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Credit</th>
                    <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Date</th>
                    <th class="text-center py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                    <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="py-3 px-4 text-gray-900 dark:text-white font-mono text-sm">{!! $entry['id'] !!}</td>
                        <td class="py-3 px-4">{!! $entry['customer'] !!}</td>
                        <td class="py-3 px-4">{!! $entry['type'] !!}</td>
                        <td class="py-3 px-4 text-gray-900 dark:text-white">{!! $entry['period'] !!}</td>
                        <td class="text-right py-3 px-4 font-semibold text-orange-600 dark:text-orange-400">{!! $entry['debit'] !!}</td>
                        <td class="text-right py-3 px-4 font-semibold text-green-600 dark:text-green-400">{!! $entry['credit'] !!}</td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400 text-sm">{!! $entry['date'] !!}</td>
                        <td class="text-center py-3 px-4">
                            <a href="/ledger/show/{!! $entry['id'] !!}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2 block opacity-50"></i>
                            No ledger entries found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
        <span>Total Records: <strong>{{ count($entries) }}</strong></span>
        <div id="pagination-{{ $id }}" class="flex gap-2"></div>
    </div>
</div>

<style>
#{{ $id }} tbody tr {
    background-color: transparent;
}

#{{ $id }} tbody tr:hover {
    background-color: var(--gray-50);
}

@media (prefers-color-scheme: dark) {
    #{{ $id }} tbody tr:hover {
        background-color: rgba(107, 114, 128, 0.5);
    }
}
</style>
