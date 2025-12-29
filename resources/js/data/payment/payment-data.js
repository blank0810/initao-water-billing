
export const CUSTOMER_STATUSES = Object.freeze({
    APPLICANT: 'Applicant',
    ACTIVE_CONNECTED: 'Active / Connected',
    DISCONNECTED: 'Disconnected',
    RECONNECTION_PENDING: 'Reconnection Pending',
    SUSPENDED: 'Suspended',
    DELINQUENT: 'Delinquent',
    OVERDUE: 'Overdue',
    CLOSED_TERMINATED: 'Closed / Terminated'
});

export const CUSTOMER_STATUS_ALIASES = Object.freeze({
    Applicant: CUSTOMER_STATUSES.APPLICANT,
    'Active Consumer': CUSTOMER_STATUSES.ACTIVE_CONNECTED,
    Active: CUSTOMER_STATUSES.ACTIVE_CONNECTED,
    Connected: CUSTOMER_STATUSES.ACTIVE_CONNECTED,
    'Active / Connected': CUSTOMER_STATUSES.ACTIVE_CONNECTED,
    'With Outstanding Balance': CUSTOMER_STATUSES.DELINQUENT,
    Delinquent: CUSTOMER_STATUSES.DELINQUENT,
    Overdue: CUSTOMER_STATUSES.OVERDUE,
    Disconnected: CUSTOMER_STATUSES.DISCONNECTED,
    'Reconnection Pending': CUSTOMER_STATUSES.RECONNECTION_PENDING,
    Suspended: CUSTOMER_STATUSES.SUSPENDED,
    'Closed / Terminated': CUSTOMER_STATUSES.CLOSED_TERMINATED,
    Closed: CUSTOMER_STATUSES.CLOSED_TERMINATED,
    Terminated: CUSTOMER_STATUSES.CLOSED_TERMINATED
});

export function normalizeCustomerStatus(status) {
    if (!status) return CUSTOMER_STATUSES.APPLICANT;
    return CUSTOMER_STATUS_ALIASES[String(status).trim()] || String(status).trim();
}

export const CUSTOMER_STATUS_BADGE_CLASSES = Object.freeze({
    [CUSTOMER_STATUSES.APPLICANT]: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    [CUSTOMER_STATUSES.ACTIVE_CONNECTED]: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    [CUSTOMER_STATUSES.DISCONNECTED]: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    [CUSTOMER_STATUSES.RECONNECTION_PENDING]: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    [CUSTOMER_STATUSES.SUSPENDED]: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    [CUSTOMER_STATUSES.DELINQUENT]: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    [CUSTOMER_STATUSES.OVERDUE]: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    [CUSTOMER_STATUSES.CLOSED_TERMINATED]: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
});

export function getCustomerStatusBadgeClass(status) {
    const normalized = normalizeCustomerStatus(status);
    return CUSTOMER_STATUS_BADGE_CLASSES[normalized] || CUSTOMER_STATUS_BADGE_CLASSES[CUSTOMER_STATUSES.APPLICANT];
}

export const PAYMENT_METHODS = Object.freeze([
    { value: 'cash', label: 'Cash' },
    { value: 'credit', label: 'Credit Card' },
    { value: 'bank', label: 'Bank Transfer' },
    { value: 'gcash', label: 'GCash' },
    { value: 'maya', label: 'Maya' }
]);

export const PAYMENT_STATUS_RULES = Object.freeze({
    [CUSTOMER_STATUSES.APPLICANT]: {
        allowNewPayments: true,
        allowedPaymentMethods: PAYMENT_METHODS.map(m => m.value),
        purposes: [
            { value: 'application_processing', label: 'Application Processing', description: 'Application-related charges', amount: 100 },
            { value: 'installation_inspection', label: 'Installation & Inspection', description: 'Installation and inspection charges', amount: 300 }
        ]
    },
    [CUSTOMER_STATUSES.ACTIVE_CONNECTED]: {
        allowNewPayments: true,
        allowedPaymentMethods: PAYMENT_METHODS.map(m => m.value),
        purposes: [
            { value: 'current_bill', label: 'Current Billing', description: 'Regular water consumption billing', amount: 420.5 },
            { value: 'full_settlement', label: 'Full Settlement', description: 'Settle current and past due balances', amount: 450.5 }
        ]
    },
    [CUSTOMER_STATUSES.DISCONNECTED]: {
        allowNewPayments: true,
        allowedPaymentMethods: PAYMENT_METHODS.map(m => m.value),
        purposes: [
            { value: 'balance_settlement', label: 'Outstanding Balance', description: 'Settle unpaid balances and fees', amount: 2500 }
        ]
    },
    [CUSTOMER_STATUSES.RECONNECTION_PENDING]: {
        allowNewPayments: true,
        allowedPaymentMethods: ['cash', 'bank'],
        purposes: [
            { value: 'reconnection_settlement', label: 'Reconnection Settlement', description: 'Reconnection fees and past due balances', amount: 3500 }
        ]
    },
    [CUSTOMER_STATUSES.SUSPENDED]: {
        allowNewPayments: true,
        allowedPaymentMethods: ['cash', 'bank'],
        purposes: [
            { value: 'eligible_settlement', label: 'Eligible Settlement', description: 'Only eligible payments are allowed', amount: 900 }
        ]
    },
    [CUSTOMER_STATUSES.DELINQUENT]: {
        allowNewPayments: true,
        allowedPaymentMethods: PAYMENT_METHODS.map(m => m.value),
        purposes: [
            { value: 'overdue_settlement', label: 'Overdue Settlement', description: 'Overdue bills with penalties/interest', amount: 1250.75 },
            { value: 'partial_settlement', label: 'Partial Settlement', description: 'Pay a portion toward arrears', amount: 500 }
        ]
    },
    [CUSTOMER_STATUSES.CLOSED_TERMINATED]: {
        allowNewPayments: false,
        allowedPaymentMethods: [],
        purposes: []
    }
});

export function getPaymentStatusRule(status) {
    const normalized = normalizeCustomerStatus(status);
    return PAYMENT_STATUS_RULES[normalized] || PAYMENT_STATUS_RULES[CUSTOMER_STATUSES.APPLICANT];
}

export function buildPaymentBreakdown(customer) {
    const normalizedStatus = normalizeCustomerStatus(customer?.status);
    if (Array.isArray(customer?.breakdown) && customer.breakdown.length > 0) {
        return customer.breakdown;
    }

    const amountDue = Number(customer?.amount_due || 0);

    if (normalizedStatus === CUSTOMER_STATUSES.APPLICANT) {
        return [
            { group: 'Application Fees', code: 'registration_fee', label: 'Registration Fee', amount: 50.0 },
            { group: 'Installation Fees', code: 'installation_fee', label: 'Installation Fee', amount: 200.0 },
            { group: 'Inspection & Other Charges', code: 'tapping_fee', label: 'Tapping Fee', amount: 50.0 },
            { group: 'Inspection & Other Charges', code: 'excavation_fee', label: 'Excavation Fee', amount: 50.0 }
        ];
    }

    if (normalizedStatus === CUSTOMER_STATUSES.CLOSED_TERMINATED) {
        return [];
    }

    return [{ group: 'Summary', code: 'amount_due', label: 'Amount Due', amount: amountDue }];
}

export function sumBreakdownAmount(breakdown) {
    return (breakdown || []).reduce((sum, item) => sum + (Number(item.amount) || 0), 0);
}

export function formatCurrency(amount) {
    const n = Number(amount) || 0;
    return '₱ ' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

export const paymentApplications = [
    {
        application_id: 'APP-2024-001',
        customer_name: 'Juan Dela Cruz',
        customer_code: 'CUST-2024-001',
        address: 'Purok 1, Poblacion',
        area: 'Poblacion',
        meter_reader: 'Pedro Penduko',
        processed_at: '2024-01-20T08:30:00',
        amount_due: 350.0,
        status: CUSTOMER_STATUSES.APPLICANT,
        payment_type: 'APPLICATION_FEE',
        account_no: null,
        meter_no: null,
        breakdown: buildPaymentBreakdown({ status: CUSTOMER_STATUSES.APPLICANT })
    },
    {
        application_id: 'APP-2024-002',
        customer_name: 'Maria Santos',
        customer_code: 'CUST-2024-002',
        address: 'Purok 2, Central',
        area: 'Central',
        meter_reader: 'Juan Tamad',
        processed_at: '2024-01-21T09:15:00',
        amount_due: 450.5,
        status: CUSTOMER_STATUSES.ACTIVE_CONNECTED,
        payment_type: 'BILLING',
        account_no: 'ACC-2024-5102',
        meter_no: 'MTR-1002',
        breakdown: [
            { group: 'Consumption Billing', code: 'water_bill', label: 'Water Bill', amount: 420.5 },
            { group: 'Penalties & Adjustments', code: 'penalty', label: 'Penalty', amount: 30.0 }
        ]
    },
    {
        application_id: 'APP-2024-003',
        customer_name: 'Pedro Garcia',
        customer_code: 'CUST-2024-003',
        address: 'Purok 3, San Jose',
        area: 'San Jose',
        meter_reader: 'Nardong Putik',
        processed_at: '2024-01-21T10:45:00',
        amount_due: 1250.75,
        status: CUSTOMER_STATUSES.DELINQUENT,
        payment_type: 'BILLING_ARREARS',
        account_no: 'ACC-2024-5103',
        meter_no: 'MTR-1003',
        breakdown: [
            { group: 'Past Due Balances', code: 'arrears', label: 'Arrears', amount: 1050.75 },
            { group: 'Penalties & Interest', code: 'interest', label: 'Interest / Penalty', amount: 200.0 }
        ]
    },
    {
        application_id: 'APP-2024-004',
        customer_name: 'Ana Rodriguez',
        customer_code: 'CUST-2024-004',
        address: 'Purok 4, Riverside',
        area: 'Riverside',
        meter_reader: 'Cardo Dalisay',
        processed_at: '2024-01-22T14:20:00',
        amount_due: 2500.0,
        status: CUSTOMER_STATUSES.DISCONNECTED,
        payment_type: 'DISCONNECTION',
        account_no: 'ACC-2024-5104',
        meter_no: 'MTR-1004',
        breakdown: [
            { group: 'Outstanding Balance', code: 'outstanding', label: 'Outstanding Balance', amount: 2400.0 },
            { group: 'Fees', code: 'disconnection_fee', label: 'Disconnection Fee', amount: 100.0 }
        ]
    },
    {
        application_id: 'APP-2024-005',
        customer_name: 'Carlos Lopez',
        customer_code: 'CUST-2024-005',
        address: 'Purok 5, Hillside',
        area: 'Hillside',
        meter_reader: 'Enteng Kabisote',
        processed_at: '2024-01-23T11:10:00',
        amount_due: 3500.0,
        status: CUSTOMER_STATUSES.RECONNECTION_PENDING,
        payment_type: 'RECONNECTION',
        account_no: 'ACC-2024-5105',
        meter_no: 'MTR-1005',
        breakdown: [
            { group: 'Reconnection', code: 'reconnection_fee', label: 'Reconnection Fee', amount: 500.0 },
            { group: 'Past Due Balances', code: 'past_due', label: 'Past Due Balance', amount: 3000.0 }
        ]
    },
    {
        application_id: 'APP-2024-006',
        customer_name: 'Elena Cruz',
        customer_code: 'CUST-2024-006',
        address: 'Purok 6, Riverside',
        area: 'Riverside',
        meter_reader: 'Juan Tamad',
        processed_at: '2024-01-24T09:10:00',
        amount_due: 900.0,
        status: CUSTOMER_STATUSES.SUSPENDED,
        payment_type: 'SUSPENSION',
        account_no: 'ACC-2024-5106',
        meter_no: 'MTR-1006',
        status_reason: 'Account suspended due to policy violation',
        breakdown: [{ group: 'Eligible Payments', code: 'eligible_balance', label: 'Eligible Balance Settlement', amount: 900.0 }]
    },
    {
        application_id: 'APP-2024-007',
        customer_name: 'Ramon Reyes',
        customer_code: 'CUST-2024-007',
        address: 'Purok 7, Poblacion',
        area: 'Poblacion',
        meter_reader: 'Pedro Penduko',
        processed_at: '2024-01-25T13:45:00',
        amount_due: 0.0,
        status: CUSTOMER_STATUSES.CLOSED_TERMINATED,
        payment_type: 'CLOSED',
        account_no: 'ACC-2024-5107',
        meter_no: 'MTR-1007',
        status_reason: 'Account closed/terminated'
    },
    {
        application_id: 'APP-2024-008',
        customer_name: 'Sofia Martinez',
        customer_code: 'CUST-2024-008',
        address: 'Purok 8, Central',
        area: 'Central',
        meter_reader: 'Juan Tamad',
        processed_at: '2024-01-26T10:30:00',
        amount_due: 1448.00,
        status: 'Overdue',
        payment_type: 'OVERDUE',
        account_no: 'ACC-2024-5108',
        meter_no: 'MTR-1008',
        breakdown: [
            { group: 'Past Due Balances', code: 'previous_balance', label: 'Previous Balance', amount: 850.00 },
            { group: 'Current Billing', code: 'current_bill', label: 'Current Bill', amount: 420.50 },
            { group: 'Penalties', code: 'late_penalty', label: 'Late Payment Penalty', amount: 127.50 },
            { group: 'Penalties', code: 'surcharge', label: 'Surcharge', amount: 50.00 }
        ]
    }
];

// Attach to window for global access if needed
if (typeof window !== 'undefined') {
    window.paymentApplications = paymentApplications;
}
