@php
$user = Auth::user() ?? (object) [
    'name' => 'Demo User',
    'email' => 'demo@example.com',
];
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEEDO Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom scrollbar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.5);
            border-radius: 10px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.8);
        }

        /* Smooth transitions */
        .menu-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Glow effect for active items */
        .active-glow {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
        }

        /* Hover effect for menu items */
        .menu-hover:hover {
            transform: translateX(5px);
        }

        /* Logo animation */
        .logo-container {
            transition: all 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        <!-- Sidebar -->
        <aside x-data="sidebar()"
            class="flex flex-col flex-shrink-0 w-72 bg-gradient-to-b from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border-r border-gray-200 dark:border-gray-700 shadow-xl transition-all duration-500 ease-in-out h-screen fixed left-0 top-0 z-40"
            :class="{ 'w-20': !open }">

            <!-- Header with Logo -->
            <div
                class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                <a href="{{ route('dashboard') }}" @click="setActiveMenu('dashboard')" class="flex items-center space-x-3 min-w-0 logo-container">
                    <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transition-all duration-300 hover:shadow-blue-500/25"
                        :class="open ? 'bg-gradient-to-br from-blue-500 to-indigo-600' : 'bg-gray-100 dark:bg-gray-700'">
                        <i class="fas text-xl transition-colors duration-300"
                            :class="open ? 'fa-bolt text-white' : 'fa-bars text-blue-500 dark:text-blue-400'"></i>
                    </div>
                    <div class="min-w-0 flex-1" x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0">
                        <h1
                            class="text-xl font-bold text-gray-800 dark:text-white truncate bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            MEEDO Dashboard</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-1 flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                            Administrator
                        </p>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto sidebar-scroll">

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" @click="setActiveMenu('dashboard')"
                    :class="activeMenu === 'dashboard' ?
                        'bg-gradient-to-r from-blue-500 to-indigo-500 text-white active-glow shadow-md' :
                        'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/50 menu-hover'"
                    class="flex items-center p-3 rounded-xl menu-item transition-all duration-300 group">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300"
                        :class="activeMenu === 'dashboard' ? 'bg-white/20' :
                            'bg-blue-100 dark:bg-gray-700 group-hover:bg-blue-200 dark:group-hover:bg-gray-600'">
                        <i class="fas fa-chart-pie"
                            :class="activeMenu === 'dashboard' ? 'text-white' : 'text-blue-600 dark:text-blue-400'"></i>
                    </div>
                    <span class="ml-3 font-medium" x-show="open" x-transition>Dashboard</span>
                    <span x-show="activeMenu === 'dashboard' && open" class="ml-auto">
                        <i class="fas fa-chevron-right text-xs text-white/70"></i>
                    </span>
                </a>

                <!-- User Management -->
                <div class="space-y-2">
                    <button @click="toggleSubmenu('userManagement')"
                        :class="activeMenu.startsWith('user-') ?
                            'bg-gradient-to-r from-blue-500 to-indigo-500 text-white active-glow shadow-md' :
                            'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/50 menu-hover'"
                        class="flex items-center justify-between w-full p-3 rounded-xl menu-item transition-all duration-300 group">
                        <div class="flex items-center min-w-0">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300"
                                :class="activeMenu.startsWith('user-') ? 'bg-white/20' :
                                    'bg-blue-100 dark:bg-gray-700 group-hover:bg-blue-200 dark:group-hover:bg-gray-600'">
                                <i class="fas fa-users"
                                    :class="activeMenu.startsWith('user-') ? 'text-white' : 'text-blue-600 dark:text-blue-400'"></i>
                            </div>
                            <span class="ml-3 font-medium truncate" x-show="open" x-transition>User Management</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300 flex-shrink-0 ml-2"
                            :class="{ 'rotate-180': openSubmenus.userManagement, 'text-white/70': activeMenu.startsWith('user-') }"
                            x-show="open" x-transition></i>
                    </button>

                    <div x-show="openSubmenus.userManagement && open" x-collapse
                        class="ml-4 pl-6 border-l-2 border-blue-200 dark:border-gray-600 space-y-2 mt-2">
                        <a href="{{ route('user.add') }}" @click="setActiveMenu('user-add')"
                            :class="activeMenu === 'user-add' ?
                                'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-r-2 border-blue-500' :
                                'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            class="flex items-center p-2 rounded-lg transition-all duration-300 text-sm menu-item menu-hover">
                            <i class="fas fa-user-plus w-5 text-center mr-3"
                                :class="activeMenu === 'user-add' ? 'text-blue-500' : 'text-gray-500'"></i>
                            Add User
                        </a>
                        <a href="{{ route('user.list') }}" @click="setActiveMenu('user-list')"
                            :class="activeMenu === 'user-list' ?
                                'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-r-2 border-blue-500' :
                                'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            class="flex items-center p-2 rounded-lg transition-all duration-300 text-sm menu-item menu-hover">
                            <i class="fas fa-list w-5 text-center mr-3"
                                :class="activeMenu === 'user-list' ? 'text-blue-500' : 'text-gray-500'"></i>
                            User List
                        </a>
                    </div>
                </div>

                <!-- Customer Application -->
                <div class="space-y-2">
                    <button @click="toggleSubmenu('customerApplication')"
                        :class="activeMenu.startsWith('customer-') ?
                            'bg-gradient-to-r from-blue-500 to-indigo-500 text-white active-glow shadow-md' :
                            'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/50 menu-hover'"
                        class="flex items-center justify-between w-full p-3 rounded-xl menu-item transition-all duration-300 group">
                        <div class="flex items-center min-w-0">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300"
                                :class="activeMenu.startsWith('customer-') ? 'bg-white/20' :
                                    'bg-blue-100 dark:bg-gray-700 group-hover:bg-blue-200 dark:group-hover:bg-gray-600'">
                                <i class="fas fa-user-tie"
                                    :class="activeMenu.startsWith('customer-') ? 'text-white' :
                                        'text-blue-600 dark:text-blue-400'"></i>
                            </div>
                            <span class="ml-3 font-medium truncate" x-show="open" x-transition>Customer
                                Application</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300 flex-shrink-0 ml-2"
                            :class="{ 'rotate-180': openSubmenus.customerApplication, 'text-white/70': activeMenu.startsWith('customer-') }"
                            x-show="open" x-transition></i>
                    </button>

                    <div x-show="openSubmenus.customerApplication && open" x-collapse
                        class="ml-4 pl-6 border-l-2 border-blue-200 dark:border-gray-600 space-y-2 mt-2">

                        <!-- Add Customer -->
                        <a href="{{ route('customer.add') }}" @click="setActiveMenu('customer-add')"
                            :class="activeMenu === 'customer-add' ?
                                'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-r-2 border-blue-500' :
                                'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            class="flex items-center p-2 rounded-lg transition-all duration-300 text-sm menu-item menu-hover">
                            <i class="fas fa-user-plus w-5 text-center mr-3"
                                :class="activeMenu === 'customer-add' ? 'text-blue-500' : 'text-gray-500'"></i>
                            Add Customer
                        </a>

                        <!-- Customer List -->
                        <a href="{{ route('customer.list') }}" @click="setActiveMenu('customer-list')"
                            :class="activeMenu === 'customer-list' ?
                                'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-r-2 border-blue-500' :
                                'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            class="flex items-center p-2 rounded-lg transition-all duration-300 text-sm menu-item menu-hover">
                            <i class="fas fa-list w-5 text-center mr-3"
                                :class="activeMenu === 'customer-list' ? 'text-blue-500' : 'text-gray-500'"></i>
                            Customer List
                        </a>

                        <!-- Payment Management -->
                        <a href="{{ route('payment.management') }}" @click="setActiveMenu('payment-management')"
                            :class="activeMenu === 'payment-management' ?
                                'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-r-2 border-blue-500' :
                                'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            class="flex items-center p-2 rounded-lg transition-all duration-300 text-sm menu-item menu-hover">
                            <i class="fas fa-credit-card w-5 text-center mr-3"
                                :class="activeMenu === 'payment-management' ? 'text-blue-500' : 'text-gray-500'"></i>
                            Payment Management
                        </a>

                        <!-- Customer Approval -->
                        <a href="{{ route('approve-customer') }}" @click="setActiveMenu('approve-customer')"
                            :class="activeMenu === 'approve-customer' ?
                                'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-r-2 border-blue-500' :
                                'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            class="flex items-center p-2 rounded-lg transition-all duration-300 text-sm menu-item menu-hover">
                            <i class="fas fa-check-circle w-5 text-center mr-3"
                                :class="activeMenu === 'approve-customer' ? 'text-blue-500' : 'text-gray-500'"></i>
                            Customer Approval
                        </a>

                    </div>

                </div>

                <!-- Consumer List -->
                <a href="{{ route('consumer.list') }}" @click="setActiveMenu('consumer-list')"
                    :class="activeMenu === 'consumer-list' ?
                        'bg-gradient-to-r from-blue-500 to-indigo-500 text-white active-glow shadow-md' :
                        'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/50 menu-hover'"
                    class="flex items-center p-3 rounded-xl menu-item transition-all duration-300 group">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300"
                        :class="activeMenu === 'consumer-list' ? 'bg-white/20' :
                            'bg-blue-100 dark:bg-gray-700 group-hover:bg-blue-200 dark:group-hover:bg-gray-600'">
                        <i class="fas fa-users"
                            :class="activeMenu === 'consumer-list' ? 'text-white' : 'text-blue-600 dark:text-blue-400'"></i>
                    </div>
                    <span class="ml-3 font-medium" x-show="open" x-transition>Consumer List</span>
                    <span x-show="activeMenu === 'consumer-list' && open" class="ml-auto">
                        <i class="fas fa-chevron-right text-xs text-white/70"></i>
                    </span>
                </a>

                <!-- Billing Management -->
                <a href="{{ route('billing.management') }}" @click="setActiveMenu('billing-management')"
                    :class="activeMenu === 'billing-management' ?
                        'bg-gradient-to-r from-blue-500 to-indigo-500 text-white active-glow shadow-md' :
                        'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/50 menu-hover'"
                    class="flex items-center p-3 rounded-xl menu-item transition-all duration-300 group">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300"
                        :class="activeMenu === 'billing-management' ? 'bg-white/20' :
                            'bg-blue-100 dark:bg-gray-700 group-hover:bg-blue-200 dark:group-hover:bg-gray-600'">
                        <i class="fas fa-file-invoice-dollar"
                            :class="activeMenu === 'billing-management' ? 'text-white' : 'text-blue-600 dark:text-blue-400'"></i>
                    </div>
                    <span class="ml-3 font-medium" x-show="open" x-transition>Billing Management</span>
                    <span x-show="activeMenu === 'billing-management' && open" class="ml-auto">
                        <i class="fas fa-chevron-right text-xs text-white/70"></i>
                    </span>
                </a>

                <!-- Meter Management -->
                <a href="{{ route('meter.management') }}" @click="setActiveMenu('meter-management')"
                    :class="activeMenu === 'meter-management' ?
                        'bg-gradient-to-r from-blue-500 to-indigo-500 text-white active-glow shadow-md' :
                        'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/50 menu-hover'"
                    class="flex items-center p-3 rounded-xl menu-item transition-all duration-300 group">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300"
                        :class="activeMenu === 'meter-management' ? 'bg-white/20' :
                            'bg-blue-100 dark:bg-gray-700 group-hover:bg-blue-200 dark:group-hover:bg-gray-600'">
                        <i class="fas fa-tachometer-alt"
                            :class="activeMenu === 'meter-management' ? 'text-white' : 'text-blue-600 dark:text-blue-400'"></i>
                    </div>
                    <span class="ml-3 font-medium" x-show="open" x-transition>Meter Management</span>
                    <span x-show="activeMenu === 'meter-management' && open" class="ml-auto">
                        <i class="fas fa-chevron-right text-xs text-white/70"></i>
                    </span>
                </a>

                <!-- Rate Management -->
                <a href="{{ route('rate.management') }}" @click="setActiveMenu('rate-management')"
                    :class="activeMenu === 'rate-management' ?
                        'bg-gradient-to-r from-blue-500 to-indigo-500 text-white active-glow shadow-md' :
                        'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/50 menu-hover'"
                    class="flex items-center p-3 rounded-xl menu-item transition-all duration-300 group">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300"
                        :class="activeMenu === 'rate-management' ? 'bg-white/20' :
                            'bg-blue-100 dark:bg-gray-700 group-hover:bg-blue-200 dark:group-hover:bg-gray-600'">
                        <i class="fas fa-percentage"
                            :class="activeMenu === 'rate-management' ? 'text-white' : 'text-blue-600 dark:text-blue-400'"></i>
                    </div>
                    <span class="ml-3 font-medium" x-show="open" x-transition>Rate Management</span>
                    <span x-show="activeMenu === 'rate-management' && open" class="ml-auto">
                        <i class="fas fa-chevron-right text-xs text-white/70"></i>
                    </span>
                </a>

                <!-- Ledger Management -->
                <a href="{{ route('ledger.management') }}" @click="setActiveMenu('ledger-management')"
                    :class="activeMenu === 'ledger-management' ?
                        'bg-gradient-to-r from-blue-500 to-indigo-500 text-white active-glow shadow-md' :
                        'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/50 menu-hover'"
                    class="flex items-center p-3 rounded-xl menu-item transition-all duration-300 group">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300"
                        :class="activeMenu === 'ledger-management' ? 'bg-white/20' :
                            'bg-blue-100 dark:bg-gray-700 group-hover:bg-blue-200 dark:group-hover:bg-gray-600'">
                        <i class="fas fa-book"
                            :class="activeMenu === 'ledger-management' ? 'text-white' : 'text-blue-600 dark:text-blue-400'"></i>
                    </div>
                    <span class="ml-3 font-medium" x-show="open" x-transition>Ledger Management</span>
                    <span x-show="activeMenu === 'ledger-management' && open" class="ml-auto">
                        <i class="fas fa-chevron-right text-xs text-white/70"></i>
                    </span>
                </a>

                <!-- Analytics -->
                <a href="{{ route('analytics') }}" @click="setActiveMenu('analytics')"
                    :class="activeMenu === 'analytics' ?
                        'bg-gradient-to-r from-blue-500 to-indigo-500 text-white active-glow shadow-md' :
                        'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700/50 menu-hover'"
                    class="flex items-center p-3 rounded-xl menu-item transition-all duration-300 group">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300"
                        :class="activeMenu === 'analytics' ? 'bg-white/20' :
                            'bg-blue-100 dark:bg-gray-700 group-hover:bg-blue-200 dark:group-hover:bg-gray-600'">
                        <i class="fas fa-chart-line"
                            :class="activeMenu === 'analytics' ? 'text-white' : 'text-blue-600 dark:text-blue-400'"></i>
                    </div>
                    <span class="ml-3 font-medium" x-show="open" x-transition>Analytics</span>
                    <span x-show="activeMenu === 'analytics' && open" class="ml-auto">
                        <i class="fas fa-chevron-right text-xs text-white/70"></i>
                    </span>
                </a>
            </nav>
        </aside>
    </div>

    <script>
        function sidebar() {
            return {
                open: true,
                activeMenu: '{{ session('active_menu', 'dashboard') }}',
                openSubmenus: {
                    userManagement: {{ session('active_menu') && str_starts_with(session('active_menu'), 'user-') ? 'true' : 'false' }},
                    customerApplication: {{ session('active_menu') && str_starts_with(session('active_menu'), 'customer-') ? 'true' : 'false' }}
                },
                toggleSidebar() {
                    this.open = !this.open;
                    // Close all submenus when collapsing the sidebar
                    if (!this.open) {
                        this.openSubmenus.userManagement = false;
                        this.openSubmenus.customerApplication = false;
                    }
                },
                toggleSubmenu(submenu) {
                    if (this.open) {
                        this.openSubmenus[submenu] = !this.openSubmenus[submenu];
                    }
                },
                setActiveMenu(menu) {
                    this.activeMenu = menu;
                    // Update submenu states based on active menu
                    if (menu.startsWith('user-')) {
                        this.openSubmenus.userManagement = true;
                    }
                    if (menu.startsWith('customer-')) {
                        this.openSubmenus.customerApplication = true;
                    }

                    // Store in localStorage for persistence
                    localStorage.setItem('activeMenu', menu);
                },
                init() {
                    // Get active menu from localStorage on page load
                    const savedMenu = localStorage.getItem('activeMenu');
                    if (savedMenu) {
                        this.activeMenu = savedMenu;
                        // Set submenu states based on saved menu
                        if (savedMenu.startsWith('user-')) {
                            this.openSubmenus.userManagement = true;
                        }
                        if (savedMenu.startsWith('customer-')) {
                            this.openSubmenus.customerApplication = true;
                        }
                    }
                }
            }
        }
    </script>
</body>

</html>
