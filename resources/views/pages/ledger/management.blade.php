<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <x-ui.page-header 
                title="Ledger Management" 
                subtitle="Track financial transactions, payments, and account balances"
            >
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('ledger.overall-data') }}" icon="fas fa-chart-bar">
                        Overall Data
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="switchLedgerTab('consumers')" id="tabLedgerConsumers" class="ledger-tab-button border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                            <i class="fas fa-users mr-2"></i>Consumer Ledgers
                        </button>
                        <button onclick="switchLedgerTab('transactions')" id="tabLedgerTransactions" class="ledger-tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                            <i class="fas fa-exchange-alt mr-2"></i>Transaction Types
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Search and Filters -->
            <div id="searchFilterSection" class="mb-6">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <x-ui.search-bar placeholder="Search by consumer name, ID, meter number..." />
                    </div>
                    <div class="sm:w-64">
                        <select id="sourceTypeFilterDropdown" onchange="filterBySourceType(this.value)" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Transaction Types</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div id="ledgerSummaryWrapper" class="mb-8">
                @include('components.ui.ledger.info-cards')
            </div>

            <!-- Main Table Section -->
            <div id="tableSection" x-data="ledgerManagementData()" x-init="init()">
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer Name</th>
                                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer ID</th>
                                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter No.</th>
                                    <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Total Charges</th>
                                    <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Total Payments</th>
                                    <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Outstanding Balance</th>
                                    <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="consumer in paginatedData" :key="consumer.id">
                                    <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white" x-text="consumer.name"></td>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-700 dark:text-gray-300" x-text="consumer.id"></td>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-700 dark:text-gray-300" x-text="consumer.meterNo"></td>
                                        <td class="px-4 py-3 text-sm text-right text-red-600" x-text="'₱ '+consumer.totalCharges.toFixed(2)"></td>
                                        <td class="px-4 py-3 text-sm text-right text-green-600" x-text="'₱ '+consumer.totalPayments.toFixed(2)"></td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold" :class="consumer.balance>0?'text-red-600':'text-green-600'" x-text="'₱ '+Math.abs(consumer.balance).toFixed(2)"></td>
                                        <td class="px-4 py-3 text-center"><button @click="selectConsumer(consumer.id)" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs">View Ledger</button></td>
                                    </tr>
                                </template>
                                <template x-if="paginatedData.length===0">
                                    <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400"><i class="fas fa-inbox text-3xl mb-2 opacity-50"></i><p>No consumers found</p></td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-4 flex-wrap gap-4">
                    <div class="flex items-center gap-2"><span class="text-sm text-gray-600 dark:text-gray-400">Show</span><select x-model.number="pageSize" @change="currentPage=1" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white"><option value="5">5</option><option value="10">10</option><option value="20">20</option><option value="50">50</option></select><span class="text-sm text-gray-600 dark:text-gray-400">entries</span></div>
                    <div class="flex items-center gap-2"><button @click="prevPage()" :disabled="currentPage===1" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed"><i class="fas fa-chevron-left mr-1"></i>Previous</button><div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span></div><button @click="nextPage()" :disabled="currentPage===totalPages" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">Next<i class="fas fa-chevron-right ml-1"></i></button></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Showing <span class="font-semibold text-gray-900 dark:text-white" x-text="startRecord"></span> to <span class="font-semibold text-gray-900 dark:text-white" x-text="endRecord"></span> of <span class="font-semibold text-gray-900 dark:text-white" x-text="totalRecords"></span> results</div>
                </div>
            </div>
<script>
function ledgerManagementData(){return{searchQuery:'',sourceTypeFilter:'all',pageSize:10,currentPage:1,data:[],init(){this.loadData();this.$watch('searchQuery',()=>this.currentPage=1);this.$watch('sourceTypeFilter',()=>this.currentPage=1);this.$watch('pageSize',()=>this.currentPage=1)},loadData(){if(!window._ledgerModule){setTimeout(()=>this.loadData(),100);return}const{consumers}=window._ledgerModule;this.data=consumers.map(c=>{const totalCharges=c.ledgerHistory.reduce((sum,e)=>sum+e.debit,0);const totalPayments=c.ledgerHistory.reduce((sum,e)=>sum+e.credit,0);const balance=totalCharges-totalPayments;return{...c,totalCharges,totalPayments,balance}})},get filteredData(){let filtered=this.data;if(this.searchQuery){filtered=filtered.filter(c=>c.name.toLowerCase().includes(this.searchQuery.toLowerCase())||c.id.toLowerCase().includes(this.searchQuery.toLowerCase())||c.meterNo.toLowerCase().includes(this.searchQuery.toLowerCase()))}if(this.sourceTypeFilter!=='all'){filtered=filtered.filter(c=>c.ledgerHistory.some(e=>e.sourceType===this.sourceTypeFilter))}return filtered},get totalRecords(){return this.filteredData.length},get totalPages(){return Math.ceil(this.totalRecords/this.pageSize)||1},get startRecord(){return this.totalRecords===0?0:((this.currentPage-1)*this.pageSize)+1},get endRecord(){return Math.min(this.currentPage*this.pageSize,this.totalRecords)},get paginatedData(){const start=(this.currentPage-1)*this.pageSize;return this.filteredData.slice(start,start+this.pageSize)},prevPage(){if(this.currentPage>1)this.currentPage--},nextPage(){if(this.currentPage<this.totalPages)this.currentPage++},selectConsumer(id){if(window.selectConsumer)window.selectConsumer(id)}}}
</script>

            <!-- Consumer Ledger Details Section -->
            <div id="ledgerDetailsSection" class="hidden">
                @include('pages.ledger.ledger-consumer-info')
            </div>

            <!-- Transaction Types Section -->
            <div id="ledgerTransactionsSection" class="hidden">
                @include('components.ui.ledger.transaction-types-content')
            </div>

        </div>
    </div>
</x-app-layout>

@vite(['resources/js/ledger.js'])

<script>
let currentLedgerTab = 'consumers';

function switchLedgerTab(tab) {
    currentLedgerTab = tab;
    
    document.querySelectorAll('.ledger-tab-button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });
    
    if(tab === 'consumers') {
        document.getElementById('tabLedgerConsumers').classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        document.getElementById('tabLedgerConsumers').classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        
        document.getElementById('searchFilterSection').classList.remove('hidden');
        document.getElementById('ledgerSummaryWrapper').classList.remove('hidden');
        document.getElementById('tableSection').classList.remove('hidden');
        document.getElementById('ledgerDetailsSection').classList.add('hidden');
        document.getElementById('ledgerTransactionsSection').classList.add('hidden');
    } else if(tab === 'transactions') {
        document.getElementById('tabLedgerTransactions').classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        document.getElementById('tabLedgerTransactions').classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        
        document.getElementById('searchFilterSection').classList.add('hidden');
        document.getElementById('ledgerSummaryWrapper').classList.add('hidden');
        document.getElementById('tableSection').classList.add('hidden');
        document.getElementById('ledgerDetailsSection').classList.add('hidden');
        document.getElementById('ledgerTransactionsSection').classList.remove('hidden');
    }
}

window.switchLedgerTab = switchLedgerTab;

window.viewOverallLedgerData = function() {
    switchLedgerTab('consumers');
    const wrapper = document.getElementById('ledgerSummaryWrapper');
    wrapper?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        if (typeof renderConsumerMainTable === 'function') {
            renderConsumerMainTable();
        }
    }, 100);
});
</script>
