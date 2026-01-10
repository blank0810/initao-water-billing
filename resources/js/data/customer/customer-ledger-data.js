// Customer-Specific Ledger Data
// Each customer has their own transaction history

const customerLedgerDataMap = {
    '1': [ // Juan Dela Cruz (CUST-001)
        {
            date: '2024-01-05',
            description: 'Bill Generated - January 2024',
            reference: 'BILL-2024-001',
            debit: '<span class="text-red-600 font-semibold">₱739.20</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱739.20</span>'
        },
        {
            date: '2024-01-15',
            description: 'Payment Received - Over the Counter',
            reference: 'PAY-2024-001',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱739.20</span>',
            balance: '<span class="font-semibold">₱0.00</span>'
        },
        {
            date: '2023-12-05',
            description: 'Bill Generated - December 2023',
            reference: 'BILL-2023-012',
            debit: '<span class="text-red-600 font-semibold">₱654.80</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱654.80</span>'
        },
        {
            date: '2023-12-20',
            description: 'Payment Received - GCash',
            reference: 'PAY-2023-012',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱654.80</span>',
            balance: '<span class="font-semibold">₱0.00</span>'
        }
    ],
    '2': [ // Maria Santos (CUST-002)
        {
            date: '2024-01-05',
            description: 'Bill Generated - January 2024',
            reference: 'BILL-2024-002',
            debit: '<span class="text-red-600 font-semibold">₱535.80</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱535.80</span>'
        },
        {
            date: '2024-01-10',
            description: 'Adjustment - Meter Reading Error Correction',
            reference: 'ADJ-2024-001',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱50.00</span>',
            balance: '<span class="font-semibold">₱485.80</span>'
        },
        {
            date: '2024-01-18',
            description: 'Payment Received - Bank Transfer',
            reference: 'PAY-2024-002',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱485.80</span>',
            balance: '<span class="font-semibold">₱0.00</span>'
        },
        {
            date: '2023-12-05',
            description: 'Bill Generated - December 2023',
            reference: 'BILL-2023-011',
            debit: '<span class="text-red-600 font-semibold">₱512.50</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱512.50</span>'
        },
        {
            date: '2023-12-12',
            description: 'Payment Received - Over the Counter',
            reference: 'PAY-2023-011',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱512.50</span>',
            balance: '<span class="font-semibold">₱0.00</span>'
        }
    ],
    '3': [ // Pedro Garcia (CUST-003)
        {
            date: '2024-01-05',
            description: 'Bill Generated - January 2024',
            reference: 'BILL-2024-003',
            debit: '<span class="text-red-600 font-semibold">₱965.60</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱965.60</span>'
        },
        {
            date: '2024-01-12',
            description: 'Late Payment Charge',
            reference: 'CHG-2024-001',
            debit: '<span class="text-red-600 font-semibold">₱48.28</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱1,013.88</span>'
        },
        {
            date: '2024-01-25',
            description: 'Partial Payment Received - Check',
            reference: 'PAY-2024-003A',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱500.00</span>',
            balance: '<span class="font-semibold">₱513.88</span>'
        },
        {
            date: '2023-12-05',
            description: 'Bill Generated - December 2023',
            reference: 'BILL-2023-010',
            debit: '<span class="text-red-600 font-semibold">₱890.00</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱1,403.88</span>'
        }
    ],
    '4': [ // Rosa Mendoza (CUST-004)
        {
            date: '2024-01-05',
            description: 'Bill Generated - January 2024',
            reference: 'BILL-2024-004',
            debit: '<span class="text-red-600 font-semibold">₱445.50</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱445.50</span>'
        },
        {
            date: '2024-01-08',
            description: 'Adjustment - Senior Citizen Discount',
            reference: 'ADJ-2024-002',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱22.28</span>',
            balance: '<span class="font-semibold">₱423.22</span>'
        },
        {
            date: '2024-01-20',
            description: 'Payment Received - GCash',
            reference: 'PAY-2024-004',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱423.22</span>',
            balance: '<span class="font-semibold">₱0.00</span>'
        },
        {
            date: '2023-12-05',
            description: 'Bill Generated - December 2023',
            reference: 'BILL-2023-009',
            debit: '<span class="text-red-600 font-semibold">₱420.00</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱420.00</span>'
        },
        {
            date: '2023-12-22',
            description: 'Payment Received - Over the Counter',
            reference: 'PAY-2023-009',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱420.00</span>',
            balance: '<span class="font-semibold">₱0.00</span>'
        }
    ],
    '5': [ // Carlos Lopez (CUST-005)
        {
            date: '2024-01-05',
            description: 'Bill Generated - January 2024',
            reference: 'BILL-2024-005',
            debit: '<span class="text-red-600 font-semibold">₱1,285.40</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱1,285.40</span>'
        },
        {
            date: '2024-01-12',
            description: 'Payment Received - Bank Transfer',
            reference: 'PAY-2024-005A',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱800.00</span>',
            balance: '<span class="font-semibold">₱485.40</span>'
        },
        {
            date: '2024-01-20',
            description: 'Payment Received - Check',
            reference: 'PAY-2024-005B',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱485.40</span>',
            balance: '<span class="font-semibold">₱0.00</span>'
        },
        {
            date: '2023-12-05',
            description: 'Bill Generated - December 2023',
            reference: 'BILL-2023-008',
            debit: '<span class="text-red-600 font-semibold">₱1,150.00</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱1,150.00</span>'
        },
        {
            date: '2023-12-15',
            description: 'Adjustment - Commercial Rate Correction',
            reference: 'ADJ-2023-001',
            debit: '<span class="text-red-600 font-semibold">₱75.50</span>',
            credit: '-',
            balance: '<span class="font-semibold">₱1,225.50</span>'
        },
        {
            date: '2023-12-25',
            description: 'Payment Received - Bank Transfer',
            reference: 'PAY-2023-008',
            debit: '-',
            credit: '<span class="text-green-600 font-semibold">₱1,225.50</span>',
            balance: '<span class="font-semibold">₱0.00</span>'
        }
    ]
};

// Function to get customer ledger by customer ID
function getCustomerLedger(customerId) {
    return customerLedgerDataMap[customerId] || [];
}

// Function to load ledger for a customer (by name from profile data)
function loadCustomerLedgerByName(customerName) {
    // Find customer ID from profile data
    if (window.customerProfileData) {
        const customer = window.customerProfileData.find(c => c.name === customerName);
        if (customer) {
            return getCustomerLedger(customer.id.toString());
        }
    }
    return [];
}
