// Customer Ledger Data
const customerLedgerData = [
    { 
        id: 1,
        date: '2024-01-01', 
        reference: '<span class="font-mono text-gray-900 dark:text-white">OPENING</span>', 
        description: 'Beginning Balance', 
        debit: '<span class="text-gray-500">-</span>', 
        credit: '<span class="text-gray-500">-</span>', 
        balance: '<span class="font-semibold text-gray-900 dark:text-white">₱0.00</span>' 
    },
    { 
        id: 2,
        date: '2024-01-05', 
        reference: '<span class="font-mono text-gray-900 dark:text-white">BILL-2024-001</span>', 
        description: 'Water Bill - January 2024 (25 m³)', 
        debit: '<span class="font-semibold text-red-600 dark:text-red-400">₱739.20</span>', 
        credit: '<span class="text-gray-500">-</span>', 
        balance: '<span class="font-semibold text-gray-900 dark:text-white">₱739.20</span>' 
    },
    { 
        id: 3,
        date: '2024-01-15', 
        reference: '<span class="font-mono text-gray-900 dark:text-white">PAY-2024-001</span>', 
        description: 'Payment Received - Cash', 
        debit: '<span class="text-gray-500">-</span>', 
        credit: '<span class="font-semibold text-green-600 dark:text-green-400">₱739.20</span>', 
        balance: '<span class="font-semibold text-gray-900 dark:text-white">₱0.00</span>' 
    },
    { 
        id: 4,
        date: '2023-12-05', 
        reference: '<span class="font-mono text-gray-900 dark:text-white">BILL-2023-012</span>', 
        description: 'Water Bill - December 2023 (22 m³)', 
        debit: '<span class="font-semibold text-red-600 dark:text-red-400">₱654.80</span>', 
        credit: '<span class="text-gray-500">-</span>', 
        balance: '<span class="font-semibold text-gray-900 dark:text-white">₱654.80</span>' 
    },
    { 
        id: 5,
        date: '2023-12-20', 
        reference: '<span class="font-mono text-gray-900 dark:text-white">PAY-2023-012</span>', 
        description: 'Payment Received - GCash', 
        debit: '<span class="text-gray-500">-</span>', 
        credit: '<span class="font-semibold text-green-600 dark:text-green-400">₱654.80</span>', 
        balance: '<span class="font-semibold text-gray-900 dark:text-white">₱0.00</span>' 
    },
];

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const tableInstance = window.tableInstances?.customerLedgerTable;
        if (tableInstance) {
            tableInstance.data = customerLedgerData;
        }
    }, 500);
});
