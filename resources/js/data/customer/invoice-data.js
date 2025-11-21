/**
 * Invoice Data for Customer Module
 */

export const invoiceData = [
    { 
        invoice_id: 'INV-2024-1001', 
        customer_code: 'CUST-2024-003', 
        customer_name: 'Pedro Garcia', 
        invoice_date: '2024-01-16', 
        due_date: '2024-01-26',
        total_amount: 3500, 
        paid_amount: 0, 
        payment_status: 'PENDING',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1002', 
        customer_code: 'CUST-2024-004', 
        customer_name: 'Ana Rodriguez', 
        invoice_date: '2024-01-15', 
        due_date: '2024-01-25',
        total_amount: 3500, 
        paid_amount: 0, 
        payment_status: 'PENDING',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1003', 
        customer_code: 'CUST-2024-005', 
        customer_name: 'Carlos Lopez', 
        invoice_date: '2024-01-14', 
        due_date: '2024-01-24',
        total_amount: 3500, 
        paid_amount: 0, 
        payment_status: 'PENDING',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1004', 
        customer_code: 'CUST-2024-006', 
        customer_name: 'Lisa Chen', 
        invoice_date: '2024-01-13', 
        due_date: '2024-01-23',
        total_amount: 3800, 
        paid_amount: 0, 
        payment_status: 'PENDING',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200},
            {description: 'Inspection Fee', amount: 300}
        ]
    },
    { 
        invoice_id: 'INV-2024-1005', 
        customer_code: 'CUST-2024-007', 
        customer_name: 'Mark Johnson', 
        invoice_date: '2024-01-12', 
        due_date: '2024-01-22',
        total_amount: 3500, 
        paid_amount: 3500, 
        payment_status: 'PAID',
        paid_date: '2024-01-20',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1006', 
        customer_code: 'CUST-2024-008', 
        customer_name: 'Sofia Martinez', 
        invoice_date: '2024-01-11', 
        due_date: '2024-01-21',
        total_amount: 3500, 
        paid_amount: 3500, 
        payment_status: 'PAID',
        paid_date: '2024-01-19',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1007', 
        customer_code: 'CUST-2024-009', 
        customer_name: 'David Wilson', 
        invoice_date: '2024-01-10', 
        due_date: '2024-01-20',
        total_amount: 3500, 
        paid_amount: 3500, 
        payment_status: 'PAID',
        paid_date: '2024-01-18',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    },
    { 
        invoice_id: 'INV-2024-1008', 
        customer_code: 'CUST-2024-010', 
        customer_name: 'Emma Brown', 
        invoice_date: '2024-01-09', 
        due_date: '2024-01-19',
        total_amount: 3500, 
        paid_amount: 3500, 
        payment_status: 'PAID',
        paid_date: '2024-01-17',
        items: [
            {description: 'Connection Fee', amount: 1500},
            {description: 'Service Deposit', amount: 1000},
            {description: 'Meter Installation', amount: 800},
            {description: 'Processing Fee', amount: 200}
        ]
    }
];

if (typeof window !== 'undefined') {
    window.invoiceData = invoiceData;
}
