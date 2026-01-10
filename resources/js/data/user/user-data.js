// User/Admin Data
const userData = [
    {
        id: 'USR-2024-001',
        user_id: 'USR-2024-001',
        name: '<div class="text-sm font-medium text-gray-900 dark:text-white">Reginald Santos</div><div class="text-xs text-gray-500">admin@initao.gov.ph</div>',
        email: 'admin@initao.gov.ph',
        role: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">System Administrator</span>',
        department: 'IT / Administration',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Active</span>',
        last_login: '2024-01-20 10:30 AM',
        actions: '<a href="/admin/users/USR-2024-001/edit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-edit mr-1"></i>Edit</a>'
    },
    {
        id: 'USR-2024-002',
        user_id: 'USR-2024-002',
        name: '<div class="text-sm font-medium text-gray-900 dark:text-white">Maria Fernandez</div><div class="text-xs text-gray-500">billing@initao.gov.ph</div>',
        email: 'billing@initao.gov.ph',
        role: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">Billing Manager</span>',
        department: 'Billing / Revenue',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Active</span>',
        last_login: '2024-01-20 09:15 AM',
        actions: '<a href="/admin/users/USR-2024-002/edit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-edit mr-1"></i>Edit</a>'
    },
    {
        id: 'USR-2024-003',
        user_id: 'USR-2024-003',
        name: '<div class="text-sm font-medium text-gray-900 dark:text-white">Jose Mercado</div><div class="text-xs text-gray-500">readings@initao.gov.ph</div>',
        email: 'readings@initao.gov.ph',
        role: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Meter Reader</span>',
        department: 'Field Operations',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Active</span>',
        last_login: '2024-01-20 08:45 AM',
        actions: '<a href="/admin/users/USR-2024-003/edit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-edit mr-1"></i>Edit</a>'
    },
    {
        id: 'USR-2024-004',
        user_id: 'USR-2024-004',
        name: '<div class="text-sm font-medium text-gray-900 dark:text-white">Angela Roque</div><div class="text-xs text-gray-500">cashier@initao.gov.ph</div>',
        email: 'cashier@initao.gov.ph',
        role: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">Cashier</span>',
        department: 'Payments / Collections',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Active</span>',
        last_login: '2024-01-20 02:30 PM',
        actions: '<a href="/admin/users/USR-2024-004/edit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-edit mr-1"></i>Edit</a>'
    },
    {
        id: 'USR-2024-005',
        user_id: 'USR-2024-005',
        name: '<div class="text-sm font-medium text-gray-900 dark:text-white">Carlos Dyan</div><div class="text-xs text-gray-500">support@initao.gov.ph</div>',
        email: 'support@initao.gov.ph',
        role: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">Support Analyst</span>',
        department: 'Customer Service',
        status: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">On Leave</span>',
        last_login: '2024-01-18 04:15 PM',
        actions: '<a href="/admin/users/USR-2024-005/edit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition inline-block"><i class="fas fa-edit mr-1"></i>Edit</a>'
    },
];

// User Roles & Permissions Data
const userRolesData = [
    {
        id: 'ROLE-001',
        role_name: 'System Administrator',
        description: 'Full system access with all permissions',
        permissions_count: 0, // All permissions
        users_count: 1,
        status: 'Active',
    },
    {
        id: 'ROLE-002',
        role_name: 'Billing Manager',
        description: 'Manage billing, rates, and billing operations',
        permissions_count: 28,
        users_count: 2,
        status: 'Active',
    },
    {
        id: 'ROLE-003',
        role_name: 'Meter Reader',
        description: 'Submit meter readings and consumption data',
        permissions_count: 5,
        users_count: 8,
        status: 'Active',
    },
    {
        id: 'ROLE-004',
        role_name: 'Cashier',
        description: 'Process payments and collection transactions',
        permissions_count: 15,
        users_count: 3,
        status: 'Active',
    },
    {
        id: 'ROLE-005',
        role_name: 'Support Analyst',
        description: 'Customer support and inquiry handling',
        permissions_count: 10,
        users_count: 4,
        status: 'Active',
    },
];

// User Details (Profile Page)
const userDetailsData = {
    id: 'USR-2024-001',
    user_id: 'USR-2024-001',
    personal_info: {
        name: 'Reginald Santos',
        email: 'admin@initao.gov.ph',
        phone: '+63 910 555 0001',
        date_of_birth: '1980-03-15',
        address: '123 Admin Lane, City, Province'
    },
    employment_info: {
        employee_id: 'EMP-2024-001',
        role: 'System Administrator',
        department: 'IT / Administration',
        supervisor: 'N/A',
        employment_date: '2015-06-01',
        employment_status: 'Permanent'
    },
    system_access: {
        username: 'admin',
        last_login: '2024-01-20 10:30 AM',
        previous_login: '2024-01-19 03:45 PM',
        login_attempts_today: 2,
        failed_login_attempts: 0,
        account_status: 'Active',
        two_factor_auth: 'Enabled'
    },
    permissions: {
        modules: ['Billing', 'Ledger', 'Rate', 'Payment', 'Customer', 'Analytics', 'Admin'],
        role_based: 'Administrator',
        custom_permissions: [],
        access_level: 'Full System Access'
    },
    activity_log: [
        { action: 'Login', date: '2024-01-20 10:30 AM', ip: '192.168.1.100' },
        { action: 'Updated Rate Settings', date: '2024-01-20 10:45 AM', ip: '192.168.1.100' },
        { action: 'Generated Report', date: '2024-01-20 11:20 AM', ip: '192.168.1.100' },
        { action: 'Login', date: '2024-01-19 03:45 PM', ip: '192.168.1.100' },
    ]
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('User data loaded');
});
