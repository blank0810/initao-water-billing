<!-- Transaction Types Tab -->
<div>
    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-blue-800 dark:text-blue-300">
                <strong>Transaction Types:</strong> All financial transactions are categorized by type. Each type has specific validation rules and audit requirements.
            </div>
        </div>
    </div>

    <x-ui.action-functions 
        searchPlaceholder="Search by transaction type..."
        filterLabel="All Categories"
        :filterOptions="[
            ['value' => 'Revenue', 'label' => 'Revenue'],
            ['value' => 'Adjustment', 'label' => 'Adjustment'],
            ['value' => 'Penalty', 'label' => 'Penalty']
        ]"
        :showDateFilter="false"
        :showExport="true"
        tableId="transactionTypesTable"
    />

    @php
        $transactionTypeHeaders = [
            ['key' => 'type_code', 'label' => 'Type Code', 'html' => false],
            ['key' => 'type_name', 'label' => 'Type Name', 'html' => false],
            ['key' => 'category', 'label' => 'Category', 'html' => true],
            ['key' => 'description', 'label' => 'Description', 'html' => false],
            ['key' => 'account_code', 'label' => 'Account Code (COA)', 'html' => false],
            ['key' => 'is_active', 'label' => 'Active', 'html' => true],
            ['key' => 'actions', 'label' => 'Actions', 'html' => true],
        ];
    @endphp

    <x-table
        id="transactionTypesTable"
        :headers="$transactionTypeHeaders"
        :data="[]"
        :searchable="false"
        :paginated="true"
        :pageSize="15"
        :actions="false"
    />
</div>
