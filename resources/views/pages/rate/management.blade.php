<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <x-ui.page-header 
                title="Rate Management" 
                subtitle="Manage water rates, billing classes, and pricing structures"
            >
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('rate.overall-data') }}" icon="fas fa-chart-bar">
                        Overall Data
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="switchTab('consumers')" id="tabConsumers" class="tab-button border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                            <i class="fas fa-users mr-2"></i>Consumers
                        </button>
                        <button onclick="switchTab('structures')" id="tabStructures" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                            <i class="fas fa-layer-group mr-2"></i>Rate Structures
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Search and Filters -->
            <div id="searchFilterSection" class="mb-6">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <x-ui.search-bar placeholder="Search by name, consumer ID, meter number, account type..." />
                    </div>
                    <div class="sm:w-64">
                        <select id="accountTypeFilterDropdown" onchange="filterByAccountType(this.value)" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Account Types</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div id="rateSummaryWrapper" class="mb-8">
                @include('components.ui.rate.info-cards')
            </div>

            <div id="tableSection" x-data="rateManagementData()" x-init="init()">
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer ID</th>
                                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter No.</th>
                                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Billing Period</th>
                                    <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount Due</th>
                                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Rate Class</th>
                                    <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="consumer in paginatedData" :key="consumer.id">
                                    <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white" x-text="consumer.name"></td>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-700 dark:text-gray-300" x-text="consumer.id"></td>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-700 dark:text-gray-300" x-text="consumer.meterNo"></td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300" x-text="consumer.billingPeriod"></td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold" :class="consumer.status === 'Overdue' ? 'text-red-600' : 'text-green-600'" x-text="'₱ ' + consumer.amountDue.toFixed(2)"></td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300" x-text="consumer.accountTypeName"></td>
                                        <td class="px-4 py-3 text-center"><button @click="selectConsumer(consumer.id)" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">View Details</button></td>
                                    </tr>
                                </template>
                                <template x-if="paginatedData.length === 0">
                                    <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400"><i class="fas fa-inbox text-3xl mb-2 opacity-50"></i><p>No consumers found</p></td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-4 flex-wrap gap-4">
                    <div class="flex items-center gap-2"><span class="text-sm text-gray-600 dark:text-gray-400">Show</span><select x-model.number="pageSize" @change="currentPage = 1" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white"><option value="5">5</option><option value="10">10</option><option value="20">20</option><option value="50">50</option></select><span class="text-sm text-gray-600 dark:text-gray-400">entries</span></div>
                    <div class="flex items-center gap-2"><button @click="prevPage()" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed"><i class="fas fa-chevron-left mr-1"></i>Previous</button><div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span></div><button @click="nextPage()" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">Next<i class="fas fa-chevron-right ml-1"></i></button></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Showing <span class="font-semibold text-gray-900 dark:text-white" x-text="startRecord"></span> to <span class="font-semibold text-gray-900 dark:text-white" x-text="endRecord"></span> of <span class="font-semibold text-gray-900 dark:text-white" x-text="totalRecords"></span> results</div>
                </div>
            </div>
<script>
function rateManagementData(){return{searchQuery:'',accountTypeFilter:'all',pageSize:10,currentPage:1,data:[],init(){this.loadData();this.$watch('searchQuery',()=>this.currentPage=1);this.$watch('accountTypeFilter',()=>this.currentPage=1);this.$watch('pageSize',()=>this.currentPage=1)},loadData(){if(!window._rateModule){setTimeout(()=>this.loadData(),100);return}const{consumers,accountTypes}=window._rateModule;this.data=consumers.map(c=>({...c,accountTypeName:accountTypes.find(at=>at.id===c.accountTypeId)?.name||'-'}))},get filteredData(){let filtered=this.data;if(this.searchQuery){filtered=filtered.filter(c=>c.name.toLowerCase().includes(this.searchQuery.toLowerCase())||c.id.toLowerCase().includes(this.searchQuery.toLowerCase())||c.meterNo.toLowerCase().includes(this.searchQuery.toLowerCase())||c.accountTypeName.toLowerCase().includes(this.searchQuery.toLowerCase()))}if(this.accountTypeFilter!=='all'){filtered=filtered.filter(c=>c.accountTypeId===parseInt(this.accountTypeFilter))}return filtered},get totalRecords(){return this.filteredData.length},get totalPages(){return Math.ceil(this.totalRecords/this.pageSize)||1},get startRecord(){return this.totalRecords===0?0:((this.currentPage-1)*this.pageSize)+1},get endRecord(){return Math.min(this.currentPage*this.pageSize,this.totalRecords)},get paginatedData(){const start=(this.currentPage-1)*this.pageSize;return this.filteredData.slice(start,start+this.pageSize)},prevPage(){if(this.currentPage>1)this.currentPage--},nextPage(){if(this.currentPage<this.totalPages)this.currentPage++},selectConsumer(id){if(window.selectConsumer)window.selectConsumer(id)}}}
</script>

            <!-- Consumer Details Section -->
            <div id="rateDetailsSection" class="hidden">
                @include('pages.rate.rate-consumer-info')
            </div>

            <!-- Rate Structures Section -->
            <div id="rateStructuresSection" class="hidden">
                @include('components.ui.rate.rate-structure-content')
            </div>

        </div>
    </div>
</x-app-layout>

@vite(['resources/js/rate.js'])

<script>
let currentTab = 'consumers';

function switchTab(tab) {
    currentTab = tab;
    
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });
    
    if(tab === 'consumers') {
        document.getElementById('tabConsumers').classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        document.getElementById('tabConsumers').classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        
        document.getElementById('searchFilterSection').classList.remove('hidden');
        document.getElementById('rateSummaryWrapper').classList.remove('hidden');
        document.getElementById('tableSection').classList.remove('hidden');
        document.getElementById('rateDetailsSection').classList.add('hidden');
        document.getElementById('rateStructuresSection').classList.add('hidden');
    } else if(tab === 'structures') {
        document.getElementById('tabStructures').classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        document.getElementById('tabStructures').classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        
        document.getElementById('searchFilterSection').classList.add('hidden');
        document.getElementById('rateSummaryWrapper').classList.add('hidden');
        document.getElementById('tableSection').classList.add('hidden');
        document.getElementById('rateDetailsSection').classList.add('hidden');
        document.getElementById('rateStructuresSection').classList.remove('hidden');
    }
}

function showRateTable() {
    document.getElementById('tableSection').classList.remove('hidden');
    document.getElementById('rateDetailsSection').classList.add('hidden');
    document.getElementById('rateSummaryWrapper').classList.remove('hidden');
    document.getElementById('searchFilterSection').classList.remove('hidden');
    if (typeof renderConsumerMainTable === 'function') renderConsumerMainTable();
}

window.showRateTable = showRateTable;
window.switchTab = switchTab;

function viewOverallRateData() {
    switchTab('consumers');
    const wrapper = document.getElementById('rateSummaryWrapper');
    wrapper?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

window.viewOverallRateData = viewOverallRateData;

document.addEventListener('DOMContentLoaded', function() {
    if (typeof renderConsumerMainTable === 'function') {
        renderConsumerMainTable();
    }
});
</script>
