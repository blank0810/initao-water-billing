@props([
    'headers' => [],
    'data' => [],
    'searchable' => true,
    'paginated' => true,
    'pageSize' => 10,
    'actions' => true
])

@php
$tableId = 'table-' . uniqid();
@endphp

<div x-data="tableData('{{ $tableId }}', {{ json_encode($data) }}, {{ $pageSize }})" x-init="init()">
    @if($searchable)
        <div class="flex justify-between items-center mb-4">
            <x-ui.search-bar 
                placeholder="Search records..."
                x-model="searchQuery"
            />
            {{ $slot ?? '' }}
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                        @foreach($headers as $header)
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                        @if($actions)
                            <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Actions
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-for="(item, index) in paginatedData" :key="item.id || index">
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                            @foreach($headers as $header)
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                    <span x-text="item.{{ $header['key'] }}"></span>
                                </td>
                            @endforeach
                            @if($actions)
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="flex justify-center gap-2">
                                        <x-ui.button variant="outline" size="sm" @click="viewItem(item)">
                                            <i class="fas fa-eye"></i>
                                        </x-ui.button>
                                        <x-ui.button variant="outline" size="sm" @click="editItem(item)">
                                            <i class="fas fa-edit"></i>
                                        </x-ui.button>
                                        <x-ui.button variant="danger" size="sm" @click="deleteItem(item)">
                                            <i class="fas fa-trash"></i>
                                        </x-ui.button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    </template>
                    <template x-if="paginatedData.length === 0">
                        <tr>
                            <td :colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                                <p>No records found</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    @if($paginated)
        <!-- Pagination Controls -->
        <div class="flex justify-between items-center mt-4 flex-wrap gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
                <select x-model.number="pageSize" @change="currentPage = 1" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
                <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
            </div>
            
            <div class="flex items-center gap-2">
                <button 
                    @click="prevPage()"
                    :disabled="currentPage === 1"
                    class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                >
                    <i class="fas fa-chevron-left mr-1"></i>Previous
                </button>
                
                <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
                    Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                </div>
                
                <button 
                    @click="nextPage()"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                >
                    Next<i class="fas fa-chevron-right ml-1"></i>
                </button>
            </div>
            
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span class="font-semibold text-gray-900 dark:text-white" x-text="startRecord"></span> to <span class="font-semibold text-gray-900 dark:text-white" x-text="endRecord"></span> of <span class="font-semibold text-gray-900 dark:text-white" x-text="totalRecords"></span> results
            </div>
        </div>
    @endif
</div>

<script>
function tableData(tableId, initialData, initialPageSize) {
    return {
        searchQuery: '',
        pageSize: 10,
        currentPage: 1,
        data: initialData || [],
        
        init() {
            this.pageSize = parseInt(initialPageSize) || 10;
            this.$watch('searchQuery', () => {
                this.currentPage = 1;
            });
            this.$watch('pageSize', () => {
                this.currentPage = 1;
            });
        },
        
        get filteredData() {
            if (!this.searchQuery) return this.data;
            return this.data.filter(item => 
                Object.values(item).some(value => 
                    String(value).toLowerCase().includes(this.searchQuery.toLowerCase())
                )
            );
        },
        
        get totalRecords() {
            return this.filteredData.length;
        },
        
        get totalPages() {
            return Math.ceil(this.totalRecords / this.pageSize) || 1;
        },
        
        get startRecord() {
            if (this.totalRecords === 0) return 0;
            return ((this.currentPage - 1) * this.pageSize) + 1;
        },
        
        get endRecord() {
            const end = this.currentPage * this.pageSize;
            return Math.min(end, this.totalRecords);
        },
        
        get paginatedData() {
            const start = (this.currentPage - 1) * this.pageSize;
            const end = start + this.pageSize;
            return this.filteredData.slice(start, end);
        },
        
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },
        
        viewItem(item) {
            console.log('View item:', item);
        },
        
        editItem(item) {
            console.log('Edit item:', item);
        },
        
        deleteItem(item) {
            if (confirm('Are you sure you want to delete this item?')) {
                this.data = this.data.filter(d => d.id !== item.id);
                if (this.currentPage > this.totalPages && this.totalPages > 0) {
                    this.currentPage = this.totalPages;
                }
            }
        }
    }
}
</script>