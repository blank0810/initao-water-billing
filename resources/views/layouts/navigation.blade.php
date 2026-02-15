@php
$user = Auth::user() ?? (object) [
    'name' => 'Demo User',
    'email' => 'demo@example.com',
    'photo_url' => asset('images/logo.png'),
];

// Define page titles mapping
$pageTitles = [
    'dashboard' => 'Dashboard',
    'user-add' => 'Add User',
    'user-list' => 'User List',
    'customer-add' => 'Add Customer',
    'customer-list' => 'Application List',
    'application-list' => 'Application List',
    'payment-management' => 'Payment Management',
    'approve-customer' => 'Customer Approval',
    // removed standalone application-process
    'invoice-list' => 'Invoice List',
    'declined-customer' => 'Declined Customers',
    'service-application' => 'Service Application',
    'service-connection' => 'Service Connection',
    'customer-details' => 'Customer Details',
    'billing-management' => 'Billing Management',
    'meter-management' => 'Meter Management',
    'rate-management' => 'Rate Management',
    'ledger-management' => 'Ledger Management',
    'analytics' => 'Analytics',
    'settings' => 'Settings',
    'report' => 'Report',
    'reports' => 'Reports',
    'reports-tables-billing' => 'Monthly Billing Summary',
    'reports-tables-collection' => 'Monthly Collection Summary',
    'reports-tables-status' => 'Monthly Status Report',
    'reports-tables-aging' => 'Aging of Accounts',
    'reports-tables-masterlist' => 'Consumer Master List',
    'reports-tables-abstract' => 'Abstract of Collection',
    'reports-tables-bill-history' => 'Bill History',
    'reports-tables-statement' => 'Statement of Account',
];

// Get current active menu from session or default to dashboard
    $routeToMenu = [
        'dashboard' => 'dashboard',
        'user.add' => 'user-add',
        'user.list' => 'user-list',
        'customer.add' => 'customer-add',
        'customer.list' => 'customer-list',
        'application.list' => 'application-list',
        'payment.management' => 'payment-management',
        'approve.customer' => 'approve-customer',
        // removed application.process
        'invoice.list' => 'approve-customer',
        'declined.customer' => 'approve-customer',
        'service.connection' => 'service-connection',
        'customer.list' => 'customer-list',
        'customer.details' => 'customer-details',
        'billing.main' => 'billing-management',
        'billing.management' => 'billing-management',
        'meter.management' => 'meter-management',
        'rate.management' => 'rate-management',
        'ledger.management' => 'ledger-management',
        'analytics' => 'analytics',
        'reports' => 'reports',
        'reports.tables.billing' => 'reports-tables-billing',
        'reports.tables.collection' => 'reports-tables-collection',
        'reports.tables.status' => 'reports-tables-status',
        'reports.tables.aging' => 'reports-tables-aging',
        'reports.tables.masterlist' => 'reports-tables-masterlist',
        'reports.tables.abstract' => 'reports-tables-abstract',
        'reports.tables.bill-history' => 'reports-tables-bill-history',
        'reports.tables.statement' => 'reports-tables-statement',
];
$activeMenu = $routeToMenu[Route::currentRouteName()] ?? session('active_menu', 'dashboard');
$pageTitle = $pageTitles[$activeMenu] ?? 'Dashboard';

// Define breadcrumb mapping
$breadcrumbs = [
    'dashboard' => ['Pages', 'Dashboard'],
    'user-add' => ['Pages', 'User Management', 'Add User'],
    'user-list' => ['Pages', 'User Management', 'User List'],
    'customer-add' => ['Pages', 'Application Management', 'Add Customer'],
    'application-list' => ['Pages', 'Application Management', 'Application List'],
    'customer-list' => ['Pages', 'Customer Management', 'Customer List'],
    'payment-management' => ['Pages', 'Payment Management'],
    'approve-customer' => ['Pages', 'Application Management', 'Customer Approval'],
    'invoice-list' => ['Pages', 'Application Management', 'Invoice List'],
    'declined-customer' => ['Pages', 'Application Management', 'Declined Customers'],
    'service-application' => ['Pages', 'Service Application'],
    'service-connection' => ['Pages', 'Service Connection'],
    'customer-details' => ['Pages', 'Customer Management', 'Customer Details'],
    'billing-management' => ['Pages', 'Billing Management'],
    'meter-management' => ['Pages', 'Meter Management'],
    'rate-management' => ['Pages', 'Rate Management'],
    'ledger-management' => ['Pages', 'Ledger Management'],
    'analytics' => ['Pages', 'Analytics'],
    'settings' => ['Pages', 'Settings'],
    'report' => ['Pages', 'Report'],
    'reports' => ['Pages', 'Reports'],
    'reports-tables-billing' => ['Pages', 'Reports', 'Monthly Billing Summary'],
    'reports-tables-collection' => ['Pages', 'Reports', 'Monthly Collection Summary'],
    'reports-tables-status' => ['Pages', 'Reports', 'Monthly Status Report'],
    'reports-tables-aging' => ['Pages', 'Reports', 'Aging of Accounts'],
    'reports-tables-masterlist' => ['Pages', 'Reports', 'Consumer Master List'],
    'reports-tables-abstract' => ['Pages', 'Reports', 'Abstract of Collection'],
    'reports-tables-bill-history' => ['Pages', 'Reports', 'Bill History'],
    'reports-tables-statement' => ['Pages', 'Reports', 'Statement of Account'],
];

$currentBreadcrumbs = $breadcrumbs[$activeMenu] ?? ['Pages', 'Dashboard'];
$hideBreadcrumb = in_array(Route::currentRouteName(), ['approve.customer']);
@endphp

<nav x-data="{ notifOpen: false, userOpen: false }" class="bg-[#e7e7e7] dark:bg-[#0d131c] z-30 relative sticky top-0">
    <div class="max-w-full mx-auto px-6 sm:px-8 lg:px-10">
        <div class="flex justify-between h-24 items-center">

            <!-- Left: Mobile menu button and breadcrumb -->
            <div class="flex items-center space-x-4">
                <!-- Mobile menu button -->
                <button @click="window.appState?.toggleSidebar()" data-sidebar-toggle
                    class="lg:hidden p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 text-gray-600 dark:text-gray-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Breadcrumb & Title -->
                <div class="flex flex-col justify-center">
                    @if(!$hideBreadcrumb)
                        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                            @foreach($currentBreadcrumbs as $index => $crumb)
                                @if($index < count($currentBreadcrumbs) - 1)
                                    <li class="flex items-center">
                                        <span class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">{{ $crumb }}</span>
                                        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </li>
                                @else
                                    <li class="font-medium text-gray-700 dark:text-gray-300">{{ $crumb }}</li>
                                @endif
                            @endforeach
                        </ol>
                    @endif
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pageTitle }}</h1>
                </div>
            </div>

            <!-- Right: Controls -->
            <div class="flex items-center space-x-4">

                <!-- Search Bar -->
                <div class="relative hidden lg:block">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Search..."
                        class="pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-[#111826] border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all duration-200 w-64 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                    >
                </div>

                <!-- Theme Toggle -->
                <button x-data="{
                    isDark: document.documentElement.classList.contains('dark'),
                    init() {
                        // Listen for external theme changes
                        window.addEventListener('theme-changed', (e) => {
                            this.isDark = e.detail.isDark;
                        });
                    },
                    toggle() {
                        window.toggleTheme();
                        this.isDark = window.isDarkTheme();
                    }
                }"
                @click="toggle()"
                class="p-2.5 rounded-lg bg-gray-100 dark:bg-[#111826] border border-gray-200 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 text-gray-600 dark:text-gray-300"
                title="Toggle theme">
                    <!-- Sun for Light Mode -->
                    <svg x-show="!isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Moon for Dark Mode -->
                    <svg x-show="isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Notifications -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open; $event.stopPropagation()"
                            class="relative p-2.5 rounded-lg bg-gray-100 dark:bg-[#111826] border border-gray-200 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 text-gray-600 dark:text-gray-300">
                        <!-- Bell Icon -->
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center border-2 border-white dark:border-gray-800 font-medium">3</span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">

                        <!-- Header -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Notifications</h3>
                        </div>

                        <!-- Notifications List -->
                        <div class="max-h-96 overflow-y-auto">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition duration-150">
                                <p class="text-sm text-gray-800 dark:text-gray-200">New customer application pending approval</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">2 minutes ago</span>
                            </div>
                            <div class="p-4 border-b border-gray-100 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition duration-150">
                                <p class="text-sm text-gray-800 dark:text-gray-200">Payment received from John Doe</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">1 hour ago</span>
                            </div>
                            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition duration-150">
                                <p class="text-sm text-gray-800 dark:text-gray-200">System maintenance scheduled</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Yesterday</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">View all notifications</a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open; $event.stopPropagation()"
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200">
                        <div class="h-8 w-8 rounded-full overflow-hidden">
                            <img src="{{ $user->photo_url }}" class="w-full h-full object-cover" alt="{{ $user->name }}">
                        </div>
                        <div class="hidden md:block text-left">
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Administrator</div>
                        </div>
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">

                        <!-- User Info -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                        </div>

                        <!-- Menu Items -->
                        <div class="p-2">
                            <a href="{{ route('user.add') }}" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition duration-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9a3 3 0 11-6 0 3 3 0 016 0zM6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                                </svg>
                                <span>Add User</span>
                            </a>

                            <a href="{{ route('connection.service-application.create') }}" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition duration-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>New Application</span>
                            </a>

                            <a href="{{ route('user-manual') }}" target="_blank" rel="noopener" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition duration-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>User Manual</span>
                                <svg class="h-3 w-3 ml-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>

                               
                        </div>

                        <!-- Logout -->
                        <div class="p-2 border-t border-gray-200 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center space-x-2 w-full px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition duration-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Log Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</nav>
