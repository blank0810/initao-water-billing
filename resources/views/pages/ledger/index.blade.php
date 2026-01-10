<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <x-ui.page-header 
                title="Ledger Management" 
                subtitle="Track financial transactions, payments, and account balances. Complete audit trail for COA compliance."
            >
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="#" icon="fas fa-chart-bar">
                        Overall Data
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Tab Navigation -->
            <x-ui.ledger.ledger-tabs :activeTab="'consumers'" />

            <!-- Tab Content: Consumer Ledgers -->
            <div id="content-consumers" class="ledger-tab-content">
                @include('pages.ledger.tabs.consumer-ledgers')
            </div>

            <!-- Tab Content: Transaction Types -->
            <div id="content-transactions" class="ledger-tab-content hidden">
                @include('pages.ledger.tabs.transaction-types')
            </div>

            <!-- Tab Content: Ledger History -->
            <div id="content-history" class="ledger-tab-content hidden">
                @include('pages.ledger.tabs.ledger-history')
            </div>
        </div>
    </div>

    @vite([
        'resources/js/utils/action-functions.js',
        'resources/js/data/ledger/ledger-data.js'
    ])

    <script>
    function switchLedgerTab(tab) {
        // Hide all tab contents
        document.querySelectorAll('.ledger-tab-content').forEach(el => el.classList.add('hidden'));
        
        // Remove active classes from all tabs
        document.querySelectorAll('.ledger-tab').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });
        
        // Show selected tab content
        document.getElementById('content-' + tab).classList.remove('hidden');
        
        // Add active classes to selected tab
        const activeTab = document.getElementById('tab-' + tab);
        activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        activeTab.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
    }

    window.switchLedgerTab = switchLedgerTab;

    // Initialize first tab on load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const tableIds = ['consumerLedgersTable', 'transactionTypesTable', 'ledgerHistoryTable'];
            tableIds.forEach(tableId => {
                if (document.getElementById(tableId)) {
                    new ActionFunctionsManager(tableId);
                }
            });
        }, 100);
        
        switchLedgerTab('consumers');
    });
    </script>
</x-app-layout>
