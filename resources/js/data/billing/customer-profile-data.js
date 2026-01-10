// Customer Profile Data - Complete customer information for details page
const customerProfileData = [
    { 
        id: 1,
        name: 'Juan Dela Cruz',
        customer_id: 'CUST-001',
        account_no: 'ACC-001',
        rate_class: 'Residential',
        meter_no: 'MTR-001',
        address: 'Purok 1, Poblacion, Vigan City',
        contact: '09123456789',
        email: 'juan.delacruz@email.com',
        status: 'Active',
        registration_date: '2023-06-15',
        initials: 'JD',
        current_bill: '₱739.20',
        consumption: '25 m³',
        billing_period: 'January 2024'
    },
    { 
        id: 2,
        name: 'Maria Santos',
        customer_id: 'CUST-002',
        account_no: 'ACC-002',
        rate_class: 'Residential',
        meter_no: 'MTR-002',
        address: 'Purok 2, Namacpacan, Vigan City',
        contact: '09234567890',
        email: 'maria.santos@email.com',
        status: 'Active',
        registration_date: '2023-07-20',
        initials: 'MS',
        current_bill: '₱535.80',
        consumption: '18 m³',
        billing_period: 'January 2024'
    },
    { 
        id: 3,
        name: 'Pedro Garcia',
        customer_id: 'CUST-003',
        account_no: 'ACC-003',
        rate_class: 'Commercial',
        meter_no: 'MTR-003',
        address: 'Purok 3, Magsaysay, Vigan City',
        contact: '09345678901',
        email: 'pedro.garcia@email.com',
        status: 'Active',
        registration_date: '2023-05-10',
        initials: 'PG',
        current_bill: '₱965.60',
        consumption: '32 m³',
        billing_period: 'January 2024'
    },
    { 
        id: 4,
        name: 'Rosa Mendoza',
        customer_id: 'CUST-004',
        account_no: 'ACC-004',
        rate_class: 'Residential',
        meter_no: 'MTR-004',
        address: 'Purok 4, San Fernando, Vigan City',
        contact: '09456789012',
        email: 'rosa.mendoza@email.com',
        status: 'Active',
        registration_date: '2023-08-05',
        initials: 'RM',
        current_bill: '₱445.50',
        consumption: '15 m³',
        billing_period: 'January 2024'
    },
    { 
        id: 5,
        name: 'Carlos Lopez',
        customer_id: 'CUST-005',
        account_no: 'ACC-005',
        rate_class: 'Commercial',
        meter_no: 'MTR-005',
        address: 'Purok 5, Sarao, Vigan City',
        contact: '09567890123',
        email: 'carlos.lopez@email.com',
        status: 'Active',
        registration_date: '2023-04-15',
        initials: 'CL',
        current_bill: '₱1,285.40',
        consumption: '48 m³',
        billing_period: 'January 2024'
    }
];

// Function to get customer by name
function getCustomerByName(name) {
    return customerProfileData.find(customer => customer.name === name);
}

// Function to get customer by account number
function getCustomerByAccountNo(accountNo) {
    return customerProfileData.find(customer => customer.account_no === accountNo);
}

// Function to get customer by customer ID
function getCustomerById(customerId) {
    return customerProfileData.find(customer => customer.id === customerId);
}
