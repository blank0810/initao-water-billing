@php
$user = Auth::user() ?? (object) [
    'name' => 'Demo User',
    'email' => 'demo@example.com',
];
@endphp

<!-- Sidebar -->
<aside x-data="sidebar()"
    class="hidden lg:flex flex-col flex-shrink-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 sidebar-transition h-screen fixed left-0 top-0 z-50 overflow-hidden"
    :class="sidebarOpen ? 'w-72' : 'w-20'">

    <!-- Header with Logo -->
    <div class="flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-800 h-24">
        <button @click="toggleSidebar()" class="flex items-center space-x-3 w-full min-w-0 group">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-11 h-11 rounded-lg object-cover">
            </div>

            <!-- Title (when expanded) -->
            <div class="min-w-0 flex-1" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
<p class="text-2xl font-semibold tracking-wide text-gray-700 dark:text-gray-300 flex items-center">
    MEEDO
</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                    Administrator
                </p>
            </div>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" @click="setActiveMenu('dashboard')"
            :class="activeMenu === 'dashboard' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'dashboard' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-chart-pie text-sm" :class="activeMenu === 'dashboard' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>

        <!-- User Management -->
        @can('users.view')
        <div class="space-y-1">
            <button @click="toggleSubmenu('userManagement')"
                :class="(activeMenu.startsWith('user-') || openSubmenus.userManagement) ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
                class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl transition-all duration-200 group">
                <div class="flex items-center min-w-0">
                    <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                        :class="(activeMenu.startsWith('user-') || openSubmenus.userManagement) ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                        <i class="fas fa-users text-sm" :class="(activeMenu.startsWith('user-') || openSubmenus.userManagement) ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
                    </div>
                    <span class="ml-3 text-sm font-medium truncate" x-show="sidebarOpen" x-transition>User Management</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.userManagement }" x-show="sidebarOpen" x-transition></i>
            </button>

            <div x-show="openSubmenus.userManagement && sidebarOpen" x-collapse class="ml-3 pl-6 border-l-2 border-gray-200 dark:border-gray-700 space-y-1 mt-1">
                @can('users.manage')
                <a href="{{ route('user.add') }}" @click="setActiveMenu('user-add')"
                    :class="activeMenu === 'user-add' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-user-plus w-4 text-xs mr-2.5"></i>
                    <span>Add User</span>
                </a>
                @endcan
                <a href="{{ route('user.list') }}" @click="setActiveMenu('user-list')"
                    :class="activeMenu === 'user-list' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-list w-4 text-xs mr-2.5"></i>
                    <span>User List</span>
                </a>
            </div>
        </div>
        @endcan

        <!-- Connection Management -->
        @can('customers.manage')
        <div class="space-y-1">
            <button @click="toggleSubmenu('connectionManagement')"
                :class="(activeMenu.startsWith('connection-') || openSubmenus.connectionManagement) ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
                class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl transition-all duration-200 group">
                <div class="flex items-center min-w-0">
                    <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                        :class="(activeMenu.startsWith('connection-') || openSubmenus.connectionManagement) ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                        <i class="fas fa-plug text-sm" :class="(activeMenu.startsWith('connection-') || openSubmenus.connectionManagement) ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
                    </div>
                    <span class="ml-3 text-sm font-medium truncate" x-show="sidebarOpen" x-transition>Connection Management</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.connectionManagement }" x-show="sidebarOpen" x-transition></i>
            </button>

            <div x-show="openSubmenus.connectionManagement && sidebarOpen" x-collapse class="ml-3 pl-6 border-l-2 border-gray-200 dark:border-gray-700 space-y-1 mt-1">
                <a href="{{ route('connection.service-application.create') }}" @click="setActiveMenu('connection-new')"
                    :class="activeMenu === 'connection-new' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-plus-circle w-4 text-xs mr-2.5"></i>
                    <span>New Application</span>
                </a>
                <a href="{{ route('connection.service-application.index') }}" @click="setActiveMenu('connection-applications')"
                    :class="activeMenu === 'connection-applications' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-file-alt w-4 text-xs mr-2.5"></i>
                    <span>Applications</span>
                </a>
                <a href="{{ route('service.connection') }}" @click="setActiveMenu('connection-active')"
                    :class="activeMenu === 'connection-active' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-plug w-4 text-xs mr-2.5"></i>
                    <span>Active Connections</span>
                </a>
            </div>
        </div>
        @endcan

        <!-- Customer Management -->
        @can('customers.view')
        <div class="space-y-1">
            <button @click="toggleSubmenu('customerManagement')"
                :class="(activeMenu.startsWith('customer-') || openSubmenus.customerManagement) ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
                class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl transition-all duration-200 group">
                <div class="flex items-center min-w-0">
                    <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                        :class="(activeMenu.startsWith('customer-') || openSubmenus.customerManagement) ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                        <i class="fas fa-user-tie text-sm" :class="(activeMenu.startsWith('customer-') || openSubmenus.customerManagement) ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
                    </div>
                    <span class="ml-3 text-sm font-medium truncate" x-show="sidebarOpen" x-transition>Customer Management</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.customerManagement }" x-show="sidebarOpen" x-transition></i>
            </button>

            <div x-show="openSubmenus.customerManagement && sidebarOpen" x-collapse class="ml-3 pl-6 border-l-2 border-gray-200 dark:border-gray-700 space-y-1 mt-1">
                <a href="{{ route('customer.list') }}" @click="setActiveMenu('customer-list')"
                    :class="activeMenu === 'customer-list' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-list w-4 text-xs mr-2.5"></i>
                    <span>Customer List</span>
                </a>
                @can('customers.manage')
                <a href="{{ route('approve.customer') }}" @click="setActiveMenu('customer-approval')"
                    :class="activeMenu === 'customer-approval' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-check-circle w-4 text-xs mr-2.5"></i>
                    <span>Customer Approval</span>
                </a>
                @endcan
            </div>
        </div>
        @endcan

        <!-- Consumer List -->
        @can('customers.view')
        <a href="{{ route('consumer.list') }}" @click="setActiveMenu('consumer-list')"
            :class="activeMenu === 'consumer-list' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'consumer-list' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-file-alt text-sm" :class="activeMenu === 'consumer-list' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Consumer List</span>
        </a>
        @endcan

        <!-- Payment Management -->
        @can('payments.view')
        <a href="{{ route('payment.management') }}" @click="setActiveMenu('payment-management')"
            :class="activeMenu === 'payment-management' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'payment-management' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-credit-card text-sm" :class="activeMenu === 'payment-management' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Payment Management</span>
        </a>
        @endcan

        <!-- Billing Management -->
        @can('billing.view')
        <a href="{{ route('billing.management') }}" @click="setActiveMenu('billing-management')"
            :class="activeMenu === 'billing-management' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'billing-management' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-file-invoice-dollar text-sm" :class="activeMenu === 'billing-management' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Billing</span>
        </a>
        @endcan

        <!-- Meter Management -->
        @can('meters.view')
        <a href="{{ route('meter.management') }}" @click="setActiveMenu('meter-management')"
            :class="activeMenu === 'meter-management' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'meter-management' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-tachometer-alt text-sm" :class="activeMenu === 'meter-management' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Meter</span>
        </a>
        @endcan

        <!-- Rate Management -->
        @can('settings.manage')
        <a href="{{ route('rate.management') }}" @click="setActiveMenu('rate-management')"
            :class="activeMenu === 'rate-management' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'rate-management' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-percentage text-sm" :class="activeMenu === 'rate-management' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Rate</span>
        </a>
        @endcan

        <!-- Ledger Management -->
        @can('billing.view')
        <a href="{{ route('ledger.management') }}" @click="setActiveMenu('ledger-management')"
            :class="activeMenu === 'ledger-management' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'ledger-management' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-book text-sm" :class="activeMenu === 'ledger-management' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Ledger</span>
        </a>
        @endcan

        <!-- Analytics -->
        @can('reports.view')
        <a href="{{ route('analytics') }}" @click="setActiveMenu('analytics')"
            :class="activeMenu === 'analytics' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'analytics' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-chart-line text-sm" :class="activeMenu === 'analytics' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Analytics</span>
        </a>
        @endcan

        <!-- Admin Configuration -->
        @canany(['config.geographic.manage', 'config.billing.manage', 'config.access.manage'])
        <div class="space-y-1">
            <button @click="toggleSubmenu('adminConfig')"
                :class="(activeMenu.startsWith('config-') || openSubmenus.adminConfig) ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
                class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl transition-all duration-200 group">
                <div class="flex items-center min-w-0">
                    <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                        :class="(activeMenu.startsWith('config-') || openSubmenus.adminConfig) ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                        <i class="fas fa-cogs text-sm" :class="(activeMenu.startsWith('config-') || openSubmenus.adminConfig) ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
                    </div>
                    <span class="ml-3 text-sm font-medium truncate" x-show="sidebarOpen" x-transition>Admin Configuration</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.adminConfig }" x-show="sidebarOpen" x-transition></i>
            </button>

            <div x-show="openSubmenus.adminConfig && sidebarOpen" x-collapse class="ml-3 pl-6 border-l-2 border-gray-200 dark:border-gray-700 space-y-1 mt-1">

                <!-- Geographic Submenu -->
                @can('config.geographic.manage')
                <div class="space-y-1">
                    <button @click="toggleSubmenu('geographic')"
                        :class="(activeMenu.startsWith('config-geographic-') || openSubmenus.geographic) ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-map-marked-alt w-4 text-xs mr-2.5"></i>
                            <span>Geographic</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.geographic }"></i>
                    </button>

                    <div x-show="openSubmenus.geographic" x-collapse class="ml-6 space-y-1 mt-1">
                        <a href="{{ route('config.barangays.index') }}" @click="setActiveMenu('config-geographic-barangays')"
                            :class="activeMenu === 'config-geographic-barangays' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-map-marker-alt w-4 text-xs mr-2.5"></i>
                            <span>Barangays</span>
                        </a>
                        <a href="{{ route('config.areas.index') }}" @click="setActiveMenu('config-geographic-areas')"
                            :class="activeMenu === 'config-geographic-areas' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-layer-group w-4 text-xs mr-2.5"></i>
                            <span>Areas</span>
                        </a>
                        <a href="{{ route('config.puroks.index') }}" @click="setActiveMenu('config-geographic-puroks')"
                            :class="activeMenu === 'config-geographic-puroks' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-house-user w-4 text-xs mr-2.5"></i>
                            <span>Puroks</span>
                        </a>
                    </div>
                </div>
                @endcan

                <!-- Water Rates -->
                @can('config.billing.manage')
                <a href="{{ route('config.water-rates.index') }}" @click="setActiveMenu('config-water-rates')"
                    :class="activeMenu === 'config-water-rates' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-dollar-sign w-4 text-xs mr-2.5"></i>
                    <span>Water Rates</span>
                </a>

                <!-- Billing Configuration Submenu -->
                <div class="space-y-1">
                    <button @click="toggleSubmenu('billingConfig')"
                        :class="(activeMenu.startsWith('config-billing-') || openSubmenus.billingConfig) ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-receipt w-4 text-xs mr-2.5"></i>
                            <span>Billing Configuration</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.billingConfig }"></i>
                    </button>

                    <div x-show="openSubmenus.billingConfig" x-collapse class="ml-6 space-y-1 mt-1">
                        <a href="{{ route('config.account-types.index') }}" @click="setActiveMenu('config-billing-account-types')"
                            :class="activeMenu === 'config-billing-account-types' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-users-cog w-4 text-xs mr-2.5"></i>
                            <span>Account Types</span>
                        </a>
                        <a href="{{ route('config.charge-items.index') }}" @click="setActiveMenu('config-billing-charge-items')"
                            :class="activeMenu === 'config-billing-charge-items' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-file-invoice-dollar w-4 text-xs mr-2.5"></i>
                            <span>Application Fee Templates</span>
                        </a>
                    </div>
                </div>
                @endcan

                <!-- Access Control Submenu -->
                @can('config.access.manage')
                <div class="space-y-1">
                    <button @click="toggleSubmenu('accessControl')"
                        :class="(activeMenu.startsWith('config-access-') || openSubmenus.accessControl) ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt w-4 text-xs mr-2.5"></i>
                            <span>Access Control</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.accessControl }"></i>
                    </button>

                    <div x-show="openSubmenus.accessControl" x-collapse class="ml-6 space-y-1 mt-1">
                        <a href="{{ route('admin.roles.index') }}" @click="setActiveMenu('config-access-roles')"
                            :class="activeMenu === 'config-access-roles' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-user-shield w-4 text-xs mr-2.5"></i>
                            <span>Roles</span>
                        </a>
                        <a href="{{ route('admin.permissions.index') }}" @click="setActiveMenu('config-access-permissions')"
                            :class="activeMenu === 'config-access-permissions' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-key w-4 text-xs mr-2.5"></i>
                            <span>Permissions</span>
                        </a>
                        <a href="{{ route('admin.role-permissions.matrix') }}" @click="setActiveMenu('config-access-matrix')"
                            :class="activeMenu === 'config-access-matrix' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-th w-4 text-xs mr-2.5"></i>
                            <span>Permission Matrix</span>
                        </a>
                    </div>
                </div>
                @endcan

            </div>
        </div>
        @endcanany

        <!-- Activity Log - Super Admin Only -->
        @role('super_admin')
        <a href="{{ route('admin.activity-log') }}" @click="setActiveMenu('activity-log')"
            :class="activeMenu === 'activity-log' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'activity-log' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-history text-sm" :class="activeMenu === 'activity-log' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Activity Log</span>
        </a>
        @endrole

    </nav>

    <!-- Bottom Actions -->
    <div class="px-3 py-3 mt-auto border-t border-gray-200 dark:border-gray-800">
        <div :class="sidebarOpen ? 'flex items-center justify-between' : 'flex flex-col items-center space-y-2'">
            <a href="{{ url('/profile') }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Profile">
                <i class="fas fa-user text-gray-600 dark:text-gray-400"></i>
            </a>
            @can('settings.manage')
            <a href="{{ url('/settings') }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Settings">
                <i class="fas fa-cog text-gray-600 dark:text-gray-400"></i>
            </a>
            @endcan
            @can('reports.view')
            <a href="{{ url('/report') }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Report">
                <i class="fas fa-flag text-gray-600 dark:text-gray-400"></i>
            </a>
            @endcan
        </div>
    </div>
</aside>

<script>
    function sidebar() {
        return {
            sidebarOpen: true,
            activeMenu: '{{ session('active_menu', 'dashboard') }}',
            openSubmenus: {
                userManagement: {{ session('active_menu') && str_starts_with(session('active_menu'), 'user-') ? 'true' : 'false' }},
                customerManagement: {{ session('active_menu') && (str_starts_with(session('active_menu'), 'customer-')) ? 'true' : 'false' }},
                connectionManagement: {{ session('active_menu') && str_starts_with(session('active_menu'), 'connection-') ? 'true' : 'false' }},
                adminConfig: {{ session('active_menu') && str_starts_with(session('active_menu'), 'config-') ? 'true' : 'false' }},
                geographic: {{ session('active_menu') && str_starts_with(session('active_menu'), 'config-geographic-') ? 'true' : 'false' }},
                billingConfig: {{ session('active_menu') && str_starts_with(session('active_menu'), 'config-billing-') ? 'true' : 'false' }},
                accessControl: {{ session('active_menu') && str_starts_with(session('active_menu'), 'config-access-') ? 'true' : 'false' }}
            },

            init() {
                const savedSidebarState = localStorage.getItem('sidebarOpen');
                if (savedSidebarState !== null) {
                    this.sidebarOpen = savedSidebarState === 'true';
                }

                const path = window.location.pathname;
                const routeMap = {
                    '/dashboard': 'dashboard',
                    '/customer/list': 'customer-list',
                    '/customer/payment-management': 'payment-management',
                    '/customer/approve-customer': 'customer-approval',
                    '/customer/invoice-list': 'invoice-list',
                    '/customer/declined-customer': 'declined-customer',
                    '/connection/service-application/create': 'connection-new',
                    '/connection/service-application': 'connection-applications',
                    '/customer/service-connection': 'connection-active',
                    '/user/add': 'user-add',
                    '/user/list': 'user-list',
                    '/consumer/list': 'consumer-list',
                    '/billing/management': 'billing-management',
                    '/meter/management': 'meter-management',
                    '/rate/management': 'rate-management',
                    '/ledger/management': 'ledger-management',
                    '/analytics': 'analytics',
                    '/config/barangays': 'config-geographic-barangays',
                    '/config/areas': 'config-geographic-areas',
                    '/config/puroks': 'config-geographic-puroks',
                    '/config/water-rates': 'config-water-rates',
                    '/config/account-types': 'config-billing-account-types',
                    '/config/charge-items': 'config-billing-charge-items',
                    '/admin/roles': 'config-access-roles',
                    '/admin/permissions': 'config-access-permissions',
                    '/admin/role-permissions': 'config-access-matrix',
                    '/admin/activity-log': 'activity-log'
                };

                if (path.startsWith('/customer/payment/')) {
                    this.activeMenu = 'payment-management';
                } else {
                    this.activeMenu = routeMap[path] || '{{ session('active_menu', 'dashboard') }}';
                }

                localStorage.setItem('activeMenu', this.activeMenu);

                if (this.activeMenu.startsWith('user-')) {
                    this.openSubmenus.userManagement = true;
                } else if (this.activeMenu.startsWith('customer-')) {
                    this.openSubmenus.customerManagement = true;
                } else if (this.activeMenu.startsWith('connection-')) {
                    this.openSubmenus.connectionManagement = true;
                } else if (this.activeMenu.startsWith('config-')) {
                    this.openSubmenus.adminConfig = true;
                    if (this.activeMenu.startsWith('config-geographic-')) {
                        this.openSubmenus.geographic = true;
                    } else if (this.activeMenu.startsWith('config-billing-')) {
                        this.openSubmenus.billingConfig = true;
                    } else if (this.activeMenu.startsWith('config-access-')) {
                        this.openSubmenus.accessControl = true;
                    }
                }

                if (window.appState) {
                    window.appState.sidebarOpen = this.sidebarOpen;
                }

                if (window.innerWidth < 1024) {
                    this.sidebarOpen = false;
                }
            },

            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
                localStorage.setItem('sidebarOpen', this.sidebarOpen);
                if (window.appState) {
                    window.appState.sidebarOpen = this.sidebarOpen;
                }
            },

            toggleSubmenu(submenu) {
                if (!this.sidebarOpen) {
                    this.sidebarOpen = true;
                    return;
                }
                Object.keys(this.openSubmenus).forEach(key => {
                    if (key !== submenu) {
                        this.openSubmenus[key] = false;
                    }
                });
                this.openSubmenus[submenu] = !this.openSubmenus[submenu];
            },

            setActiveMenu(menu) {
                this.activeMenu = menu;
                localStorage.setItem('activeMenu', menu);
                // Close all submenus first
                this.openSubmenus.userManagement = false;
                this.openSubmenus.customerManagement = false;
                this.openSubmenus.connectionManagement = false;
                this.openSubmenus.adminConfig = false;
                this.openSubmenus.geographic = false;
                this.openSubmenus.billingConfig = false;
                this.openSubmenus.accessControl = false;
                // Open the appropriate submenu
                if (menu.startsWith('user-')) {
                    this.openSubmenus.userManagement = true;
                } else if (menu.startsWith('customer-')) {
                    this.openSubmenus.customerManagement = true;
                } else if (menu.startsWith('connection-')) {
                    this.openSubmenus.connectionManagement = true;
                } else if (menu.startsWith('config-')) {
                    this.openSubmenus.adminConfig = true;
                    if (menu.startsWith('config-geographic-')) {
                        this.openSubmenus.geographic = true;
                    } else if (menu.startsWith('config-billing-')) {
                        this.openSubmenus.billingConfig = true;
                    } else if (menu.startsWith('config-access-')) {
                        this.openSubmenus.accessControl = true;
                    }
                }
            },

            handleMenuClick(menu, event) {
                event.preventDefault();
                event.stopPropagation();
                this.setActiveMenu(menu);
                const routes = {
                    'payment-management': '{{ route("payment.management") }}',
                    'approve-customer': '{{ route("approve.customer") }}',
                    'service-connection': '{{ route("service.connection") }}'
                };
                if (routes[menu]) {
                    setTimeout(() => {
                        window.location.href = routes[menu];
                    }, 150);
                }
            }
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('appState', () => {
            return {
                sidebarOpen: true,
                isMobile: false,
                init() {
                    window.appState = this;
                    this.checkMobile();
                    window.addEventListener('resize', () => this.checkMobile());
                    const savedSidebarState = localStorage.getItem('sidebarOpen');
                    if (savedSidebarState !== null) {
                        this.sidebarOpen = savedSidebarState === 'true';
                    }
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                    }
                    this.initTheme();
                },
                checkMobile() {
                    this.isMobile = window.innerWidth < 1024;
                    if (this.isMobile && this.sidebarOpen) {
                        this.sidebarOpen = false;
                    }
                },
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                    localStorage.setItem('sidebarOpen', this.sidebarOpen);
                },
                closeSidebar() {
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                    }
                },
                initTheme() {
                    const savedTheme = localStorage.getItem('theme-preference');
                    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    let isDark;
                    if (savedTheme !== null) {
                        isDark = savedTheme === 'dark';
                    } else {
                        isDark = systemPrefersDark;
                    }
                    if (isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            }
        });
    });

    document.addEventListener('click', (e) => {
        if (window.appState && window.appState.isMobile && !e.target.closest('aside') && !e.target.closest('[data-sidebar-toggle]')) {
            window.appState.closeSidebar();
        }
    });
</script>
