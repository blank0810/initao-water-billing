// Ledger Transaction Data - Phase 2
const ledgerTransactionData = [
    {
        id: 'LE-2024-0001',
        entry_id: 'LE-2024-0001',
        customer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">CUST-001</div>',
        type: '<span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">Bill</span>',
        amount: '<span class="font-bold text-red-600 dark:text-red-400">-₱739.20</span>',
        debit: '₱739.20',
        credit: '—',
        period: 'January 2024',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Posted</span>',
        date_posted: '2024-01-08',
        actions: '<a href="/ledger/show/LE-2024-0001" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-xs"><i class="fas fa-eye"></i></a>'
    },
    {
        id: 'LE-2024-0002',
        entry_id: 'LE-2024-0002',
        customer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">CUST-001</div>',
        type: '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">Payment</span>',
        amount: '<span class="font-bold text-green-600 dark:text-green-400">+₱739.20</span>',
        debit: '—',
        credit: '₱739.20',
        period: 'January 2024',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Posted</span>',
        date_posted: '2024-01-15',
        actions: '<a href="/ledger/show/LE-2024-0002" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-xs"><i class="fas fa-eye"></i></a>'
    },
    {
        id: 'LE-2024-0003',
        entry_id: 'LE-2024-0003',
        customer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Maria Santos</div><div class="text-xs text-gray-500">CUST-002</div>',
        type: '<span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">Bill</span>',
        amount: '<span class="font-bold text-red-600 dark:text-red-400">-₱535.80</span>',
        debit: '₱535.80',
        credit: '—',
        period: 'January 2024',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Posted</span>',
        date_posted: '2024-01-08',
        actions: '<a href="/ledger/show/LE-2024-0003" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-xs"><i class="fas fa-eye"></i></a>'
    },
    {
        id: 'LE-2024-0004',
        entry_id: 'LE-2024-0004',
        customer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Pedro Garcia</div><div class="text-xs text-gray-500">CUST-003</div>',
        type: '<span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">Bill</span>',
        amount: '<span class="font-bold text-red-600 dark:text-red-400">-₱965.60</span>',
        debit: '₱965.60',
        credit: '—',
        period: 'January 2024',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Posted</span>',
        date_posted: '2024-01-08',
        actions: '<a href="/ledger/show/LE-2024-0004" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-xs"><i class="fas fa-eye"></i></a>'
    },
    {
        id: 'LE-2024-0005',
        entry_id: 'LE-2024-0005',
        customer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Rosa Mendoza</div><div class="text-xs text-gray-500">CUST-004</div>',
        type: '<span class="px-2 py-1 text-xs font-semibold rounded bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300">Adjustment</span>',
        amount: '<span class="font-bold text-orange-600 dark:text-orange-400">-₱50.00</span>',
        debit: '₱50.00',
        credit: '—',
        period: 'January 2024',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Posted</span>',
        date_posted: '2024-01-10',
        actions: '<a href="/ledger/show/LE-2024-0005" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-xs"><i class="fas fa-eye"></i></a>'
    },
];

// Ledger Entry Details for Show Page
const ledgerEntryDetailsData = {
    id: 'LE-2024-0001',
    entry_id: 'LE-2024-0001',
    transaction_type: 'Bill',
    amount: '₱739.20',
    debit: '₱739.20',
    credit: '—',
    customer_name: 'Juan Dela Cruz',
    customer_id: 'CUST-001',
    account_no: 'ACC-2024-001',
    bill_id: 'B-2024-001',
    billing_period: 'January 2024',
    rate_applied: 'Residential Standard (RT-2024-001)',
    rate_details_used: ['RD-2024-001-T1', 'RD-2024-001-T2', 'RD-2024-001-T3'],
    date_posted: '2024-01-08',
    status: 'Posted (Immutable)',
    posted_by: 'System',
    coa_account: '1100-A (Accounts Receivable)',
    audit_trail: [
        { action: 'Created', date: '2024-01-08 09:00:00', user: 'system', notes: 'Bill posted from billing module' },
        { action: 'Posted', date: '2024-01-08 09:00:00', user: 'system', notes: 'Entry locked - immutable' }
    ]
};

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const tableInstance = window.tableInstances?.ledgerTransactionTable;
        if (tableInstance) {
            tableInstance.data = ledgerTransactionData;
        }
    }, 500);
});
