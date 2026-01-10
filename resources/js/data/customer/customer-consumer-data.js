// Customer/Consumer Data
const customerConsumerData = [
    {
        id: 'C-2024-001',
        customer_id: 'C-2024-001',
        name: '<div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">ACC-2024-001</div>',
        category: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">Residential</span>',
        rate_category: 'Residential Standard',
        address: '123 Main Street, Barangay San Jose, City',
        billing_period: 'Monthly (Jan 2024)',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Active</span>',
        actions: '<a href="/customer/show/C-2024-001" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'C-2024-002',
        customer_id: 'C-2024-002',
        name: '<div class="text-sm font-medium text-gray-900 dark:text-white">Maria Santos</div><div class="text-xs text-gray-500">ACC-2024-002</div>',
        category: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">Residential</span>',
        rate_category: 'Residential Standard',
        address: '456 Oak Avenue, Barangay Santa Maria, City',
        billing_period: 'Monthly (Jan 2024)',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Active</span>',
        actions: '<a href="/customer/show/C-2024-002" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'C-2024-003',
        customer_id: 'C-2024-003',
        name: '<div class="text-sm font-medium text-gray-900 dark:text-white">Angel Construction Inc.</div><div class="text-xs text-gray-500">ACC-2024-003</div>',
        category: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Commercial</span>',
        rate_category: 'Commercial',
        address: '789 Industrial Park, Barangay Libis, City',
        billing_period: 'Monthly (Jan 2024)',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Active</span>',
        actions: '<a href="/customer/show/C-2024-003" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'C-2024-004',
        customer_id: 'C-2024-004',
        name: '<div class="text-sm font-medium text-gray-900 dark:text-white">Pedro Reyes</div><div class="text-xs text-gray-500">ACC-2024-004</div>',
        category: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">Residential</span>',
        rate_category: 'Residential Standard',
        address: '321 Maple Drive, Barangay Quezon, City',
        billing_period: 'Monthly (Jan 2024)',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Active</span>',
        actions: '<a href="/customer/show/C-2024-004" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
    {
        id: 'C-2024-005',
        customer_id: 'C-2024-005',
        name: '<div class="text-sm font-medium text-gray-900 dark:text-white">City Hospital</div><div class="text-xs text-gray-500">ACC-2024-005</div>',
        category: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">Institutional</span>',
        rate_category: 'Institutional',
        address: '654 Health Boulevard, Medical District, City',
        billing_period: 'Monthly (Jan 2024)',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Active</span>',
        actions: '<a href="/customer/show/C-2024-005" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-eye mr-1"></i>View</a>'
    },
];

// Customer Details (Show Page)
const customerDetailsData = {
    id: 'C-2024-001',
    customer_id: 'C-2024-001',
    account_info: {
        account_no: 'ACC-2024-001',
        account_status: 'Active',
        account_type: 'Individual',
        date_registered: '2022-03-15'
    },
    personal_info: {
        name: 'Juan Dela Cruz',
        date_of_birth: '1975-08-20',
        email: 'juan.delacruz@example.com',
        phone: '+63 910 123 4567',
        id_type: 'National ID',
        id_number: '12-3456789-0'
    },
    service_info: {
        rate_id: 'RT-2024-001',
        rate_category: 'Residential Standard',
        billing_period: 'BP-2024-01',
        meter_id: 'MTR-2024-001',
        meter_serial: 'M-2024-00001'
    },
    address_info: {
        service_address: '123 Main Street, Barangay San Jose, City',
        postal_code: '1234',
        province: 'Metro Manila',
        contact_person: 'Juan Dela Cruz'
    },
    billing_summary: {
        current_bill: 'B-2024-001',
        bill_amount: '₱739.20',
        due_date: '2024-02-08',
        total_unpaid: '₱0.00',
        last_payment: 'PT-2024-0001',
        last_payment_date: '2024-01-15'
    }
};

// Initialize table on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Customer data loaded');
});
