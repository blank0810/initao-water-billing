// Payment Transaction Data - Phase 1 Extended
const paymentTransactionData = [
    {
        id: 'PT-2024-0001',
        transaction_id: 'PT-2024-0001',
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">ACC-2024-001</div>',
        amount: '<span class="font-bold text-green-600 dark:text-green-400">₱739.20</span>',
        bill_id: 'B-2024-001',
        payment_date: '2024-01-15',
        payment_method: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">Bank Transfer</span>',
        reference: 'BTR-20240115-001',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Completed</span>',
        actions: '<a href="/payment/show/PT-2024-0001" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'PT-2024-0002',
        transaction_id: 'PT-2024-0002',
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Maria Santos</div><div class="text-xs text-gray-500">ACC-2024-002</div>',
        amount: '<span class="font-bold text-green-600 dark:text-green-400">₱520.50</span>',
        bill_id: 'B-2024-002',
        payment_date: '2024-01-16',
        payment_method: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Over-the-Counter</span>',
        reference: 'OTC-20240116-001',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Completed</span>',
        actions: '<a href="/payment/show/PT-2024-0002" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'PT-2024-0003',
        transaction_id: 'PT-2024-0003',
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Angel Construction Inc.</div><div class="text-xs text-gray-500">ACC-2024-003</div>',
        amount: '<span class="font-bold text-green-600 dark:text-green-400">₱1,250.00</span>',
        bill_id: 'B-2024-003',
        payment_date: '2024-01-17',
        payment_method: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">Check</span>',
        reference: 'CHK-20240117-001',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Completed</span>',
        actions: '<a href="/payment/show/PT-2024-0003" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'PT-2024-0004',
        transaction_id: 'PT-2024-0004',
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">Pedro Reyes</div><div class="text-xs text-gray-500">ACC-2024-004</div>',
        amount: '<span class="font-bold text-green-600 dark:text-green-400">₱680.75</span>',
        bill_id: 'B-2024-004',
        payment_date: '2024-01-18',
        payment_method: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">Bank Transfer</span>',
        reference: 'BTR-20240118-002',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">Pending</span>',
        actions: '<a href="/payment/show/PT-2024-0004" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'PT-2024-0005',
        transaction_id: 'PT-2024-0005',
        consumer: '<div class="text-sm font-medium text-gray-900 dark:text-white">City Hospital</div><div class="text-xs text-gray-500">ACC-2024-005</div>',
        amount: '<span class="font-bold text-green-600 dark:text-green-400">₱2,150.00</span>',
        bill_id: 'B-2024-005',
        payment_date: '2024-01-19',
        payment_method: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">Check</span>',
        reference: 'CHK-20240119-002',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Completed</span>',
        actions: '<a href="/payment/show/PT-2024-0005" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
];

// Payment Transaction Details (Show Page)
const paymentTransactionDetailsData = {
    id: 'PT-2024-0001',
    transaction_id: 'PT-2024-0001',
    bill_id: 'B-2024-001',
    consumer: {
        id: 1,
        name: 'Juan Dela Cruz',
        account_no: 'ACC-2024-001',
        category: 'Residential',
        address: '123 Main Street, Barangay San Jose, City'
    },
    billing_info: {
        billing_period: 'January 2024',
        consumption: '25 m³',
        bill_amount: '₱739.20',
        bill_date: '2024-01-08'
    },
    payment_info: {
        payment_date: '2024-01-15',
        payment_method: 'Bank Transfer',
        payment_reference: 'BTR-20240115-001',
        amount_paid: '₱739.20',
        status: 'Completed'
    },
    ledger_entry: {
        entry_id: 'LE-2024-0001',
        type: 'Payment',
        debit: '—',
        credit: '₱739.20',
        date_posted: '2024-01-15'
    },
    breakdown: {
        base_charges: '₱50.00',
        consumption_charges: '₱680.00',
        penalties: '₱0.00',
        adjustments: '₱9.20',
        total: '₱739.20'
    }
};

// Initialize table on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Payment transaction data loaded');
});
